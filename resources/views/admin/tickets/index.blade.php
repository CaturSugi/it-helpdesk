@extends('layouts.app')
@section('title', 'Manajemen Tiket')
@section('page-title', 'Manajemen Tiket')

@section('content')
<div class="card">
    {{-- Filter Bar --}}
    <form method="GET" class="filter-bar">
        <div class="input-group search-input">
            <i class="fa-solid fa-search input-group-icon"></i>
            <input type="text" name="search" class="form-control"
                   placeholder="Cari tiket atau nama pengguna..."
                   value="{{ request('search') }}">
        </div>

        <select name="status" class="form-control" style="max-width:160px;">
            <option value="">Semua Status</option>
            @foreach(['open'=>'Terbuka','in_progress'=>'Diproses','pending'=>'Pending','resolved'=>'Selesai','closed'=>'Ditutup'] as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="priority" class="form-control" style="max-width:160px;">
            <option value="">Semua Prioritas</option>
            @foreach(['low'=>'Rendah','medium'=>'Sedang','high'=>'Tinggi','critical'=>'Kritis'] as $val => $label)
            <option value="{{ $val }}" {{ request('priority') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="category" class="form-control" style="max-width:160px;">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="agent" class="form-control" style="max-width:160px;">
            <option value="">Semua Agent</option>
            @foreach($agents as $agent)
            <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        @if(request()->hasAny(['search','status','priority','category','agent']))
        <a href="{{ route('admin.tickets') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-xmark"></i> Reset
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Subjek</th>
                    <th>Pemohon</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Agent</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td>
                        <span style="font-family:var(--font-mono);font-size:.8rem;color:var(--gray-500);">
                            {{ $ticket->ticket_number }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $ticket->subject }}
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <img src="{{ $ticket->user->avatar_url }}" alt=""
                                 style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                            <span style="font-size:.85rem;">{{ $ticket->user->name }}</span>
                        </div>
                    </td>
                    <td>
                        @if($ticket->category)
                        <span class="badge badge-secondary">{{ $ticket->category->name }}</span>
                        @else
                        <span style="color:var(--gray-400);font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $ticket->priority_badge }}">
                            <i class="fa-solid {{ $ticket->priority_icon }}"></i>
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $ticket->status_badge }}">
                            {{ str_replace('_',' ', ucfirst($ticket->status)) }}
                        </span>
                    </td>
                    <td>
                        @if($ticket->assignedAgent)
                        <div style="display:flex;align-items:center;gap:6px;">
                            <img src="{{ $ticket->assignedAgent->avatar_url }}" alt=""
                                 style="width:24px;height:24px;border-radius:50%;object-fit:cover;">
                            <span style="font-size:.83rem;">{{ $ticket->assignedAgent->name }}</span>
                        </div>
                        @else
                        <span style="color:var(--gray-400);font-size:.8rem;"><i class="fa-solid fa-user-slash"></i> Belum</span>
                        @endif
                    </td>
                    <td style="font-size:.8rem;color:var(--gray-500);">
                        {{ $ticket->created_at->format('d M Y') }}<br>
                        <span style="font-size:.73rem;">{{ $ticket->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fa-solid fa-ticket-simple"></i>
                            <p>Tidak ada tiket ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--gray-100);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <span style="font-size:.83rem;color:var(--gray-500);">
                Menampilkan {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} dari {{ $tickets->total() }} tiket
            </span>
            {{ $tickets->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
