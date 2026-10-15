<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - CueReserve')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="flex min-h-screen">

        <!-- Sidebar Samping -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col shadow-lg">
            <div class="p-6 border-b border-gray-800">
                <h2 class="text-2xl font-bold text-blue-400">CueReserve</h2>
                <p class="text-sm text-gray-400 mt-1">Admin Panel</p>
            </div>

            <!-- Menu Navigasi -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-2 rounded-md transition duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} flex gap-2">
                    <svg xmlns="http://w3.org" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="5" rx="1"></rect>
                        <rect x="14" y="12" width="7" height="9" rx="1"></rect>
                        <rect x="3" y="16" width="7" height="5" rx="1"></rect>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.bookings') }}"
                    class="block px-4 py-2 rounded-md transition duration-200 {{ request()->routeIs('admin.bookings*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} flex gap-2">
                    <svg xmlns="http://w3.org" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                        <rect x="9" y="3" width="6" height="4" rx="1"></rect>
                        <path d="M9 12h6M9 16h4"></path>
                    </svg>
                    Kelola Pesanan
                </a>

                <a href="{{ route('tables.index') }}"
                    class="block px-4 py-2 rounded-md transition duration-200 {{ request()->routeIs('tables.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} flex gap-2">
                    <svg xmlns="http://w3.org" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Kunci Pas / Wrench -->
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"></path>
                    </svg>
                    Kelola Meja
                </a>

                <a href="{{ route('admin.settings') }}"
                    class="block px-4 py-2 rounded-md transition duration-200 {{ request()->routeIs('admin.settings*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} flex gap-2">
                    <svg xmlns="http://w3.org" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    Pengaturan
                </a>
            </nav>

            <!-- Bagian Bawah Sidebar -->
            <div class="p-4 border-t border-gray-800">
                <a href="{{ url('/') }}" target="_blank" class="block w-full items-center justify-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded transition duration-200 mb-2 flex gap-2">
                    <svg xmlns="http://w3.org" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Lingkaran Luar Bumi -->
                        <circle cx="12" cy="12" r="10"></circle>

                        <!-- Garis Khatulistiwa / Horisontal -->
                        <line x1="2" y1="12" x2="22" y2="12"></line>

                        <!-- Garis Bujur / Vertikal Melengkung -->
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    Lihat Website
                </a>
            </div>
        </aside>

        <!-- Konten Utama (Sebelah Kanan Sidebar) -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>

    </div>

</body>

</html>