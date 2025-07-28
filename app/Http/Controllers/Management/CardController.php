<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HttpService;
use App\Services\PolicyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardController extends Controller
{
    public function index(Request $request)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $search = $request->input('search');
        $data = HttpService::getAll('cards', $search, 10)->json();

        return Inertia::render('Management/Cards/Index', [
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
        return Inertia::render('Management/Cards/Create', [
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

        HttpService::post('cards', [
            'user_id' => $request->user_id,
            'full_name' => $full_name,
            'serial_no' => $request->serial_no,
            'type' => $request->type,
            'issue_date' => $request->issue_date,
            'balance' => $request->balance,
        ]);
        return redirect()->route('management.cards.index');
    }

    public function show($id)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $card = HttpService::getById('cards', $id)->json();
        return Inertia::render('Management/Cards/Show', [
            'card' => $card
        ]);
    }

    public function edit(Request $request, $id)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $response = HttpService::getById('cards', $id);
        return Inertia::render('Management/Cards/Edit', [
            'card' => $response->json() ?? null,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        $user = User::find($request->user_id);
        $full_name = $user->first_name . ' ' . $user->last_name;

        HttpService::put('cards' , $id, [
            'user_id' => $request->user_id,
            'full_name' => $full_name,
            'serial_no' => $request->serial_no,
            'type' => $request->type,
            'issue_date' => $request->issue_date,
            'balance' => $request->balance,
            'last_spend_date' => $request->last_spend_date,
            'last_spend_place' => $request->last_spend_place,
            'last_upload_date' => $request->last_upload_date,
            'last_upload_place' => $request->last_upload_place,
        ]);
        return redirect()->route('management.cards.index');
    }

    public function destroy($id)
    {
        if (PolicyService::isStaff(auth()->user())) {
            abort(404, 'Staff can not access this page.');
        }

        HttpService::delete('cards' , $id);
        return redirect()->route('management.cards.index');
    }
}
