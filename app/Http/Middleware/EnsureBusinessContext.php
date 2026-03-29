<?php

namespace App\Http\Middleware;

use App\Models\Business;
use App\Support\BusinessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $businessId = (int) $request->session()->get('current_business_id');
        if ($businessId <= 0) {
            return redirect()->route('business.select');
        }

        if (! $user->hasAccessToBusiness($businessId)) {
            $request->session()->forget('current_business_id');
            BusinessContext::clear();

            return redirect()
                ->route('business.select')
                ->with('error', 'Anda tidak memiliki akses ke bisnis tersebut.');
        }

        $business = Business::query()->whereKey($businessId)->where('is_active', true)->first();
        if (! $business) {
            $request->session()->forget('current_business_id');
            BusinessContext::clear();

            return redirect()
                ->route('business.select')
                ->with('error', 'Bisnis tidak ditemukan atau tidak aktif.');
        }

        BusinessContext::set($business);
        view()->share('currentBusiness', $business);

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        BusinessContext::clear();
    }
}
