<?php

namespace App\Jobs;

use App\Services\CounterService\CounterService;
use App\Services\Message\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncUnreadMessagesCountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @throws ConnectionException
     */
    public function handle(CounterService $counterService, MessageService $messageService): void
    {
        $unreadCount = $messageService->countUnreadMessagesForUser($this->userId);

        $response = $counterService->setUnreadCounter($this->userId, $unreadCount);

        if (!$response) {
            Log::error("Failed to sync unread messages count for user {$this->userId}. Retrying...");
            $this->release(30);
        }
    }
}
