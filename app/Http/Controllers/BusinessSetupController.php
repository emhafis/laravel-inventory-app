<?php

namespace App\Http\Controllers;

use App\Enums\BusinessRole;
use App\Http\Requests\StoreBusinessRequest;
use App\Models\Business;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BusinessSetupController extends Controller
{
    public function create(): View
    {
        return view('businesses.create');
    }

    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $business = DB::transaction(function () use ($request) {
            $slug = $this->makeUniqueSlug(
                $request->string('name')->toString(),
                $request->filled('slug') ? $request->string('slug')->toString() : null
            );

            $biz = Business::query()->create([
                'name' => $request->string('name'),
                'slug' => $slug,
                'timezone' => $request->string('timezone'),
                'currency_code' => $request->string('currency_code'),
                'is_active' => true,
            ]);

            $request->user()->businesses()->attach($biz->id, [
                'role' => BusinessRole::Owner->value,
            ]);

            return $biz;
        });

        $request->session()->put('current_business_id', $business->id);
        BusinessContext::set($business);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Bisnis baru berhasil dibuat. Anda ditetapkan sebagai pemilik.');
    }

    private function makeUniqueSlug(string $name, ?string $explicitSlug): string
    {
        $base = Str::slug($explicitSlug ?? $name) ?: 'bisnis';
        $slug = $base;
        $i = 1;

        while (Business::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
