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

    {{-- Desktop Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out
                  -translate-x-full lg:translate-x-0 lg:static lg:inset-0 hidden lg:flex lg:flex-col"
           :class="sidebarOpen ? 'translate-x-0 flex flex-col' : '-translate-x-full lg:translate-x-0'">

        {{-- Sidebar Header --}}
        <div class="flex items-center justify-between h-16 px-6 bg-orange-500 flex-shrink-0">
            <a href="{{ route('consumer.dashboard') }}" class="flex items-center space-x-2">
                <span class="text-xl font-bold text-white">Promora</span>
                <span class="text-xs text-orange-100 font-medium">Consumer</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-orange-100 min-h-[44px] min-w-[44px] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- User Info --}}
        @auth
        <div class="px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Akun Konsumen</p>
            <p class="text-sm font-semibold text-gray-800 mt-1 truncate">{{ auth()->user()->name }}</p>
            @if(auth()->user()->location)
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->location }}</p>
            @endif
        </div>
        @endauth

        {{-- Navigation --}}
        <nav class="px-4 py-4 space-y-1 flex-1">
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

            <a href="{{ route('consumer.dashboard') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('consumer.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ url('/explore') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->is('explore*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Jelajahi</span>
            </a>

            <a href="{{ url('/calendar') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->is('calendar*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Kalender</span>
            </a>

            <a href="{{ route('consumer.bookmarks') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('consumer.bookmarks') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                <span>Bookmark</span>
            </a>

            <a href="{{ route('consumer.profile') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors min-h-[44px]
                      {{ request()->routeIs('consumer.profile') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profil</span>
            </a>
        </nav>

        {{-- Notification Bell --}}
        @auth
        <div class="px-4 py-3 border-t border-gray-100 flex-shrink-0">
            @include('components.notification-bell')
        </div>
        @endauth

        {{-- Logout --}}
        <div class="px-4 py-4 border-t border-gray-100 flex-shrink-0">
            <form method="POST" action="{{ route('consumer.logout') }}">
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
    <div class="flex-1 flex flex-col min-w-0 pb-16 lg:pb-0">

        {{-- Top bar (mobile) --}}
        <div class="lg:hidden flex items-center justify-between h-16 px-4 bg-white shadow-sm border-b border-gray-200">
            <button @click="sidebarOpen = true"
                    class="text-gray-600 hover:text-orange-500 min-h-[44px] min-w-[44px] flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-lg font-bold text-orange-500">Promora</span>
            <div class="flex items-center">
                @auth
                @include('components.notification-bell')
                @endauth
            </div>
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

{{-- Mobile Bottom Navigation --}}
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-lg">
    <div class="grid grid-cols-5 h-16">
        <a href="{{ url('/') }}"
           class="flex flex-col items-center justify-center space-y-1 min-h-[44px]
                  {{ request()->is('/') ? 'text-orange-500' : 'text-gray-500 hover:text-orange-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 22V12h6v10"/>
            </svg>
            <span class="text-xs font-medium">Beranda</span>
        </a>

        <a href="{{ url('/explore') }}"
           class="flex flex-col items-center justify-center space-y-1 min-h-[44px]
                  {{ request()->is('explore*') ? 'text-orange-500' : 'text-gray-500 hover:text-orange-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-xs font-medium">Jelajahi</span>
        </a>

        <a href="{{ url('/calendar') }}"
           class="flex flex-col items-center justify-center space-y-1 min-h-[44px]
                  {{ request()->is('calendar*') ? 'text-orange-500' : 'text-gray-500 hover:text-orange-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs font-medium">Kalender</span>
        </a>

        <a href="{{ route('consumer.bookmarks') }}"
           class="flex flex-col items-center justify-center space-y-1 min-h-[44px]
                  {{ request()->routeIs('consumer.bookmarks') ? 'text-orange-500' : 'text-gray-500 hover:text-orange-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
            <span class="text-xs font-medium">Bookmark</span>
        </a>

        <a href="{{ route('consumer.profile') }}"
           class="flex flex-col items-center justify-center space-y-1 min-h-[44px]
                  {{ request()->routeIs('consumer.profile') ? 'text-orange-500' : 'text-gray-500 hover:text-orange-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs font-medium">Profil</span>
        </a>
    </div>
</nav>
@endsection

@stack('consumer_scripts')
