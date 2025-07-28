<?php

namespace App\Http\Controllers\Management;

use App\Enum\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Yetkilendirme kontrolü
        // $this->authorize('view', 'dashboard');

        // 1. Toplam Personel Sayısı
        $totalStaffCount = User::count();

        // 2. Toplam Birim Sayısı
        $totalUnitCount = Unit::count();

        // 3. Birimlerdeki Personel Sayısına Göre Pasta Dilimi Çizelgesi Verisi
        $unitsWithStaffCount = Unit::withCount('users')->get();
        $unitStaffDistribution = $unitsWithStaffCount->map(function ($unit) {
            return [
                'name' => $unit->name,
                'value' => $unit->users_count,
            ];
        });

        // 4. Bugün İzinli Olanlar
        $today = Carbon::today();
        $staffOnLeaveToday = Leave::where(function ($query) use ($today) {
            $query->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today);
        })
            ->where('status', LeaveStatus::APPROVED->value ?? 'Onaylandı')
            ->with('user')
            ->get();

        // 5. Onay Bekleyen İzin Talepleri Sayısı
        $pendingLeaveRequestsCount = Leave::where('status', LeaveStatus::PENDING->value ?? 'Beklemede')->count();

        // 6. Son Eklenen Personel
        $recentlyHiredStaff = User::latest()->take(5)->get(['id', 'first_name', 'last_name', 'email', 'created_at']);

        // 7. Yaklaşan Doğum Günleri (SQLite uyumlu)
        $currentMonth = $today->format('m');
        $currentDay = $today->format('d');

        $upcomingBirthdays = User::where(function ($query) use ($currentMonth, $currentDay) {
            // Mevcut ayda, bugünden sonraki günler
            $query->where(DB::raw("strftime('%m', birth_date)"), '=', $currentMonth)
                ->where(DB::raw("strftime('%d', birth_date)"), '>=', $currentDay);
        })
            ->orWhere(function ($query) use ($currentMonth) {
                // Mevcut aydan sonraki tüm aylar
                $query->where(DB::raw("strftime('%m', birth_date)"), '>', $currentMonth);
            })
            // SQLite'da ORDER BY için strftime kullanıyoruz
            ->orderByRaw("strftime('%m', birth_date), strftime('%d', birth_date)") // <-- Burası güncellendi
            ->take(5)
            ->get(['id', 'first_name', 'last_name', 'birth_date']);


        return Inertia::render('Dashboard', [
            'totalStaffCount' => $totalStaffCount,
            'totalUnitCount' => $totalUnitCount,
            'unitStaffDistribution' => $unitStaffDistribution,
            'staffOnLeaveToday' => $staffOnLeaveToday,
            'pendingLeaveRequestsCount' => $pendingLeaveRequestsCount,
            'recentlyHiredStaff' => $recentlyHiredStaff,
            'upcomingBirthdays' => $upcomingBirthdays,
        ]);
    }
}
