<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\GetMessagesRequest;
use App\Http\Requests\Message\MarkAsReadMessageRequest;
use App\Http\Requests\Message\SyncCountOfMessagesRequest;
use App\Jobs\SyncUnreadMessagesCountJob;
use App\Services\Auth\AuthenticatedUser;
use App\Services\Message\MessageService;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function __construct(private readonly MessageService $messageService)
    {
        //
    }

    public function index(GetMessagesRequest $request): Response
    {
        $authUserId = AuthenticatedUser::getId();
        $dialogPartnerId = $request->route('user_id');
        $offset = $request->integer('offset', 0);
        $limit = $request->integer('limit', 100);

        $messages = $this->messageService->getMessages(
            authUserId: $authUserId,
            dialogPartnerId: $dialogPartnerId,
            offset: $offset,
            limit: $limit
        );

        return response()->json($messages, Response::HTTP_OK);
    }

    public function create(CreateMessageRequest $request): Response
    {
        $authUserId = AuthenticatedUser::getId();
        $dialogPartnerId = $request->route('user_id');
        $text = $request->string('text');

        try {
            $this->messageService->createMessage(
                authUserId: $authUserId,
                dialogPartnerId: $dialogPartnerId,
                text: $text
            );

            return response()->json('Message created successfully', Response::HTTP_CREATED);
        } catch (Exception $e) {
            SyncUnreadMessagesCountJob::dispatch($dialogPartnerId);

            return response()->json('Failed to create message', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function markAsRead(MarkAsReadMessageRequest $request): Response
    {
        $authUserId = AuthenticatedUser::getId();
        $messageId = $request->integer('message_id');

        try {
            $this->messageService->markMessageAsRead(
                $authUserId,
                $messageId
            );
            return response()->json('Messages marked as read', Response::HTTP_OK);
        } catch (Exception $e) {
            SyncUnreadMessagesCountJob::dispatch($authUserId);

            return response()->json('Failed to mark messages as read', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sync(SyncCountOfMessagesRequest $request): Response
    {
        SyncUnreadMessagesCountJob::dispatch($request->route('user_id'));

        return response()->json('Sync job dispatched successfully', Response::HTTP_OK);
    }
}
