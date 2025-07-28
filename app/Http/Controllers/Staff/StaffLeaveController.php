<?php

namespace App\Http\Controllers\Staff;

use App\Enum\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffLeaveRequest;
use Inertia\Inertia;
use App\Models\Leave;

class StaffLeaveController extends Controller
{
    public function create()
    {
        $user = auth()->user(); // Oturum açmış kullanıcı
        $unit = $user->unit->name ?? 'Belirtilmemiş'; // Kullanıcının birimi

        return Inertia::render('Staff/Leave/Create', [
            'user' => $user->only('first_name', 'last_name', 'email', 'identity_number', 'birth_date', 'gender', 'phone', 'address', 'position', 'photo'),
            'unit' => $unit,
        ]);
    }

    public function show(Leave $leave)
    {
        if ($leave->user_id !== auth()->id()) {
            abort(403);
        }

        $user = auth()->user();
        $unit = $user->unit->name ?? 'Belirtilmemiş';

        return Inertia::render('Staff/Leave/Show', [
            'user' => $user->only('first_name', 'last_name', 'email', 'identity_number', 'birth_date', 'gender', 'phone', 'address', 'position', 'photo'),
            'unit' => $unit,
            'leave' => $leave,
        ]);
    }

    public function edit(Leave $leave)
    {
        if (($leave->status !== LeaveStatus::PENDING) || ($leave->user_id !== auth()->id())) {
            abort(403);
        }

        $user = auth()->user();
        $unit = $user->unit->name ?? 'Belirtilmemiş';

        return Inertia::render('Staff/Leave/Edit', [
            'user' => $user->only('first_name', 'last_name', 'email', 'identity_number', 'birth_date', 'gender', 'phone', 'address', 'position', 'photo'),
            'unit' => $unit,
            'leave' => $leave,
        ]);
    }

    public function index()
    {
        $user = auth()->user();
        $unit = $user->unit->name ?? 'Belirtilmemiş';

        // Paginate the user's leaves, e.g., 10 items per page
        $leaves = $user->leaves()
            ->orderBy('created_at', 'desc')
            ->paginate(10) // <-- Added pagination here
            ->withQueryString(); // Keep query string for filters/sort if any

        return Inertia::render('Staff/Leave/Index', [
            'user' => $user->only('first_name', 'last_name', 'email', 'identity_number', 'birth_date', 'gender', 'phone', 'address', 'position', 'photo'),
            'unit' => $unit,
            'leaves' => $leaves, // Laravel's paginator returns a special object
        ]);
    }

    public function store(StaffLeaveRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        Leave::create($validated);

        return redirect()->route('leaves.index')
            ->with('success', 'İzin talebiniz başarıyla oluşturuldu.');
    }

    public function update(StaffLeaveRequest $request, Leave $leave)
    {
        if ($leave->user_id !== auth()->id()) {
            abort(403);
        }

        if ($leave->status !== LeaveStatus::PENDING) {
            return redirect()->route('leaves.show', $leave->id)
                ->with('error', 'Onaylanmış veya reddedilmiş izin talepleri güncellenemez.');
        }

        $leave->update($request->validated());

        return redirect()->route('leaves.index')
            ->with('success', 'İzin talebiniz başarıyla güncellendi.');
    }

    public function destroy(Leave $leave)
    {
        // Yetkilendirme: Sadece kendi izin talebini iptal edebilir
        if ($leave->user_id !== auth()->id()) {
            abort(403, 'Bu izin talebini iptal etme yetkiniz yok.');
        }

        // Sadece bekleyen izin talepleri iptal edilebilir
        if ($leave->status->value !== LeaveStatus::PENDING->value) {
            return redirect()->route('leaves.index')->with('error', 'Sadece bekleyen izin talepleri iptal edilebilir.');
        }

        $leave->delete(); // İzin kaydını sil

        return redirect()->route('leaves.index')->with('success', 'İzin talebiniz başarıyla iptal edildi.');
    }
}
