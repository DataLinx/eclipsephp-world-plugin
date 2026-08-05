<?php

namespace Workbench\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;
use Workbench\App\Models\User;

class WorkbenchBootstrap
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') === 'local' && ! Auth::guard('web')->check()) {
            $user = User::query()->first();

            if (! $user) {
                try {
                    $user = User::query()->firstOrCreate(
                        ['email' => 'test@example.com'],
                        [
                            'name' => 'Admin User',
                            'password' => Hash::make('password'),
                            'email_verified_at' => now(),
                        ],
                    );
                } catch (Throwable $e) {
                    Log::error('[Workbench] User creation failed', ['message' => $e->getMessage()]);
                    // In case of a race/unique constraint, fetch the existing one
                    $user = User::query()->where('email', 'test@example.com')->first();
                }
            }

            if ($user) {
                Auth::guard('web')->login($user);
                $request->session()->regenerate();

                if ($request->is('admin/login')) {
                    return redirect()->to('/admin');
                }
            }
        }

        return $next($request);
    }
}
