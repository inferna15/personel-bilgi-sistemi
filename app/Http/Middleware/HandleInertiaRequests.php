<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // parent::share($request) ile gelen varsayılan verileri alıyoruz (örn: ziggy routes, flash messages)
        $sharedData = parent::share($request);

        // Auth verisini güvenli bir şekilde oluşturuyoruz
        $authData = [
            'user' => null,
            'role' => null,
        ];

        if ($request->user()) { // Kullanıcı giriş yapmışsa
            $authData['user'] = [
                'id' => $request->user()->id,
                'name' => $request->user()->name, // veya $request->user()->first_name . ' ' . $request->user()->last_name
                'email' => $request->user()->email,
                // Eğer Spatie Laravel-Permission kullanıyorsanız:
                'role' => $request->user()->getRoleNames()->first(),
                // Eğer 'role' doğrudan User modelinizde bir sütun ise:
                // 'role' => $request->user()->role,
                // İhtiyaç duyduğunuz diğer kullanıcı bilgilerini buraya ekleyin
            ];
            // Eğer 'role' bilgisini doğrudan 'auth' objesinin altına koymak isterseniz:
            // $authData['role'] = $request->user()->getRoleNames()->first();
        }

        // Oluşturduğumuz auth verisini paylaşılan verilere ekliyoruz
        $sharedData['auth'] = $authData;

        // Flash mesajları (eğer kullanıyorsanız, parent::share içinde değilse buraya ekleyin)
        $sharedData['flash'] = [
            'success' => fn () => $request->session()->get('success'),
            'error' => fn () => $request->session()->get('error'),
        ];


        return $sharedData;
    }
}
