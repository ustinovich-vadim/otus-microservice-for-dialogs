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

class IncrementUnreadCounterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $messageId;
    private int $recipientId;

    public function __construct($messageId, $recipientId)
    {
        $this->messageId = $messageId;
        $this->recipientId = $recipientId;
    }

    /**
     * @throws ConnectionException
     */
    public function handle(MessageService $messageService, CounterService $counterService): void
    {
        $incremented = $counterService->incrementUnreadCounter($this->recipientId);

        $status = $incremented ? MessageStatusEnum::Unread->value : MessageStatusEnum::Failed->value;
        $messageService->updateMessageStatus($this->messageId, $status);
    }
}
