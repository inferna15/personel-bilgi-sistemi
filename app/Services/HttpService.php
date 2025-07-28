<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HttpService
{
    public static function getAll($table, $search = null, $per_page = 10) {
        return Http::withToken(config('services.kvas.token'))
            ->withoutVerifying()
            ->get('http://localhost:8000/api/'. $table . '?search=' . $search . '&per_page=' . $per_page);
    }

    public static function getById($table, $id) {
        return Http::withToken(config('services.kvas.token'))
            ->withoutVerifying()
            ->get('http://localhost:8000/api/'. $table . '/' . $id);
    }

    public static function getByUserId($table, $userId) {
        return Http::withToken(config('services.kvas.token'))
            ->withoutVerifying()
            ->get('http://localhost:8000/api/user/' . $userId . '/' . $table);
    }

    public static function post($table, $data) {
        return Http::withToken(config('services.kvas.token'))
            ->withoutVerifying()
            ->post('http://localhost:8000/api/'. $table, $data);
    }

    public static function put($table, $id ,$data) {
        return Http::withToken(config('services.kvas.token'))
            ->withoutVerifying()
            ->put('http://localhost:8000/api/'. $table . '/' . $id, $data);
    }

    public static function delete($table, $id) {
        return Http::withToken(config('services.kvas.token'))
            ->withoutVerifying()
            ->delete('http://localhost:8000/api/'. $table . '/' . $id);
    }
}
