<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Announcement; // Announcement modelinizin olduğunu varsayalım
use App\Models\User;
use App\Models\Unit;

class StaffAnnouncementController extends Controller
{
    public function show(Announcement $announcement)
    {
        $user = auth()->user();
        $unit = $user->unit->name ?? 'Belirtilmemiş';

        return Inertia::render('Staff/Announcement/Show', [
            'user' => $user->only('first_name', 'last_name', 'email', 'identity_number', 'birth_date', 'gender', 'phone', 'address', 'position', 'photo'),
            'unit' => $unit,
            'announcement' => $announcement,
        ]);
    }

    public function index()
    {
        $user = auth()->user();
        $unit = $user->unit->name ?? 'Belirtilmemiş';

        // Paginate all announcements, e.g., 5 items per page
        $announcements = Announcement::orderBy('date', 'desc')
            ->paginate(10) // <-- Added pagination here
            ->withQueryString(); // Keep query string for filters/sort if any

        return Inertia::render('Staff/Announcement/Index', [
            'user' => $user->only('first_name', 'last_name', 'email', 'identity_number', 'birth_date', 'gender', 'phone', 'address', 'position', 'photo'),
            'unit' => $unit,
            'announcements' => $announcements, // Laravel's paginator returns a special object
        ]);
    }
}
