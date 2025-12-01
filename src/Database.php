<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

/**
 * Простейший синглтон вокруг PDO.
 * Один раз создаём соединение и переиспользуем в приложении.
 */
class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            try {
                self::$connection = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASSWORD,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                exit('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
