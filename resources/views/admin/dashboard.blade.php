@extends('layouts.admin')

@section('title', 'Dashboard Admin - Promora')

@section('content')
<div style="padding: 28px 24px; max-width: 1100px; margin: 0 auto;">

    {{-- Page Header --}}
    <div style="margin-bottom: 28px;">
        <h1 style="font-size: 22px; font-weight: 700; color: #DD3015; letter-spacing: -0.3px;">
            Dashboard Admin
        </h1>
        <p style="font-size: 14px; color: #9e6e6e; margin-top: 4px;">
            Kelola verifikasi seller yang menunggu persetujuan.
        </p>
    </div>

    {{-- Stat Cards --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 28px;">

        {{-- Pending --}}
        <div style="background: #fff; border-radius: 14px; padding: 18px 20px; border: 1px solid rgba(221,48,21,0.10); display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,184,0,0.15); color: #b98200; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 12px; color: #aaa; font-weight: 500;">Menunggu Verifikasi</div>
                <div style="font-size: 22px; font-weight: 700; color: #b98200; line-height: 1.2;">
                    {{ $pendingSellers->count() }}
                </div>
            </div>
        </div>

        {{-- Verified --}}
        <div style="background: #fff; border-radius: 14px; padding: 18px 20px; border: 1px solid rgba(221,48,21,0.10); display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(34,197,94,0.12); color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 12px; color: #aaa; font-weight: 500;">Seller Terverifikasi</div>
                <div style="font-size: 22px; font-weight: 700; color: #16a34a; line-height: 1.2;">
                    {{ $verifiedSellers ?? 0 }}
                </div>
            </div>
        </div>

        {{-- Total --}}
        <div style="background: #fff; border-radius: 14px; padding: 18px 20px; border: 1px solid rgba(221,48,21,0.10); display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(221,48,21,0.10); color: #DD3015; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 12px; color: #aaa; font-weight: 500;">Total Seller</div>
                <div style="font-size: 22px; font-weight: 700; color: #222; line-height: 1.2;">
                    {{ $totalSellers ?? 0 }}
                </div>
            </div>
        </div>

    </div>

    {{-- Pending Sellers Table --}}
    <div style="background: #fff; border-radius: 16px; border: 1px solid rgba(221,48,21,0.12); overflow: hidden;">

        {{-- Card Header --}}
        <div style="padding: 18px 24px; border-bottom: 1px solid rgba(221,48,21,0.10); display: flex; align-items: center; justify-content: space-between; background: linear-gradient(90deg, #DD3015 0%, #c0280e 100%);">
            <h2 style="font-size: 15px; font-weight: 600; color: #fff;">
                Seller Menunggu Verifikasi
            </h2>
            @if($pendingSellers->isNotEmpty())
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #FFB800; color: #7a5500;">
                    <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                    {{ $pendingSellers->count() }} pending
                </span>
            @endif
        </div>

        @if($pendingSellers->isEmpty())
            {{-- Empty State --}}
            <div style="padding: 56px 24px; text-align: center;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #F3E1E1; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;">
                    <svg width="26" height="26" fill="none" stroke="#DD3015" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 style="font-size: 14px; font-weight: 500; color: #555;">Tidak ada seller yang menunggu verifikasi</h3>
                <p style="font-size: 13px; color: #aaa; margin-top: 4px;">Semua seller sudah diverifikasi.</p>
            </div>

        @else
            {{-- Table --}}
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 680px;">
                    <thead style="background: #fdf4f2;">
                        <tr>
                            <th style="padding: 11px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #DD3015; text-transform: uppercase; letter-spacing: 0.6px;">
                                Nama Bisnis
                            </th>
                            <th style="padding: 11px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #DD3015; text-transform: uppercase; letter-spacing: 0.6px;">
                                Kategori
                            </th>
                            <th style="padding: 11px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #DD3015; text-transform: uppercase; letter-spacing: 0.6px;">
                                Alamat
                            </th>
                            <th style="padding: 11px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #DD3015; text-transform: uppercase; letter-spacing: 0.6px;">
                                Tanggal Daftar
                            </th>
                            <th style="padding: 11px 20px; text-align: right; font-size: 11px; font-weight: 700; color: #DD3015; text-transform: uppercase; letter-spacing: 0.6px;">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingSellers as $seller)
                            <tr style="border-top: 1px solid rgba(221,48,21,0.07);"
                                onmouseover="this.style.background='#fdf4f2'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Nama Bisnis --}}
                                <td style="padding: 14px 20px; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        @if($seller->logo)
                                            <img src="{{ Storage::url($seller->logo) }}"
                                                 alt="{{ $seller->business_name }}"
                                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                                        @else
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #DD3015, #F30000); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #fff; flex-shrink: 0;">
                                                {{ strtoupper(substr($seller->business_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div style="font-size: 13px; font-weight: 600; color: #222;">{{ $seller->business_name }}</div>
                                            <div style="font-size: 12px; color: #aaa; margin-top: 1px;">{{ $seller->user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td style="padding: 14px 20px; vertical-align: middle;">
                                    <span style="display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: rgba(221,48,21,0.09); color: #DD3015;">
                                        {{ $seller->business_category }}
                                    </span>
                                </td>

                                {{-- Alamat --}}
                                <td style="padding: 14px 20px; vertical-align: middle;">
                                    <div style="font-size: 13px; color: #666; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $seller->address }}
                                    </div>
                                </td>

                                {{-- Tanggal --}}
                                <td style="padding: 14px 20px; vertical-align: middle;">
                                    <span style="font-size: 12px; color: #aaa;">
                                        {{ $seller->created_at->format('d M Y') }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td style="padding: 14px 20px; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">

                                        {{-- Setujui --}}
                                        <form method="POST"
                                              action="{{ route('admin.sellers.verify', $seller) }}"
                                              onsubmit="return confirm('Setujui seller {{ addslashes($seller->business_name) }}?')">
                                            @csrf
                                            <button type="submit"
                                                    style="display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: #22c55e; color: #fff; min-height: 36px; transition: background 0.15s;"
                                                    onmouseover="this.style.background='#16a34a'"
                                                    onmouseout="this.style.background='#22c55e'">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>

                                        {{-- Tolak --}}
                                        <form method="POST"
                                              action="{{ route('admin.sellers.reject', $seller) }}"
                                              onsubmit="return confirm('Tolak dan hapus seller {{ addslashes($seller->business_name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    style="display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; background: #DD3015; color: #fff; min-height: 36px; transition: background 0.15s;"
                                                    onmouseover="this.style.background='#F30000'"
                                                    onmouseout="this.style.background='#DD3015'">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Tolak
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>

</div>

{{-- Background page --}}
@push('styles')
<style>
    body, .admin-content-wrapper {
        background-color: #F3E1E1 !important;
    }
</style>
@endpush

@endsection