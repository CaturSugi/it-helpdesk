@extends('layouts.app')
@section('title', 'Tiket Saya')
@section('page-title', 'Tiket Saya')

@section('content')

{{-- Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fa-solid fa-ticket"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Tiket</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-folder-open"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['open'] }}</div>
            <div class="stat-label">Terbuka</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info"><i class="fa-solid fa-spinner"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['in_progress'] }}</div>
            <div class="stat-label">Diproses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['resolved'] }}</div>
            <div class="stat-label">Selesai</div>
        </div>
    </div>
</div>

<div class="card">
    {{-- Filter --}}
    <form method="GET" class="filter-bar">
        <div class="input-group search-input">
            <i class="fa-solid fa-search input-group-icon"></i>
            <input type="text" name="search" class="form-control"
                   placeholder="Cari tiket..."
                   value="{{ request('search') }}">
        </div>
        <select name="status" class="form-control" style="max-width:160px;">
            <option value="">Semua Status</option>
            @foreach(['open'=>'Terbuka','in_progress'=>'Diproses','pending'=>'Pending','resolved'=>'Selesai','closed'=>'Ditutup'] as $val => $label)
            <option value="{{ $val }}" {{ request('status')===$val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="priority" class="form-control" style="max-width:160px;">
            <option value="">Semua Prioritas</option>
            @foreach(['low'=>'Rendah','medium'=>'Sedang','high'=>'Tinggi','critical'=>'Kritis'] as $val => $label)
            <option value="{{ $val }}" {{ request('priority')===$val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        @if(request()->hasAny(['search','status','priority']))
        <a href="{{ route('tickets.index') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-xmark"></i> Reset
        </a>
        @endif
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm" style="margin-left:auto;">
            <i class="fa-solid fa-plus"></i> Tiket Baru
        </a>
    </form>

    {{-- Ticket List --}}
    @forelse($tickets as $ticket)
    <a href="{{ route('tickets.show', $ticket) }}" class="ticket-item">
        <span class="ticket-priority-dot priority-{{ $ticket->priority }}"></span>
        <div class="ticket-info">
            <div class="ticket-number">{{ $ticket->ticket_number }}</div>
            <div class="ticket-subject">{{ $ticket->subject }}</div>
            <div class="ticket-meta">
                @if($ticket->category)
                <span><i class="fa-solid fa-tag"></i> {{ $ticket->category->name }}</span>
                @endif
                <span><i class="fa-solid fa-calendar"></i> {{ $ticket->created_at->format('d M Y') }}</span>
                <span><i class="fa-solid fa-clock"></i> {{ $ticket->created_at->diffForHumans() }}</span>
                @if($ticket->assignedAgent)
                <span><i class="fa-solid fa-user-tie"></i> {{ $ticket->assignedAgent->name }}</span>
                @else
                <span style="color:var(--warning);"><i class="fa-solid fa-user-clock"></i> Menunggu agent</span>
                @endif
                <span>
                    <i class="fa-solid fa-comments"></i>
                    {{ $ticket->replies_count ?? $ticket->replies->count() }} balasan
                </span>
            </div>
        </div>
        <div class="ticket-status-col">
            <span class="badge {{ $ticket->status_badge }}" style="margin-bottom:6px;display:block;">
                {{ str_replace('_',' ', ucfirst($ticket->status)) }}
            </span>
            <span class="badge {{ $ticket->priority_badge }}" style="display:block;text-align:center;">
                <i class="fa-solid {{ $ticket->priority_icon }}"></i>
                {{ ucfirst($ticket->priority) }}
            </span>
        </div>
    </a>
    @empty
    <div class="empty-state" style="padding:60px;">
        <i class="fa-solid fa-ticket-simple"></i>
        <p>Anda belum memiliki tiket</p>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary" style="margin-top:14px;">
            <i class="fa-solid fa-plus"></i> Buat Tiket Pertama
        </a>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--gray-100);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <span style="font-size:.83rem;color:var(--gray-500);">
                Menampilkan {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} dari {{ $tickets->total() }}
            </span>
            {{ $tickets->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
