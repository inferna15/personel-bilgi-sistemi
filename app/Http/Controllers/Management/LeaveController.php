<?php

namespace App\Http\Controllers\Management;

use App\Enum\LeaveStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use App\Http\Resources\LeaveEditResource;
use App\Http\Resources\LeaveIndexResource;
use App\Http\Resources\LeaveShowResource;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Leave::class);

        $search = $request->input('search');

        $query = Leave::with('user')
            ->when($search, function ($q) use ($search) {
            $q->where(function ($query) use ($search) {
                $query->whereRaw("type LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CAST(days_count AS CHAR) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("status LIKE ?", ["%{$search}%"]);
                })
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
        });

        $leaves = $query->paginate(10)->withQueryString();

        return Inertia::render('Management/Leaves/Index', [
            'leaves' => LeaveIndexResource::collection($leaves),
            'current_page' => $leaves->currentPage(),
            'last_page' => $leaves->lastPage(),
            'total' => $leaves->total(),
            'prev_page_url' => $leaves->previousPageUrl(),
            'next_page_url' => $leaves->nextPageUrl(),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Leave::class);
        $userId = $request->input('user_id');
        return Inertia::render('Management/Leaves/Create', [
            'users' => $userId ? null : User::select(['id', 'first_name', 'last_name'])->get(),
            'user_id' => $userId,
        ]);
    }


    public function store(StoreLeaveRequest $request)
    {
        $this->authorize('create', Leave::class);
        $validated = $request->validated();
        if ($validated['status'] === LeaveStatus::APPROVED->value || $validated['status'] === LeaveStatus::REJECTED->value) {
            $validated['reviewed_by'] = auth()->id(); // ya da auth()->user()->id
            $validated['reviewed_at'] = now();
        }
        Leave::create($validated);
        return redirect()->route('management.leaves.index')->with('success', 'Leave request created.');
    }

    public function show(Leave $leave)
    {
        $this->authorize('view', $leave);
        $reviewed_by = null;
        if ($leave->reviewed_by) {
            $reviewed_by = User::find($leave->reviewed_by)->select(['id', 'first_name', 'last_name'])->first();
        }
        return Inertia::render('Management/Leaves/Show', [
            'leave' => new LeaveShowResource($leave),
            'user' => User::find($leave->user_id)->select('id', 'first_name', 'last_name', 'email')->first(),
            'reviewed_by' => $reviewed_by,
        ]);
    }

    public function edit(Leave $leave)
    {
        $this->authorize('update', $leave);
        return Inertia::render('Management/Leaves/Edit', [
            'leave' => new LeaveEditResource($leave),
            'user' => User::find($leave->user_id)->select('id', 'first_name', 'last_name')->first(),
        ]);
    }

    public function update(UpdateLeaveRequest $request, Leave $leave)
    {
        $this->authorize('update', $leave);
        $validated = $request->validated();
        if ($leave->status === LeaveStatus::PENDING &&
            (LeaveStatus::from($validated['status']) === LeaveStatus::APPROVED ||
                LeaveStatus::from($validated['status']) === LeaveStatus::REJECTED)){
            $validated['reviewed_by'] = auth()->id();
            $validated['reviewed_at'] = now();
        } else if (LeaveStatus::from($validated['status']) === LeaveStatus::PENDING &&
            ($leave->status === LeaveStatus::APPROVED || $leave->status === LeaveStatus::REJECTED)) {
            $validated['reviewed_by'] = null;
            $validated['reviewed_at'] = null;
        }

        $leave->update($validated);
        return redirect()->route('management.leaves.index');
    }

    public function destroy(Leave $leave)
    {
        $this->authorize('delete', $leave);
        $leave->delete();
        return redirect()->route('management.leaves.index');
    }

    public function review(Request $request)
    {
        $this->authorize('viewAny', Leave::class);

        $search = $request->input('search');

        $query = Leave::pending()->with(['user'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->whereRaw("type LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("CAST(days_count AS CHAR) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("status LIKE ?", ["%{$search}%"]);
                })
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    });
            });

        $reviews = $query->paginate(10)->withQueryString(); // Sayfalandırma ekledik

        return Inertia::render('Management/Leaves/Review', [
            'reviews' => LeaveIndexResource::collection($reviews), // Resource koleksiyonuna sarıyoruz
            'current_page' => $reviews->currentPage(),
            'last_page' => $reviews->lastPage(),
            'total' => $reviews->total(),
            'prev_page_url' => $reviews->previousPageUrl(),
            'next_page_url' => $reviews->nextPageUrl(),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function approve(Leave $leave)
    {
        $this->authorize('update', $leave);
        $leave->update(['status' => LeaveStatus::APPROVED->value, 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
        return redirect()->route('management.leaves.review');
    }

    public function reject(Leave $leave)
    {
        $this->authorize('update', $leave);
        $leave->update(['status' => LeaveStatus::REJECTED->value, 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
        return redirect()->route('management.leaves.review');
    }
}
