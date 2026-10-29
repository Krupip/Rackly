<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Rackly Billiard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background-image: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.5) 100%), url('/images/Table photograph.png');
            background-size: cover;
            background-position: center;
        }
        .bg-brand-green { background-color: #1a5c38; }
        .hover-brand:hover { background-color: #14422a; }
        .text-brand-green { color: #1a5c38; }
        .border-brand-green { border-color: #1a5c38; }
        .focus-brand:focus { outline: none; border-color: #1a5c38; box-shadow: 0 0 0 3px rgba(26,92,56,0.15); }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen grid md:grid-cols-2">
        {{-- LEFT: Image Panel --}}
        <div class="hidden md:flex hero-bg flex-col justify-between p-10 relative">
            {{-- Logo --}}
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-full bg-[#1B5E43] text-white flex items-center justify-center font-bold text-xl shadow-sm">
                    8
                </div>
                <div>
                    <div class="font-bold text-white text-sm tracking-wide uppercase">Rackly</div>
                    <div class="text-xs text-green-200 -mt-0.5">Billiard Club</div>
                </div>
            </div>

            {{-- Hero Text --}}
            <div class="text-white">
                <p class="text-xs font-semibold uppercase tracking-widest text-green-300 mb-3">Sudut Favoritmu Menunggu</p>
                <h1 class="text-4xl font-black leading-tight mb-4">
                    Pukulan pertama.<br>Cerita berikutnya.
                </h1>
                <p class="text-gray-300 text-base mb-6">
                    Satu akun untuk jadwal bermain, meja favorit,<br>dan malam-malam yang berkesan.
                </p>
                <div class="flex items-center gap-2">
                    <div class="flex gap-0.5">
                        @for($i = 0; $i < 5; $i++)
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#facc15"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <span class="text-sm text-gray-300">4,9 dari 240+ pemain</span>
                </div>
                <div class="w-8 h-0.5 bg-green-400 mt-4"></div>
            </div>

            <p class="text-gray-500 text-xs">© 2026 Rackly Billiard · Bandung</p>
        </div>

        {{-- RIGHT: Form Panel --}}
        <div class="flex flex-col bg-white">
            {{-- Top nav --}}
            <div class="flex justify-between items-center px-8 py-5 border-b border-gray-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke beranda
                </a>
            </div>

            <div class="flex-1 flex items-center justify-center px-8 py-12">
                <div class="w-full max-w-md">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-green mb-2">Selamat datang kembali</p>
                    <h2 class="text-3xl font-black text-gray-900 mb-1">Masuk. Lalu, main.</h2>
                    <p class="text-gray-500 text-sm mb-8">Masuk untuk booking meja dan kelola jadwal bermainmu.</p>

                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat email</label>
                            <div class="relative">
                                <input id="email" name="email" type="email" autocomplete="email" required
                                       value="{{ old('email') }}"
                                       placeholder="kamu@email.com"
                                       class="w-full border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 text-sm pr-10 focus-brand transition">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata sandi</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                       placeholder="••••••••••"
                                       class="w-full border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 text-sm pr-10 focus-brand transition">
                                <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" id="remember_me"
                                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-600">Ingat saya</span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" :disabled="isSubmitting"
                                class="w-full bg-brand-green hover-brand disabled:opacity-75 disabled:cursor-not-allowed text-white font-bold py-3.5 px-6 rounded-lg transition flex items-center justify-center gap-2 text-sm shadow-sm">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Memproses masuk...' : 'Masuk ke akun →'"></span>
                        </button>
                    </form>

                    <div class="my-6 flex items-center gap-3">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-xs text-gray-400">atau</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <p class="text-center text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-brand-green font-bold hover:underline underline-offset-2">
                            Daftar sekarang ↗
                        </a>
                    </p>

                    <div class="mt-6 flex items-center gap-2 text-xs text-gray-400">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Data pribadimu aman bersama kami.
                    </div>
                </div>
            </div>

            {{-- Footer links --}}
            <div class="flex justify-center gap-8 px-8 py-4 border-t border-gray-100">
                <a href="#" class="text-xs text-gray-400 hover:text-gray-600 transition">Syarat & ketentuan</a>
                <a href="#" class="text-xs text-gray-400 hover:text-gray-600 transition">Kebijakan privasi</a>
                <a href="#" class="text-xs text-gray-400 hover:text-gray-600 transition">Bantuan ↗</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
