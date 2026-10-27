<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Rackly Billiard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        .focus-brand:focus { outline: none; border-color: #1a5c38; box-shadow: 0 0 0 3px rgba(26,92,56,0.15); }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen grid md:grid-cols-2">
        {{-- LEFT: Image Panel --}}
        <div class="hidden md:flex hero-bg flex-col justify-between p-10">
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
            <div class="flex justify-between items-center px-8 py-5 border-b border-gray-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke beranda
                </a>
            </div>

            <div class="flex-1 flex items-center justify-center px-8 py-10">
                <div class="w-full max-w-md">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-green mb-2">Jadi bagian dari permainan</p>
                    <h2 class="text-3xl font-black text-gray-900 mb-1">Buat akun barumu.</h2>
                    <p class="text-gray-500 text-sm mb-8">Booking lebih mudah. Waktu bermain lebih terencana.</p>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama lengkap</label>
                            <input id="name" name="name" type="text" autocomplete="name" required
                                   value="{{ old('name') }}"
                                   placeholder="Nama lengkap kamu"
                                   class="w-full border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 text-sm focus-brand transition">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email + WhatsApp --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat email</label>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                       value="{{ old('email') }}"
                                       placeholder="kamu@email.com"
                                       class="w-full border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 text-sm focus-brand transition">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor WhatsApp</label>
                                <input id="phone" name="phone" type="tel"
                                       value="{{ old('phone') }}"
                                       placeholder="08xx xxxx xxxx"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus-brand transition">
                            </div>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata sandi</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" autocomplete="new-password" required
                                       placeholder="••••••••••"
                                       class="w-full border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 text-sm pr-10 focus-brand transition">
                                <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Minimal 8 karakter, gunakan kombinasi huruf dan angka.</p>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi kata sandi</label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                       placeholder="••••••••••"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm pr-10 focus-brand transition">
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Terms --}}
                        <div class="flex items-start gap-2">
                            <input type="checkbox" id="terms" required
                                   class="mt-0.5 w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <label for="terms" class="text-xs text-gray-600 leading-relaxed cursor-pointer">
                                Saya menyetujui <a href="#" class="text-brand-green font-semibold hover:underline">Syarat & Ketentuan</a> serta <a href="#" class="text-brand-green font-semibold hover:underline">Kebijakan Privasi</a> Rackly Billiard.
                            </label>
                        </div>

                        <button type="submit"
                                class="w-full bg-brand-green hover-brand text-white font-bold py-3.5 px-6 rounded-lg transition text-sm">
                            Buat akun →
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-600 mt-6">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline underline-offset-2">
                            Masuk di sini ↗
                        </a>
                    </p>
                </div>
            </div>

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
