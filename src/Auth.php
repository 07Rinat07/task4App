<?php

declare(strict_types=1);

namespace App;

/**
 * Класс для работы с авторизацией.
 * В сессии храним только идентификатор пользователя.
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

        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }
}
