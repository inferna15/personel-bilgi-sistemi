<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HttpService;
use App\Services\PolicyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarController extends Controller
{
    public function index(Request $request)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $search = $request->input('search');
        $data = HttpService::getAll('cars', $search, 10)->json();

        return Inertia::render('Management/Cars/Index', [
            'data' => $data,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(Request $request)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }
        $userId = $request->input('user_id');
        return Inertia::render('Management/Cars/Create', [
            'users' => $userId ? null : User::select(['id', 'first_name', 'last_name'])->get(),
            'user_id' => $userId,
        ]);
    }

    public function store(Request $request)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $user = User::find($request->user_id);
        $full_name = $user->first_name . ' ' . $user->last_name;

        HttpService::post('cars', [
            'user_id' => $request->user_id,
            'full_name' => $full_name,
            'brand' => $request->brand,
            'model' => $request->model,
            'color' => $request->color,
            'license_plate' => $request->license_plate,
            'year' => $request->year,
            'type' => $request->type,
        ]);

        return redirect()->route('management.cars.index');
    }

    public function show($id)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $car = HttpService::getById('cars', $id)->json();

        return Inertia::render('Management/Cars/Show', [
            'car' => $car
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $response = HttpService::getById('cars', $id);
        return Inertia::render('Management/Cars/Edit', [
            'car' => $response->json() ?? null,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $user = User::find($request->user_id);
        $full_name = $user->first_name . ' ' . $user->last_name;

        HttpService::put('cars' , $id, [
            'user_id' => $request->user_id,
            'full_name' => $full_name,
            'brand' => $request->brand,
            'model' => $request->model,
            'color' => $request->color,
            'license_plate' => $request->license_plate,
            'year' => $request->year,
            'type' => $request->type,
        ]);
        return redirect()->route('management.cars.index');
    }

    public function destroy($id)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        HttpService::delete('cars' , $id);
        return redirect()->route('management.cars.index');
    }
}
