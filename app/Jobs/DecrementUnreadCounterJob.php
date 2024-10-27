<?php

namespace App\Jobs;

use App\Enums\MessageStatusEnum;
use App\Services\CounterService\CounterService;
use App\Services\Message\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DecrementUnreadCounterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $userId;
    private int $messageId;

    public function __construct(int $userId, int $messageId)
    {
        $this->userId = $userId;
        $this->messageId = $messageId;
    }

    /**
     * @throws ConnectionException
     */
    public function handle(CounterService $counterService, MessageService $messageService): void
    {
        $response = $counterService->decrementUnreadCounter($this->userId);

        if (!$response) {
            $messageService->updateMessageStatus($this->messageId, MessageStatusEnum::Unread->value);
        }
    }
}
