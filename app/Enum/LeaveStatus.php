<?php

namespace App\Enum;

enum LeaveStatus: string
{
    case PENDING = 'Beklemede';
    case APPROVED = 'Onaylandı';
    case REJECTED = 'Reddedildi';
}
