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
        <div class="flex items-center justify-between h-16 px-6 bg-red-600">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <span class="text-xl font-bold text-white">Promora</span>
                <span class="text-xs text-red-100 font-medium">Admin</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-red-100 min-h-[44px] min-w-[44px] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Admin Info --}}
        @auth
        <div class="px-6 py-4 border-b border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Administrator</p>
            <p class="text-sm font-semibold text-gray-800 mt-1 truncate">{{ auth()->user()->name }}</p>
        </div>
        @endauth

        {{-- Navigation --}}
        <nav class="px-4 py-4 space-y-1 flex-1 overflow-y-auto">
            {{-- Beranda: link ke homepage publik --}}
            <a href="{{ url('/') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->is('/') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 22V12h6v10"/>
                </svg>
                <span>Beranda</span>
            </a>

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- Verifikasi Seller --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Verifikasi Seller</span>
            </a>

            {{-- Moderasi Promo --}}
            <a href="{{ route('admin.promos.index') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('admin.promos.*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span>Moderasi Promo</span>
            </a>

            {{-- Kategori --}}
            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('admin.categories.*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Kategori</span>
            </a>

            {{-- Statistik --}}
            <a href="{{ route('admin.stats') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('admin.stats') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Statistik</span>
            </a>
        </nav>

        {{-- Logout --}}
        <div class="px-4 py-4 border-t border-gray-100 flex-shrink-0">
            <form method="POST" action="{{ route('admin.logout') }}">
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
                    class="text-gray-600 hover:text-red-500 min-h-[44px] min-w-[44px] flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-lg font-bold text-red-600">Promora Admin</span>
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

@stack('admin_scripts')
