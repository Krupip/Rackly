<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — Rackly Billiard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAF9] text-gray-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-10">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#1B5E43] text-white flex items-center justify-center font-bold text-xl shadow-sm">
                        8
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-gray-900 block leading-tight">RACKLY</span>
                        <span class="text-[10px] tracking-widest text-[#1B5E43] font-bold block uppercase -mt-1">Billiard Club</span>
                    </div>
                </a>

                <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-600">
                    <a href="{{ url('/') }}" class="hover:text-[#1B5E43] transition">Beranda</a>
                    <a href="{{ url('/#tables') }}" class="hover:text-[#1B5E43] transition">Pilihan meja</a>
                    <a href="{{ url('/#how-to-book') }}" class="hover:text-[#1B5E43] transition">Cara booking</a>
                    <a href="{{ url('/#location') }}" class="hover:text-[#1B5E43] transition">Lokasi</a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('booking.history') }}" class="text-sm font-medium text-gray-700 hover:text-[#1B5E43] transition">
                    Riwayat booking
                </a>

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-1 pl-2 pr-3 rounded-full hover:bg-gray-100 border border-transparent hover:border-gray-200 transition">
                            <div class="w-8 h-8 rounded-full bg-[#E8F3EE] text-[#1B5E43] font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-800 hidden sm:inline">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Masuk sebagai</p>
                                <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-semibold text-[#1B5E43] hover:bg-gray-50">Profil saya</a>
                            <a href="{{ route('booking.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Riwayat booking</a>
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-[#1B5E43] font-semibold hover:bg-emerald-50">Admin Panel</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-1">
        
        <!-- Header Title -->
        <div class="mb-8">
            <span class="text-xs font-bold tracking-wider text-[#1B5E43] uppercase block mb-1">AKUN SAYA</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Profil saya</h1>
            <p class="text-gray-500 mt-2 text-sm sm:text-base">Kelola informasi akun dan buat pengalaman booking lebih mudah.</p>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Informasi profil berhasil diperbarui.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Kata sandi berhasil diganti.
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Sidebar Card (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 text-center">
                    
                    <div class="w-20 h-20 rounded-full bg-[#E8F3EE] text-[#1B5E43] font-bold text-2xl flex items-center justify-center mx-auto mb-4">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>

                    <h2 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                    
                    <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Member Aktif
                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        Bergabung sejak {{ Auth::user()->created_at->translatedFormat('F Y') }}
                    </p>

                    <div class="border-t border-gray-100 my-6"></div>

                    <!-- Navigation Links -->
                    <div class="space-y-1.5 text-left text-sm font-semibold">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-emerald-50 text-[#1B5E43]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil saya
                        </a>
                        <a href="{{ route('booking.history') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-gray-600 hover:bg-gray-50 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Riwayat booking
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-red-600 hover:bg-red-50 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Green Notification Box -->
                <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-4 flex gap-3 text-xs text-emerald-900 leading-relaxed">
                    <svg class="w-5 h-5 text-[#1B5E43] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        Email & identitas akun digunakan untuk verifikasi status booking dan penagihan saat tiba di klub.
                    </div>
                </div>

            </div>

            <!-- Right Forms Area (8 cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Card 1: Informasi Pribadi -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
                    <div class="border-b border-gray-100 pb-4 mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Informasi pribadi</h2>
                        <p class="text-xs text-gray-400 mt-1">Pastikan data di bawah ini selalu akurat dan terbaru.</p>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <x-input-error class="mt-1" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Alamat Email</label>
                                <div class="relative">
                                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition pr-10">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-emerald-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </div>
                                <x-input-error class="mt-1" :messages="$errors->get('email')" />
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <span class="text-xs text-gray-400">Perubahan berlaku otomatis untuk booking selanjutnya.</span>
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-[#1B5E43] hover:bg-[#154b35] text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card 2: Keamanan Akun (Ganti Password) -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 sm:p-8">
                    <div class="border-b border-gray-100 pb-4 mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Keamanan akun</h2>
                        <p class="text-xs text-gray-400 mt-1">Gunakan kata sandi unik untuk melindungi akun Anda.</p>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                        @csrf
                        @method('put')

                        <div>
                            <label for="update_password_current_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kata Sandi Saat Ini</label>
                            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="update_password_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                                <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                            </div>

                            <div>
                                <label for="update_password_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi Baru</label>
                                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B5E43]/20 focus:border-[#1B5E43] transition">
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <span class="text-xs text-gray-400">Minimal 8 karakter unik.</span>
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-bold transition">
                                Perbarui kata sandi
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-500">
            <p>© {{ date('Y') }} Rackly Billiard Club. Hak cipta dilindungi.</p>
            <p>Butuh bantuan? WhatsApp <a href="https://wa.me/6281288881908" class="text-[#1B5E43] font-semibold hover:underline">0812-8888-1908</a></p>
        </div>
    </footer>

</body>
</html>
