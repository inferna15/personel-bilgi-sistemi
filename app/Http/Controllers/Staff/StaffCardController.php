<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffCardUpdateRequest;
use App\Services\HttpService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffCardController extends Controller
{
    public function edit($id)
    {
        $user = auth()->user();
        $unit = $user->unit->name ?? 'Belirtilmemiş';
        $card = HttpService::getById('cards', $id)->json();

        if ($card['user_id'] !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Staff/Card/Edit', [
            'user' => $user->only('first_name', 'last_name', 'email', 'identity_number', 'birth_date', 'gender', 'phone', 'address', 'position', 'photo'),
            'unit' => $unit,
            'card' => $card
        ]);
    }


    public function update(StaffCardUpdateRequest $request, $id)
    {
        $card = HttpService::getById('cards', $id)->json();
        $amount = $request->input('amount');

        if ($card['user_id'] !== auth()->id()) {
            abort(403);
        }

        $card['balance'] = $card['balance'] + $amount;
        HttpService::put('cards' , $id, $card);
        return redirect()->route('home');
    }
}
