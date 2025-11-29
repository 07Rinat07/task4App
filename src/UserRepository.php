<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use PDO;

/**
 * Класс только для SQL по таблице users.
 */

class UserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(string $name, string $email, string $passwordHash, string $verificationToken): int
    {
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password_hash, status, verification_token, created_at)
             VALUES (:name, :email, :password_hash, :status, :verification_token, :created_at)'
        );

        $stmt->execute([
            'name'               => $name,
            'email'              => $email,
            'password_hash'      => $passwordHash,
            'status'             => 'unverified',
            'verification_token' => $verificationToken,
            'created_at'         => $now,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateLastLogin(int $userId): void
    {
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'UPDATE users SET last_login_at = :time, last_activity_at = :time WHERE id = :id'
        );
        $stmt->execute([
            'time' => $now,
            'id'   => $userId,
        ]);
    }

    public function updateLastActivity(int $userId): void
    {
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'UPDATE users SET last_activity_at = :time WHERE id = :id'
        );
        $stmt->execute([
            'time' => $now,
            'id'   => $userId,
        ]);
    }

    public function markEmailVerified(string $token): ?array
    {
        // Ищем пользователя по токену подтверждения
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE verification_token = :token');
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();

        if ($user === false) {
            return null;
        }

        // Если был заблокирован, оставляем blocked, иначе -> active

        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET status = CASE WHEN status = "blocked" THEN status ELSE "active" END,
                 verification_token = NULL
             WHERE id = :id'
        );
        $stmt->execute(['id' => $user['id']]);

        return $this->findById((int) $user['id']);
    }

    /**
     * Список пользователей для таблицы, отсортированный по последнему логину.
     *
     * @return array<int, array<string, mixed>>
     */

    public function findAllForTable(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, name, email, status, created_at, last_login_at
             FROM users
             ORDER BY COALESCE(last_login_at, created_at) DESC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Массовое изменение статуса.
     *
     * @param int[] $ids
     */

    public function updateStatus(array $ids, string $status): void
    {
        if (!$ids) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql          = "UPDATE users SET status = ? WHERE id IN ($placeholders)";
        $params       = array_merge([$status], $ids);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Массовое удаление пользователей.
     *
     * @param int[] $ids
     */

    public function deleteByIds(array $ids): void
    {
        if (!$ids) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql          = "DELETE FROM users WHERE id IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
    }

    /**
     * Удаляет только тех из выбранных, у кого статус unverified.
     *
     * @param int[] $ids
     */

    public function deleteUnverifiedByIds(array $ids): void
    {
        if (!$ids) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql          = "DELETE FROM users WHERE id IN ($placeholders) AND status = 'unverified'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
    }
}
