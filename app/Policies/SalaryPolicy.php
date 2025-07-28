<?php

namespace App\Policies;

use App\Models\Salary;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalaryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view salaries');
    }

    public function view(User $user, Salary $salary): bool
    {
        return $user->can('view salaries');
    }

    public function create(User $user): bool
    {
        return $user->can('create salaries');
    }

    public function update(User $user, Salary $salary): bool
    {
        return $user->can('update salaries');
    }

    public function delete(User $user, Salary $salary): bool
    {
        return $user->can('delete salaries');
    }

    public function restore(User $user, Salary $salary): bool
    {
        return false;
    }

    public function forceDelete(User $user, Salary $salary): bool
    {
        return false;
    }
}
