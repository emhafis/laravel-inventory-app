<?php

namespace App\Http\Controllers;

use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessSwitchController extends Controller
{
    public function select(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $businesses = $user->businesses()->where('businesses.is_active', true)->orderBy('name')->get();

        if ($businesses->isEmpty()) {
            return redirect()
                ->route('businesses.create')
                ->with('status', 'Belum ada bisnis. Buat bisnis baru untuk memulai.');
        }

        if ($businesses->count() === 1) {
            $request->session()->put('current_business_id', $businesses->first()->id);
            BusinessContext::set($businesses->first());

            return redirect()->intended(route('dashboard'));
        }

        return view('business.select', [
            'businesses' => $businesses,
        ]);
    }

    public function switch(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
        ]);

        if (! $request->user()->hasAccessToBusiness((int) $data['business_id'])) {
            return back()->with('error', 'Akses ditolak ke bisnis tersebut.');
        }

        $request->session()->put('current_business_id', (int) $data['business_id']);

        return redirect()->route('dashboard')->with('status', 'Bisnis aktif diperbarui.');
    }
}
