<?php

declare(strict_types=1);

namespace App;

/**
 * Флеш-сообщения: сохраняем в сессию массив сообщений
 * и один раз выводим, затем очищаем.
 */


class Flash
{
    private const SESSION_KEY = 'flash_messages';

    public static function add(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION[self::SESSION_KEY][] = [
            'type'    => $type,    // success / danger / warning / info (классы Bootstrap)
            'message' => $message,
        ];
    }

    /**
     * Забираем и очищаем флеши.
     *
     * @return array<int, array{type: string, message: string}>
     */
    public static function consume(): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);

        return $messages;
    }
}
