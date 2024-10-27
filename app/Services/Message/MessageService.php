<?php

namespace App\Services\Message;

use App\Enums\MessageStatusEnum;
use App\Jobs\DecrementUnreadCounterJob;
use App\Jobs\IncrementUnreadCounterJob;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Services\Dialog\DialogService;
use Illuminate\Support\Facades\DB;

readonly class MessageService
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private DialogService $dialogService
    ) {
        //
    }

    public function getMessages(int $authUserId, int $dialogPartnerId, int $offset, int $limit): array
    {

        $dialogWithUser = $this->dialogService->getDialogIdWithUser($authUserId, $dialogPartnerId);

        return $dialogWithUser ? $this->messageRepository->getMessagesFromDialog($dialogWithUser, $offset, $limit) : [];
    }

    public function createMessage(int $authUserId, int $dialogPartnerId, string $text): void
    {
        DB::transaction(function () use ($authUserId, $dialogPartnerId, $text) {
            $dialogWithUser = $this->dialogService->getOrCreateDialogIdWithUser($authUserId, $dialogPartnerId);

            $messageId = $this->messageRepository->createMessage($authUserId, $dialogWithUser['id'], $text);

            IncrementUnreadCounterJob::dispatch($messageId, $dialogPartnerId);
        });
    }

    public function updateMessageStatus(int $messageId, string $status): bool
    {
        return $this->messageRepository->updateStatus($messageId, $status);
    }

    public function markMessageAsRead(int $authUserId, int $messageId): bool
    {
        $message = $this->messageRepository->findMessageById($messageId);

        if ($message && $message->status === MessageStatusEnum::Unread->value) {
            DB::transaction(function () use ($messageId, $authUserId) {
                $this->messageRepository->updateStatus($messageId, MessageStatusEnum::Read->value);

                DecrementUnreadCounterJob::dispatch($authUserId, $messageId);
            });
        }

        return true;
    }

    public function countUnreadMessagesForUser(int $userId): int
    {
        return $this->messageRepository->countUnreadMessagesForUser($userId);
    }
}
