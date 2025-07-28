<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Services\PolicyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

// PolicyService'i projenizde tanımladığınızdan emin olun

class AnnouncementController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Announcement::class);

        $search = $request->input('search');
        $perPage = 10;

        $query = Announcement::query()
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });

        $announcements = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Management/Announcements/Index', [
            'announcements' => $announcements,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Announcement::class);

        return Inertia::render('Management/Announcements/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Announcement::class);
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'content' => ['required', 'string'],
        ]);

        Announcement::create($request->all());
        return redirect()->route('management.announcements.index')->with('success', 'Duyuru başarıyla oluşturuldu.');
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);
        return Inertia::render('Management/Announcements/Show', [
            'announcement' => $announcement,
        ]);
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $announcement->date = $announcement->date->format('Y-m-d');
        return Inertia::render('Management/Announcements/Edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'content' => ['required', 'string'],
        ]);

        $announcement->update($request->all());
        return redirect()->route('management.announcements.index')->with('success', 'Duyuru başarıyla güncellendi.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();
        return redirect()->route('management.announcements.index')->with('success', 'Duyuru başarıyla silindi.');
    }
}
