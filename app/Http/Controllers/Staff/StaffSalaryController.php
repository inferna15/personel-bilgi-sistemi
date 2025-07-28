<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffSalaryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $unit = $user->unit->name ?? 'Belirtilmemiş';

        $salaries = $user->salaries()
            ->orderBy('pay_date', 'desc')
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Staff/Salary/Index', [
            'user' => $user->only('first_name', 'last_name', 'email', 'identity_number', 'birth_date', 'gender', 'phone', 'address', 'position', 'photo'),
            'unit' => $unit,
            'salaries' => $salaries,
        ]);
    }
}
