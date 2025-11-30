<?php

declare(strict_types=1);

/**
 *    скрипт запускается из консоли:
 *    php public/send_queued_mail.php
 *
 * Он забирает письма из mail_queue и отправляет их через mail().
 * В реальном проекте здесь может быть PHPMailer или другая библиотека.
 */

use App\Database;
use App\MailQueue;

require __DIR__ . '/../src/config.php';
require __DIR__ . '/../vendor/autoload.php';

$pdo       = Database::getConnection();
$mailQueue = new MailQueue($pdo);

$batch = $mailQueue->fetchPending(20);

foreach ($batch as $row) {
    $headers   = sprintf("From: %s\r\n", MAIL_FROM);
    $recipient = $row['recipient'];
    $subject   = $row['subject'];
    $body      = $row['body'];

    $sent = mail($recipient, $subject, $body, $headers);

    if ($sent) {
        $mailQueue->markSent((int) $row['id']);
    } else {
        $mailQueue->markFailed((int) $row['id'], 'mail() returned false');
    }
}

echo 'Processed ' . count($batch) . " messages.\n";
