<?php

namespace App\Enums;

enum FlashTypeEnum: string
{
    case danger = 'danger';
    case info = 'info';
    case success = 'success';
    case warning = 'warning';
}
