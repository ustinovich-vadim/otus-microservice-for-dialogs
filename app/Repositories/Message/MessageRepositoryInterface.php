<?php

namespace App\Repositories\Message;

interface MessageRepositoryInterface
{
    public function getMessagesFromDialog(int $dialogWithUserId, int $offset, int $limit): array;

    public function createMessage(int $authUserId, int $dialogWithUserId, string $text): int;

    public function updateStatus(int $messageId, string $status): bool;

    public function findMessageById(int $messageId): ?object;

    public function countUnreadMessagesForUser(int $userId): int;
}
