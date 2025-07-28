<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view units');
    }

    public function view(User $user, Unit $unit): bool
    {
        return $user->can('view units');
    }

    public function create(User $user): bool
    {
        return $user->can('create units');
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->can('update units');
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->can('delete units');
    }

    public function restore(User $user, Unit $unit): bool
    {
        return false;
    }

    public function forceDelete(User $user, Unit $unit): bool
    {
        return false;
    }
}
