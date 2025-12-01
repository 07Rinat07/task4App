<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use PDO;
use RuntimeException;

/**
 * Основная бизнес-логика вокруг пользователей:
 * регистрация, логин, подтверждение почты, bulk-операции.
 */
class UserService
{
    private UserRepository $users;
    private MailQueue $mailQueue;

    public function __construct(PDO $pdo)
    {
        $this->users     = new UserRepository($pdo);
        $this->mailQueue = new MailQueue($pdo);
    }

    /**
     * Регистрация нового пользователя.
     *
     * @throws InvalidArgumentException|RuntimeException
     */
    public function register(string $name, string $email, string $password): int
    {
        $name  = trim($name);
        $email = trim($email);

        if ($name === '' || $email === '' || $password === '') {
            throw new InvalidArgumentException('Name, e-mail and password are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('E-mail address is not valid.');
        }

        $passwordHash      = password_hash($password, PASSWORD_DEFAULT);
        $verificationToken = bin2hex(random_bytes(32));

        try {
            $userId = $this->users->create($name, $email, $passwordHash, $verificationToken);
        } catch (\PDOException $e) {
            // 23000 — нарушение уникального ограничения (UNIQUE INDEX).
            if ((int) $e->getCode() === 23000) {
                throw new RuntimeException('User with this e-mail already exists.');
            }

            throw $e;
        }

        $verifyUrl = BASE_URL . '/index.php?page=verify_email&token=' . urlencode($verificationToken);

        $subject = 'Please confirm your e-mail';
        $body    = "Hello {$name},\n\n"
            . "Thank you for registering.\n"
            . "To confirm your e-mail, please click the link below:\n"
            . $verifyUrl . "\n\n"
            . "If you did not register on this site, please ignore this message.\n";

        $this->mailQueue->enqueue($email, $subject, $body);

        return $userId;
    }

    /**
     * Логин по e-mail и паролю.
     *
     * @return array<string, mixed>
     */
    public function login(string $email, string $password): array
    {
        $email = trim($email);

        if ($email === '' || $password === '') {
            throw new InvalidArgumentException('E-mail and password are required.');
        }

        $user = $this->users->findByEmail($email);

        if ($user === null) {
            throw new RuntimeException('Invalid e-mail or password.');
        }

        if (!password_verify($password, $user['password_hash'])) {
            throw new RuntimeException('Invalid e-mail or password.');
        }

        if ($user['status'] === 'blocked') {
            throw new RuntimeException('Your account is blocked.');
        }

        $this->users->updateLastLogin((int) $user['id']);

        return $this->users->findById((int) $user['id']);
    }

    /**
     * Подтверждение e-mail по токену.
     *
     * @return array<string, mixed>|null
     */
    public function verifyEmail(string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        return $this->users->markEmailVerified($token);
    }

    /**
     * Получить текущего пользователя:
     *  - если не залогинен — null;
     *  - если нет в БД или заблокирован — логаут и null.
     *
     * @return array<string, mixed>|null
     */
    public function getCurrentUser(): ?array
    {
        $userId = Auth::userId();
        if ($userId === null) {
            return null;
        }

        $user = $this->users->findById($userId);
        if ($user === null) {
            Auth::logout();
            return null;
        }

        if ($user['status'] === 'blocked') {
            Auth::logout();
            return null;
        }

        $this->users->updateLastActivity($userId);

        return $user;
    }

    /**
     * Пользователи для таблицы.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listUsersForTable(): array
    {
        return $this->users->findAllForTable();
    }

    /**
     * Применение bulk-действий (block/unblock/delete/delete_unverified).
     *
     * @param int[] $ids
     */
    public function applyBulkAction(string $action, array $ids): void
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if (!$ids) {
            return;
        }

        switch ($action) {
            case 'block':
                $this->users->updateStatus($ids, 'blocked');
                break;
            case 'unblock':
                $this->users->updateStatus($ids, 'active');
                break;
            case 'delete':
                $this->users->deleteByIds($ids);
                break;
            case 'delete_unverified':
                $this->users->deleteUnverifiedByIds($ids);
                break;
            default:
                throw new InvalidArgumentException('Unknown bulk action.');
        }
    }
}
