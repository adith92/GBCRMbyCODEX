<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#185FA5]">Sign In</p>
            <h3 class="mt-2 text-2xl font-semibold tracking-[-0.04em] text-[#042C53]">Akses Dashboard Operasional</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan akun yang memiliki role sesuai modul yang ingin ditinjau.</p>
        </div>

        <section class="rounded-[16px] border border-[#DCE5F0] bg-[#F7FAFE] p-4 sm:p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#E8F1FB] text-[#185FA5]">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a1 1 0 0 0 0 2v3a1 1 0 1 0 2 0v-3a1 1 0 0 0-1-1H9Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#185FA5]">Demo Credentials</p>
                    <p class="mt-1 text-sm text-slate-500">Klik salah satu role untuk langsung masuk ke akun demo terkait.</p>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['label' => 'General Manager', 'email' => 'gm@blueerp.test', 'password' => 'password', 'dot' => 'bg-[#185FA5]'],
                    ['label' => 'Sales', 'email' => 'sales@blueerp.test', 'password' => 'password', 'dot' => 'bg-[#2563EB]'],
                    ['label' => 'Finance', 'email' => 'finance@blueerp.test', 'password' => 'password', 'dot' => 'bg-[#059669]'],
                    ['label' => 'Operation', 'email' => 'operation@blueerp.test', 'password' => 'password', 'dot' => 'bg-[#D97706]'],
                ] as $demoUser)
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $demoUser['email'] }}">
                        <input type="hidden" name="password" value="{{ $demoUser['password'] }}">
                        <input type="hidden" name="remember" value="1">

                        <button
                            type="submit"
                            class="group w-full rounded-[16px] border border-[#E5E7EB] bg-white p-4 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#BFDBFE] hover:shadow-[0_18px_36px_rgba(24,95,165,0.12)] focus:outline-none focus:ring-2 focus:ring-[#378ADD]/30"
                        >
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-3 w-3 rounded-full {{ $demoUser['dot'] }}"></span>
                                <div class="min-w-0">
                                    <p class="text-[12px] font-semibold uppercase tracking-[0.14em] text-slate-500 transition group-hover:text-[#185FA5]">
                                        {{ $demoUser['label'] }}
                                    </p>
                                    <p class="mt-3 break-all font-mono text-[15px] font-medium text-[#1F2937]">
                                        {{ $demoUser['email'] }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-400">
                                        pwd: <span class="font-medium">password</span>
                                    </p>
                                </div>
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>
        </section>

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
