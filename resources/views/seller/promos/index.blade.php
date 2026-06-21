@extends('layouts.seller')

@section('title', 'Promo Saya - Promora')

@section('content')
<div style="background:#F3E1E1; min-height:100vh; padding:1.5rem; box-sizing:border-box;">

    {{-- Page Header --}}
    <div class="pm-header">
        <div>
            <p class="pm-eyebrow">Dashboard Seller</p>
            <h1 class="pm-title">Promo Saya</h1>
            <p class="pm-sub">Kelola semua promo bisnis Anda</p>
        </div>
        <a href="{{ route('seller.promos.create') }}" class="pm-btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Promo
        </a>
    </div>

    {{-- Stats Bar --}}
    @unless($promos->isEmpty())
    <div class="pm-stat-bar">
        <div class="pm-stat">
            <div class="pm-stat-label">Total Promo</div>
            <div class="pm-stat-val">{{ $promos->count() }} <span>promo</span></div>
            <div class="pm-accent-bar"></div>
        </div>
        <div class="pm-stat">
            <div class="pm-stat-label">Aktif</div>
            <div class="pm-stat-val" style="color:#15803d">{{ $promos->where('status','active')->count() }}</div>
            <div class="pm-accent-bar" style="background:#22c55e"></div>
        </div>
        <div class="pm-stat">
            <div class="pm-stat-label">Draft</div>
            <div class="pm-stat-val" style="color:#92620a">{{ $promos->where('status','draft')->count() }}</div>
            <div class="pm-accent-bar" style="background:#FFB800"></div>
        </div>
        <div class="pm-stat">
            <div class="pm-stat-label">Total Tayangan</div>
            <div class="pm-stat-val">{{ number_format($promos->sum('view_count')) }}</div>
            <div class="pm-accent-bar"></div>
        </div>
    </div>
    @endunless

    {{-- Table Card --}}
    <div class="pm-card">
        @if($promos->isEmpty())
            {{-- Empty State --}}
            <div class="pm-empty">
                <div class="pm-empty-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#DD3015" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <h3 style="font-size:16px; font-weight:700; color:#1a1a1a; margin:0 0 6px;">Belum ada promo</h3>
                <p style="font-size:13px; color:#7a5a5a; margin:0 0 1.25rem;">Mulai buat promo pertama Anda untuk menarik lebih banyak pelanggan.</p>
                <a href="{{ route('seller.promos.create') }}" class="pm-btn-primary" style="display:inline-flex;">
                    Buat Promo Pertama
                </a>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="pm-table">
                    <thead>
                        <tr>
                            <th>Promo</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Diskon</th>
                            <th>Berakhir</th>
                            <th>Tayangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promos as $promo)
                        <tr>
                            <td>
                                <div class="pm-promo-cell">
                                    @if($promo->poster_image)
                                        <div class="pm-thumb">
                                            <img src="{{ $promo->poster_url }}" alt="{{ $promo->title }}">
                                        </div>
                                    @else
                                        <div class="pm-thumb">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DD3015" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="pm-promo-name">{{ $promo->title }}</span>
                                </div>
                            </td>
                            <td class="pm-cat">{{ $promo->category->name ?? '-' }}</td>
                            <td>
                                @if($promo->status === 'active')
                                    <span class="pm-badge pm-badge-active">
                                        <span class="pm-dot pm-dot-active"></span>Aktif
                                    </span>
                                @elseif($promo->status === 'draft')
                                    <span class="pm-badge pm-badge-draft">
                                        <span class="pm-dot pm-dot-draft"></span>Draft
                                    </span>
                                @else
                                    <span class="pm-badge pm-badge-exp">
                                        <span class="pm-dot pm-dot-exp"></span>Kadaluarsa
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($promo->discount_percentage)
                                    <span class="pm-discount">{{ $promo->discount_percentage }}%</span>
                                @else
                                    <span class="pm-cat">-</span>
                                @endif
                            </td>
                            <td class="pm-date">{{ $promo->end_date->format('d M Y') }}</td>
                            <td>
                                <span class="pm-views">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9a6a6a" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    {{ number_format($promo->view_count) }}
                                </span>
                            </td>
                            <td>
                                <div class="pm-actions">
                                    <a href="{{ route('seller.promos.edit', $promo) }}" class="pm-btn-edit">Edit</a>
                                    <form method="POST" action="{{ route('seller.promos.destroy', $promo) }}"
                                          onsubmit="return confirm('Hapus promo ini?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="pm-btn-del">Hapus</button>
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

<style>
/* ── Layout ── */
.pm-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}

/* ── Typography ── */
.pm-eyebrow {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #DD3015;
    margin: 0 0 4px;
}
.pm-title {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 2px;
}
.pm-sub {
    font-size: 13px;
    color: #7a5a5a;
    margin: 0;
}

/* ── CTA Button ── */
.pm-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    background: #DD3015;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s;
    white-space: nowrap;
    min-height: 44px;
}
.pm-btn-primary:hover { background: #F30000; }

/* ── Stat Bar ── */
.pm-stat-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}
.pm-stat {
    flex: 1;
    min-width: 110px;
    background: #fff;
    border-radius: 12px;
    padding: 12px 14px;
    border: 1px solid rgba(221,48,21,.1);
}
.pm-stat-label {
    font-size: 10.5px;
    color: #9a6a6a;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 4px;
}
.pm-stat-val {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
}
.pm-stat-val span {
    font-size: 13px;
    color: #DD3015;
    font-weight: 600;
}
.pm-accent-bar {
    height: 3px;
    background: #DD3015;
    border-radius: 2px;
    width: 28px;
    margin-top: 4px;
}

/* ── Main Card ── */
.pm-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(221,48,21,.12);
    overflow: hidden;
}

/* ── Table ── */
.pm-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.pm-table thead th {
    background: #FFF5F3;
    padding: 10px 16px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #DD3015;
    border-bottom: 1.5px solid rgba(221,48,21,.15);
    white-space: nowrap;
}
.pm-table tbody tr {
    border-bottom: 1px solid #fceae7;
    transition: background .1s;
}
.pm-table tbody tr:last-child { border-bottom: none; }
.pm-table tbody tr:hover { background: #fff8f7; }
.pm-table tbody td {
    padding: 12px 16px;
    color: #3a2a2a;
    vertical-align: middle;
}

/* ── Table Cell Components ── */
.pm-promo-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}
.pm-thumb {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #FFF0ED;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
}
.pm-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.pm-promo-name {
    font-weight: 600;
    color: #1a1a1a;
    font-size: 13px;
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pm-cat { color: #7a5a5a; font-size: 12px; }
.pm-date { color: #5a3a3a; font-size: 12px; }
.pm-discount { font-weight: 700; color: #DD3015; font-size: 13px; }
.pm-views {
    color: #5a3a3a;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* ── Badges ── */
.pm-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.pm-badge-active { background: #e6f9f0; color: #15803d; }
.pm-badge-draft   { background: #FFF8E1; color: #92620a; }
.pm-badge-exp     { background: #fce8e8; color: #b91c1c; }
.pm-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.pm-dot-active { background: #22c55e; }
.pm-dot-draft  { background: #FFB800; }
.pm-dot-exp    { background: #F30000; }

/* ── Row Actions ── */
.pm-actions { display: flex; gap: 6px; }
.pm-btn-edit {
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    color: #1d4ed8;
    background: #eff6ff;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    min-height: 32px;
    transition: background .12s;
}
.pm-btn-edit:hover { background: #dbeafe; }
.pm-btn-del {
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    color: #b91c1c;
    background: #fce8e8;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    min-height: 32px;
    transition: background .12s;
}
.pm-btn-del:hover { background: #fee2e2; }

/* ── Empty State ── */
.pm-empty { padding: 3rem; text-align: center; }
.pm-empty-icon {
    width: 56px;
    height: 56px;
    background: #FFF0ED;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

/* ── Responsive ── */
@media (max-width: 640px) {
    .pm-header { flex-direction: column; }
    .pm-btn-primary { width: 100%; justify-content: center; }
    .pm-stat { min-width: calc(50% - 5px); }
    .pm-promo-name { max-width: 120px; }
}
</style>
@endsection