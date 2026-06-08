<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Promora - Platform UMKM Indonesia')</title>

    {{-- Google Fonts: Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'promora': {
                            DEFAULT: '#DD3015',
                            dark: '#F30000',
                            light: '#F3E1E1',
                        },
                        'accent': '#FFB800',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js via CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ mobileOpen: false, authOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2">
                        <span class="text-2xl font-bold" style="color: #DD3015;">Promora</span>
                    </a>
                </div>

                {{-- Desktop Navigation Links --}}
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ url('/explore') }}"
                       class="text-gray-600 font-medium transition-colors min-h-[44px] flex items-center hover:text-promora">
                        Jelajahi
                    </a>
                    <a href="{{ url('/hot-deals') }}"
                       class="text-gray-600 font-medium transition-colors min-h-[44px] flex items-center hover:text-promora">
                        Hot Deals
                    </a>
                    <a href="{{ url('/calendar') }}"
                       class="text-gray-600 font-medium transition-colors min-h-[44px] flex items-center hover:text-promora">
                        Kalender
                    </a>
                </div>

                {{-- Auth Links --}}
                <div class="hidden md:flex items-center space-x-3">
                    @auth
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-700 font-medium">{{ auth()->user()->name }}</span>

                            @if(auth()->user()->role === 'consumer')
                                <a href="{{ route('consumer.dashboard') }}"
                                   class="text-sm font-medium min-h-[44px] flex items-center" style="color: #DD3015;">
                                    Dashboard
                                </a>
                                <form method="POST" action="{{ route('consumer.logout') }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-sm text-gray-500 hover:text-red-500 font-medium min-h-[44px] px-3 transition-colors">
                                        Keluar
                                    </button>
                                </form>
                            @elseif(auth()->user()->role === 'seller')
                                <a href="{{ route('seller.dashboard') }}"
                                   class="text-sm font-medium min-h-[44px] flex items-center" style="color: #DD3015;">
                                    Dashboard
                                </a>
                                <form method="POST" action="{{ route('seller.logout') }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-sm text-gray-500 hover:text-red-500 font-medium min-h-[44px] px-3 transition-colors">
                                        Keluar
                                    </button>
                                </form>
                            @elseif(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}"
                                   class="text-sm font-medium min-h-[44px] flex items-center" style="color: #DD3015;">
                                    Admin Panel
                                </a>
                                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-sm text-gray-500 hover:text-red-500 font-medium min-h-[44px] px-3 transition-colors">
                                        Keluar
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        {{-- Login/Register Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-1 text-white px-4 py-2 rounded-lg transition-colors min-h-[44px] font-medium"
                                    style="background-color: #DD3015;"
                                    onmouseover="this.style.backgroundColor='#F30000'"
                                    onmouseout="this.style.backgroundColor='#DD3015'">
                                <span>Masuk / Daftar</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open"
                                 @click.outside="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50">

                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Konsumen
                                </div>
                                <a href="{{ route('consumer.login') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 min-h-[44px] flex items-center transition-colors"
                                   onmouseover="this.style.backgroundColor='#F3E1E1';this.style.color='#DD3015'"
                                   onmouseout="this.style.backgroundColor='';this.style.color='#374151'">
                                    Masuk sebagai Konsumen
                                </a>
                                <a href="{{ route('consumer.register') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 min-h-[44px] flex items-center transition-colors"
                                   onmouseover="this.style.backgroundColor='#F3E1E1';this.style.color='#DD3015'"
                                   onmouseout="this.style.backgroundColor='';this.style.color='#374151'">
                                    Daftar sebagai Konsumen
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>

                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Penjual (UMKM)
                                </div>
                                <a href="{{ route('seller.login') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 min-h-[44px] flex items-center transition-colors"
                                   onmouseover="this.style.backgroundColor='#F3E1E1';this.style.color='#DD3015'"
                                   onmouseout="this.style.backgroundColor='';this.style.color='#374151'">
                                    Masuk sebagai Penjual
                                </a>
                                <a href="{{ route('seller.register') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 min-h-[44px] flex items-center transition-colors"
                                   onmouseover="this.style.backgroundColor='#F3E1E1';this.style.color='#DD3015'"
                                   onmouseout="this.style.backgroundColor='';this.style.color='#374151'">
                                    Daftar sebagai Penjual
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <div class="md:hidden">
                    <button @click="mobileOpen = !mobileOpen"
                            class="text-gray-600 p-2 min-h-[44px] min-w-[44px] flex items-center justify-center"
                            style="hover-color: #DD3015;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-transition class="md:hidden bg-white border-t border-gray-100">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ url('/explore') }}"
                   class="block py-2 text-gray-700 font-medium min-h-[44px] flex items-center">Jelajahi</a>
                <a href="{{ url('/hot-deals') }}"
                   class="block py-2 text-gray-700 font-medium min-h-[44px] flex items-center">Hot Deals</a>
                <a href="{{ url('/calendar') }}"
                   class="block py-2 text-gray-700 font-medium min-h-[44px] flex items-center">Kalender</a>

                <div class="border-t border-gray-100 pt-3 mt-3">
                    @auth
                        <p class="text-sm text-gray-500 mb-2">Masuk sebagai: <strong>{{ auth()->user()->name }}</strong></p>
                        @if(auth()->user()->role === 'consumer')
                            <a href="{{ route('consumer.dashboard') }}"
                               class="block py-2 font-medium min-h-[44px] flex items-center" style="color: #DD3015;">Dashboard</a>
                            <form method="POST" action="{{ route('consumer.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left py-2 text-red-500 font-medium min-h-[44px] flex items-center">Keluar</button>
                            </form>
                        @elseif(auth()->user()->role === 'seller')
                            <a href="{{ route('seller.dashboard') }}"
                               class="block py-2 font-medium min-h-[44px] flex items-center" style="color: #DD3015;">Dashboard</a>
                            <form method="POST" action="{{ route('seller.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left py-2 text-red-500 font-medium min-h-[44px] flex items-center">Keluar</button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('consumer.login') }}" class="block py-2 text-gray-700 min-h-[44px] flex items-center">Masuk sebagai Konsumen</a>
                        <a href="{{ route('consumer.register') }}" class="block py-2 text-gray-700 min-h-[44px] flex items-center">Daftar sebagai Konsumen</a>
                        <a href="{{ route('seller.login') }}" class="block py-2 text-gray-700 min-h-[44px] flex items-center">Masuk sebagai Penjual</a>
                        <a href="{{ route('seller.register') }}" class="block py-2 text-gray-700 min-h-[44px] flex items-center">Daftar sebagai Penjual</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="text-gray-300 mt-auto" style="background-color: #1f2937;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white text-xl font-bold mb-3">Promora</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Platform terpusat untuk menemukan promo dan event UMKM lokal terbaik di Indonesia.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Tautan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/explore') }}" class="inline-flex items-center min-h-[44px] py-2 transition-colors" style="color: #d1d5db;" onmouseover="this.style.color='#FFB800'" onmouseout="this.style.color='#d1d5db'">Jelajahi Promo</a></li>
                        <li><a href="{{ url('/hot-deals') }}" class="inline-flex items-center min-h-[44px] py-2 transition-colors" style="color: #d1d5db;" onmouseover="this.style.color='#FFB800'" onmouseout="this.style.color='#d1d5db'">Hot Deals</a></li>
                        <li><a href="{{ url('/calendar') }}" class="inline-flex items-center min-h-[44px] py-2 transition-colors" style="color: #d1d5db;" onmouseover="this.style.color='#FFB800'" onmouseout="this.style.color='#d1d5db'">Kalender Event</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Bergabung</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('consumer.register') }}" class="inline-flex items-center min-h-[44px] py-2 transition-colors" style="color: #d1d5db;" onmouseover="this.style.color='#FFB800'" onmouseout="this.style.color='#d1d5db'">Daftar sebagai Konsumen</a></li>
                        <li><a href="{{ route('seller.register') }}" class="inline-flex items-center min-h-[44px] py-2 transition-colors" style="color: #d1d5db;" onmouseover="this.style.color='#FFB800'" onmouseout="this.style.color='#d1d5db'">Daftarkan UMKM Anda</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Promora. Hak cipta dilindungi.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>