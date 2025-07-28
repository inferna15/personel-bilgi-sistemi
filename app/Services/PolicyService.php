<?php

namespace App\Services;

use App\Models\User;

class PolicyService
{
    public static function isStaff(User $user)
    {
        return $user->getRoleNames()->first() === 'staff';
    }
}
