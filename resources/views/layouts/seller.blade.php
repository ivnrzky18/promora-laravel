@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
         style="display: none;"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out
                  -translate-x-full lg:translate-x-0 lg:static lg:inset-0 flex flex-col"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Sidebar Header --}}
        <div class="flex items-center justify-between h-16 px-6 bg-orange-500">
            <a href="{{ route('seller.dashboard') }}" class="flex items-center space-x-2">
                <span class="text-xl font-bold text-white">Promora</span>
                <span class="text-xs text-orange-100 font-medium">Seller</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-orange-100 min-h-[44px] min-w-[44px] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Seller Info --}}
        @auth
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Akun Penjual</p>
            <p class="text-sm font-semibold text-gray-800 mt-1 truncate">{{ auth()->user()->name }}</p>
            @if(auth()->user()->sellerProfile)
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->sellerProfile->business_name }}</p>
            @endif
        </div>
        @endauth

        {{-- Navigation --}}
        <nav class="px-4 py-4 space-y-1 flex-1 overflow-y-auto">
            {{-- Beranda: link ke homepage publik --}}
            <a href="{{ url('/') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->is('/') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 22V12h6v10"/>
                </svg>
                <span>Beranda</span>
            </a>

            <a href="{{ route('seller.dashboard') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('seller.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('seller.promos.index') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('seller.promos.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span>Promo Saya</span>
            </a>

            <a href="{{ route('seller.events.index') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('seller.events.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Event Saya</span>
            </a>

            <a href="{{ route('seller.profile') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('seller.profile') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profil</span>
            </a>
        </nav>

        {{-- Logout --}}
        <div class="px-4 py-4 border-t border-gray-100 flex-shrink-0">
            <form method="POST" action="{{ route('seller.logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center space-x-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 transition-colors min-h-[44px]">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-w-0 lg:ml-0">

        {{-- Top bar (mobile) --}}
        <div class="lg:hidden flex items-center justify-between h-16 px-4 bg-white shadow-sm border-b border-gray-200">
            <button @click="sidebarOpen = true"
                    class="text-gray-600 hover:text-orange-500 min-h-[44px] min-w-[44px] flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-lg font-bold text-orange-500">Promora Seller</span>
            <div class="w-10"></div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mx-4 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>
@endsection

@stack('seller_scripts')
