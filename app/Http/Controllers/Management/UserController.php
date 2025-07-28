<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserEditResource;
use App\Http\Resources\UserIndexResource;
use App\Http\Resources\UserShowResource;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $search = $request->input('search');

        $query = User::with('unit')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhereRaw('gender LIKE ?', ["%{$search}%"]);
                })->orWhereHas('unit', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('roles', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            });

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('Management/Users/Index', [
            'users' => UserIndexResource::collection($users),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'total' => $users->total(),
            'prev_page_url' => $users->previousPageUrl(),
            'next_page_url' => $users->nextPageUrl(),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $units = Unit::select(['id', 'name'])->get();
        if (auth()->user()->hasRole('Admin')) {
            $roles = ['Admin', 'Yönetici', 'Personel'];
        } else if (auth()->user()->hasRole('Yönetici')) {
            $roles = ['Personel'];
        } else {
            $roles = [];
        }
        return Inertia::render('Management/Users/Create', [
            'units' => $units,
            'roles' => $roles,
        ]);
    }

    public function store(UserRequest $request)
    {
        // Yetkilendirme kontrolü
        $this->authorize('create', User::class);

        $validated = $request->validated();
        $validated['password'] = bcrypt(Str::random(12));

        $photoFile = null;
        if (isset($validated['photo'])) {
            $photoFile = $validated['photo'];
            unset($validated['photo']);
        }

        $user = User::create($validated);

        if ($photoFile) {
            $path = $photoFile->store('user_photos', 'public');
            $user->photo = $path;
            $user->save();
        }

        $user->assignRole($validated['role']);


        try {
            $token = Password::getRepository()->create($user);
            $user->sendPasswordResetNotification($token);
        } catch (\Exception $e) {
            return redirect()->route('management.users.index')
                ->with('error', 'Kullanıcı başarıyla oluşturuldu, ancak şifre belirleme e-postası gönderilemedi: ' . $e->getMessage());
        }

        return redirect()->route('management.users.index');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return Inertia::render('Management/Users/Show', [
            'user' => new UserShowResource($user),
        ]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $units = Unit::select(['id', 'name'])->get();
        if (auth()->user()->hasRole('Admin')) {
            $roles = ['Admin', 'Yönetici', 'Personel'];
        } else if (auth()->user()->hasRole('Yönetici')) {
            $roles = ['Personel'];
        } else {
            $roles = [];
        }
        return Inertia::render('Management/Users/Edit', [
            'units' => $units,
            'user' => new UserEditResource($user),
            'roles' => $roles,
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('user_photos', 'public');
            $validated['photo'] = $path;
        } else {
            unset($validated['photo']);
        }

        $user->update($validated);
        if (isset($validated['role'])) {
            $user->syncRoles($validated['role']);
        }

        return redirect()->route('management.users.index');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return redirect()->route('management.users.index');
    }
}
