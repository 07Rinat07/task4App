<?php

declare(strict_types=1);

use App\Auth;
use App\Database;
use App\Flash;
use App\UserService;

require __DIR__ . '/../src/config.php';
require __DIR__ . '/../vendor/autoload.php';

Auth::startSession();

$pdo         = Database::getConnection();
$userService = new UserService($pdo);

$page = $_GET['page'] ?? 'home';

/**
 * Редирект внутрь приложения.
 */
function redirectTo(string $page): void
{
    $url = BASE_URL . '/index.php?page=' . urlencode($page);
    header('Location: ' . $url);
    exit;
}

/**
 * Гард для страниц, куда пускаем только залогиненных незаблокированных пользователей.
 *
 * @return array<string, mixed> текущий пользователь
 */
function requireAuthenticatedUser(UserService $userService): array
{
    $user = $userService->getCurrentUser();
    if ($user === null) {
        Flash::add('warning', 'Please log in to continue.');
        redirectTo('login');
    }

    return $user;
}

$currentUser = null;

switch ($page) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                $user = $userService->login($email, $password);
                Auth::login((int) $user['id']);
                Flash::add('success', 'Login successful.');
                redirectTo('users');
            } catch (\Throwable $e) {
                Flash::add('danger', $e->getMessage());
            }
        }

        include __DIR__ . '/../views/login.php';
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name     = $_POST['name'] ?? '';
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                $userId = $userService->register($name, $email, $password);
                Flash::add(
                    'success',
                    'Registration successful. Please check your e-mail for confirmation link.'
                );
                redirectTo('login');
            } catch (\Throwable $e) {
                Flash::add('danger', $e->getMessage());
            }
        }

        include __DIR__ . '/../views/register.php';
        break;

    case 'logout':
        Auth::logout();
        Flash::add('success', 'You have been logged out.');
        redirectTo('login');
        break;

    case 'verify_email':
        $token = $_GET['token'] ?? '';

        $user = $userService->verifyEmail($token);

        if ($user === null) {
            Flash::add('danger', 'Verification link is invalid or expired.');
        } else {
            if ($user['status'] === 'active') {
                Flash::add('success', 'E-mail has been confirmed.');
            } else {
                Flash::add('info', 'E-mail has been confirmed, but your account is blocked.');
            }
        }

        redirectTo('login');
        break;

    case 'users':
        $currentUser = requireAuthenticatedUser($userService);
        $users       = $userService->listUsersForTable();
        include __DIR__ . '/../views/users.php';
        break;

    case 'bulk_action':
        $currentUser = requireAuthenticatedUser($userService);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $ids    = $_POST['selected'] ?? [];

            if (!is_array($ids)) {
                $ids = [];
            }

            try {
                $userService->applyBulkAction($action, $ids);

                // Если текущий пользователь сам себя заблокировал/удалил — выкидываем на логин.
                $selectedIds = array_map('intval', $ids);
                if (in_array((int) $currentUser['id'], $selectedIds, true)) {
                    Auth::logout();
                    Flash::add(
                        'info',
                        'Your account has been changed. Please log in again if possible.'
                    );
                    redirectTo('login');
                }

                Flash::add('success', 'Action has been applied.');
            } catch (\Throwable $e) {
                Flash::add('danger', $e->getMessage());
            }
        }

        redirectTo('users');
        break;

    case 'home':
    default:
        $currentUser = $userService->getCurrentUser();
        if ($currentUser === null) {
            redirectTo('login');
        } else {
            redirectTo('users');
        }
        break;
}
