<?php

declare(strict_types=1);

namespace App;

/**
 * класс для работы с авторизацией.
 * Логика: в сессии храним только user_id.
 */
class Auth
{
    public static function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function login(int $userId): void
    {
        self::startSession();
        $_SESSION['user_id'] = $userId;
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        session_destroy();
    }

    public static function userId(): ?int
    {
        self::startSession();

        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }
}
