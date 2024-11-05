<?php

namespace App\Enums;

enum WonGiftStatusEnum: string
{
    case pending = 'pending';
    case underway = 'underway';
    case used = 'used';
}
