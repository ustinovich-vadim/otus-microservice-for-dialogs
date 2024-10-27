<?php

namespace App\Repositories\Message;

use App\Enums\MessageStatusEnum;
use Illuminate\Support\Facades\DB;

class MessageRepository implements MessageRepositoryInterface
{
    public function getMessagesFromDialog(int $dialogWithUserId, int $offset, int $limit): array
    {
        return DB::table('messages')
            ->where('dialog_id', $dialogWithUserId)
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function createMessage(int $authUserId, int $dialogWithUserId, string $text): int
    {
        return DB::table('messages')->insertGetId([
            'dialog_id' => $dialogWithUserId,
            'sender_id' => $authUserId,
            'text' => $text,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updateStatus(int $messageId, string $status): bool
    {
        return DB::table('messages')
                ->where('id', $messageId)
                ->update(['status' => $status, 'updated_at' => now()]) > 0;
    }

    public function findMessageById(int $messageId): ?object
    {
        return DB::table('messages')->where('id', $messageId)->first();
    }

    public function countUnreadMessagesForUser(int $userId): int
    {
        return DB::table('dialogs')
            ->join('messages', 'dialogs.id', '=', 'messages.dialog_id')
            ->where(function ($query) use ($userId) {
                $query->where('dialogs.user_id_1', $userId)
                    ->orWhere('dialogs.user_id_2', $userId);
            })
            ->where('messages.sender_id', '!=', $userId)
            ->where('messages.status', MessageStatusEnum::Unread->value)
            ->count();
    }
}
