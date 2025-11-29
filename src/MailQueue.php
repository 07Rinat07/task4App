<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use PDO;

/**
 * Очередь писем: HTTP-запрос только кладёт запись в mail_queue,
 * а отдельный CLI-скрипт их отправляет.
 */

class MailQueue
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function enqueue(string $recipient, string $subject, string $body): void
    {
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO mail_queue (recipient, subject, body, created_at)
             VALUES (:recipient, :subject, :body, :created_at)'
        );

        $stmt->execute([
            'recipient'  => $recipient,
            'subject'    => $subject,
            'body'       => $body,
            'created_at' => $now,
        ]);
    }

    /**
     * Берём пачку ожидающих писем.
     *
     * @return array<int, array<string, mixed>>
     */

    public function fetchPending(int $limit = 20): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM mail_queue WHERE status = 'pending' ORDER BY id ASC LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function markSent(int $id): void
    {
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            "UPDATE mail_queue SET status = 'sent', sent_at = :sent_at, last_error = NULL WHERE id = :id"
        );
        $stmt->execute([
            'sent_at' => $now,
            'id'      => $id,
        ]);
    }

    public function markFailed(int $id, string $error): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE mail_queue SET status = 'failed', last_error = :error WHERE id = :id"
        );
        $stmt->execute([
            'error' => $error,
            'id'    => $id,
        ]);
    }
}
