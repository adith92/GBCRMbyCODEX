<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#185FA5]">Sign In</p>
            <h3 class="mt-2 text-2xl font-semibold tracking-[-0.04em] text-[#042C53]">Akses Dashboard Operasional</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan akun yang memiliki role sesuai modul yang ingin ditinjau.</p>
        </div>

        <x-auth-session-status class="mb-1" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@company.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input
                    id="password"
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                    <input id="remember_me" type="checkbox" class="rounded-[6px] border-[#D7DCE3] text-[#185FA5] shadow-sm focus:ring-[#378ADD]/20" name="remember">
                    <span>{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-[#185FA5] transition hover:text-[#042C53] hover:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="flex items-center justify-end pt-2">
                <x-primary-button class="w-full sm:w-auto">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>

        <div class="rounded-[12px] border border-[#E5E7EB] bg-[#F9FAFB] px-4 py-3 text-xs leading-6 text-slate-500">
            Demo login cepat:
            <span class="font-semibold text-[#042C53]">superadmin@blueerp.test</span>
            /
            <span class="font-semibold text-[#042C53]">password</span>
        </div>
    </div>
</x-guest-layout>
