<?php
declare(strict_types=1);

/**
 * настройки проекта.
 * В реальном проекте это лучше брать из .env
 */

const DB_HOST = 'localhost';
const DB_NAME = 'database';
const DB_USER = 'root';
const DB_PASSWORD = '';
const DB_CHARSET = 'utf8mb4';

const BASE_URL = 'http://localhost:8000';

/**
 * От кого отправляем письма.
 */
const MAIL_FROM = 'no-reply@example.com';
const MAIL_FROM_NAME = 'Task4 Demo App';
