<?php

namespace App\Enums;

enum MessageStatusEnum: string
{
    case Pending = 'pending';
    case Unread = 'unread';
    case Read = 'read';
    case Failed = 'failed';
}
