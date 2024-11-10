<?php

namespace App\Enums;

enum WonGiftStatusEnum: string
{
    case pending = 'pending';
    case sold = 'sold';
    case used = 'used';
}
