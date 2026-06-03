<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DemoEnvironmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DemoEnvironmentController extends Controller
{
    public function switchRole(Request $request): RedirectResponse
    {
        abort_unless($this->demoEnabled(), 404);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $allowedEmails = collect(config('rbac.demo_users', []))->pluck('email');
        abort_unless($allowedEmails->contains($request->string('email')->toString()), 403);

        $target = User::query()->where('email', $request->string('email')->toString())->firstOrFail();

        auth()->login($target);
        $request->session()->regenerate();

        return back()->with('success', 'Demo role berpindah ke '.$target->name.'.');
    }

    public function reset(Request $request, DemoEnvironmentService $service): RedirectResponse
    {
        abort_unless($this->demoEnabled(), 404);
        abort_unless($request->user()?->hasRole('super-admin'), 403);

        $service->resetDemoData();

        return redirect()->route('dashboard')->with('success', 'Demo data berhasil di-reset ulang.');
    }

    private function demoEnabled(): bool
    {
        return filter_var((string) env('ENABLE_DEMO_SEED', false), FILTER_VALIDATE_BOOL);
    }
}
