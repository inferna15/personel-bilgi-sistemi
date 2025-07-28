<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Leave;
use App\Models\Salary;
use App\Models\Unit;
use App\Models\User;
use App\Services\HttpService;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index()
    {
        $user = User::select([
            'first_name',
            'last_name',
            'email',
            'identity_number',
            'birth_date',
            'gender',
            'phone',
            'address',
            'position',
            'photo',
            'unit_id',
        ])->find(auth()->id());

        return Inertia::render('Staff/Index', [
            'user' => $user,
            'unit' => $user->unit_id ? Unit::find($user->unit_id)?->name : null,
            'leaves' => Leave::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get(),
            'cards' => HttpService::getByUserId('cards', auth()->id())->json(),
            'cars' => HttpService::getByUserId('cars', auth()->id())->json(),
            'announcements' => Announcement::latest()->limit(3)->get(),
            'salaries' => Salary::where('user_id', auth()->id())
                ->orderBy('pay_date', 'desc')
                ->limit(3)
                ->get(),
        ]);
    }
}
