<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view announcements');
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return $user->can('view announcements');
    }

    public function create(User $user): bool
    {
        return $user->can('create announcements');
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->can('update announcements');
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->can('delete announcements');
    }

    public function restore(User $user, Announcement $announcement): bool
    {
    }

    public function forceDelete(User $user, Announcement $announcement): bool
    {
    }
}
