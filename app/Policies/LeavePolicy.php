<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeavePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view leaves');
    }

    public function view(User $user, Leave $leave): bool
    {
        return $user->can('view leaves');
    }

    public function create(User $user): bool
    {
        return $user->can('create leaves');
    }

    public function update(User $user, Leave $leave): bool
    {
        return $user->can('update leaves');
    }

    public function delete(User $user, Leave $leave): bool
    {
        return $user->can('delete leaves');
    }

    public function restore(User $user, Leave $leave): bool
    {
        return false;
    }

    public function forceDelete(User $user, Leave $leave): bool
    {
        return false;
    }
}
