<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $sessionData = session()->all();

        $user = auth()->user();
        if ($user) {
            $user->lastAction = $user->getLastAction();
        }

        $filteredSessionData = array_filter($sessionData, function ($key) {
            return !in_array($key, ['_token', '_previous', '_flash']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge(parent::share($request), [
            'success' => session('success'),
            'session' => $filteredSessionData,
            'errors' => function () {
                return session()->get('errors') ? session()->get('errors')->getBag('default')->getMessages() : (object) [];
            },
            'user' => auth()->user(),
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(), // Добавляем текущий URL
                ]);
            },
        ]);
    }
}
