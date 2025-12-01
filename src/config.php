<?php

declare(strict_types=1);

/**
 * Базовые настройки приложения.
 *
 * На локалке/хостинге меняются только значения констант.
 * В коде проект больше никак не зависит от окружения.
 */

/**
 * Параметры подключения к БД.
 * На сервере их выдают в панели хостинга (имя БД, пользователь, пароль, хост).
 */
const DB_HOST    = 'MySQL-8.0';     // или 127.0.0.1
const DB_NAME    = 'task4_app';     // имя базы данных
const DB_USER    = 'root';    // 'task4_user' пользователь БД
const DB_PASSWORD = '';     // пароль пользователя БД
const DB_CHARSET = 'utf8mb4';

/**
 * BASE_URL — публичный URL до папки public.
 * Нужен для формирования ссылок в письмах.
 *
 * Примеры:
 *   локально: 'http://localhost:8000'
 *   на хостинге: 'https://example.com'
 */
const BASE_URL = 'http://localhost:8000';

/**
 * Настройки письма-отправителя.
 */
const MAIL_FROM      = 'no-reply@example.com';
const MAIL_FROM_NAME = 'Task4 App';
