@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Overview')

@section('content')
{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fa-solid fa-ticket"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_tickets'] }}</div>
            <div class="stat-label">Total Tiket</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-folder-open"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['open_tickets'] }}</div>
            <div class="stat-label">Tiket Terbuka</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info"><i class="fa-solid fa-spinner"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['in_progress'] }}</div>
            <div class="stat-label">Sedang Diproses</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['resolved_today'] }}</div>
            <div class="stat-label">Diselesaikan Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger"><i class="fa-solid fa-fire"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['critical'] }}</div>
            <div class="stat-label">Tiket Kritis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon dark"><i class="fa-solid fa-user-slash"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['unassigned'] }}</div>
            <div class="stat-label">Belum Ditugaskan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Total Pengguna</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="fa-solid fa-user-tie"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_agents'] }}</div>
            <div class="stat-label">Total Agent</div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:24px;">
    {{-- Ticket Trend (7 hari) --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-chart-line" style="color:var(--primary);margin-right:6px;"></i>Tren Tiket (7 Hari Terakhir)</span>
        </div>
        <div class="card-body">
            @php $maxTrend = max(array_column($trend, 'count')) ?: 1; @endphp
            <div class="chart-bar-wrap">
                @foreach($trend as $t)
                <div class="chart-bar-item">
                    <div class="chart-bar-label">
                        <span>{{ $t['date'] }}</span>
                        <span style="font-weight:600;color:var(--primary)">{{ $t['count'] }} tiket</span>
                    </div>
                    <div class="chart-bar-track">
                        <div class="chart-bar-fill"
                             style="width:{{ $maxTrend ? round(($t['count']/$maxTrend)*100) : 0 }}%;background:var(--primary);"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Status & Priority Breakdown --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-chart-pie" style="color:var(--secondary);margin-right:6px;"></i>Distribusi Status & Prioritas</span>
        </div>
        <div class="card-body">
            <p style="font-size:.8rem;font-weight:600;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Berdasarkan Status</p>
            @php
                $statusColors = ['open'=>'var(--primary)','in_progress'=>'var(--warning)','pending'=>'var(--gray-400)','resolved'=>'var(--success)','closed'=>'var(--gray-700)'];
                $statusLabels = ['open'=>'Terbuka','in_progress'=>'Diproses','pending'=>'Pending','resolved'=>'Selesai','closed'=>'Ditutup'];
                $totalByStatus = array_sum($ticketsByStatus) ?: 1;
            @endphp
            <div class="chart-bar-wrap" style="margin-bottom:20px;">
                @foreach($ticketsByStatus as $s => $count)
                <div class="chart-bar-item">
                    <div class="chart-bar-label">
                        <span>{{ $statusLabels[$s] }}</span>
                        <span style="font-weight:600;">{{ $count }}</span>
                    </div>
                    <div class="chart-bar-track">
                        <div class="chart-bar-fill"
                             style="width:{{ round(($count/$totalByStatus)*100) }}%;background:{{ $statusColors[$s] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <p style="font-size:.8rem;font-weight:600;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Berdasarkan Prioritas</p>
            @php
                $prioColors  = ['low'=>'var(--success)','medium'=>'var(--secondary)','high'=>'var(--warning)','critical'=>'var(--danger)'];
                $prioLabels  = ['low'=>'Rendah','medium'=>'Sedang','high'=>'Tinggi','critical'=>'Kritis'];
                $totalByPrio = array_sum($ticketsByPriority) ?: 1;
            @endphp
            <div class="chart-bar-wrap">
                @foreach($ticketsByPriority as $p => $count)
                <div class="chart-bar-item">
                    <div class="chart-bar-label">
                        <span>{{ $prioLabels[$p] }}</span>
                        <span style="font-weight:600;">{{ $count }}</span>
                    </div>
                    <div class="chart-bar-track">
                        <div class="chart-bar-fill"
                             style="width:{{ round(($count/$totalByPrio)*100) }}%;background:{{ $prioColors[$p] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="grid-2">
    {{-- Recent Tickets --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--warning);margin-right:6px;"></i>Tiket Terbaru</span>
            <a href="{{ route('admin.tickets') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
        </div>
        <div style="overflow:hidden;">
            @forelse($recentTickets as $ticket)
            <a href="{{ route('admin.tickets.show', $ticket) }}" class="ticket-item">
                <span class="ticket-priority-dot priority-{{ $ticket->priority }}"></span>
                <div class="ticket-info">
                    <div class="ticket-number">{{ $ticket->ticket_number }}</div>
                    <div class="ticket-subject">{{ $ticket->subject }}</div>
                    <div class="ticket-meta">
                        <span><i class="fa-solid fa-user"></i> {{ $ticket->user->name }}</span>
                        <span><i class="fa-solid fa-clock"></i> {{ $ticket->created_at->diffForHumans() }}</span>
                        @if($ticket->category)
                        <span><i class="fa-solid fa-tag"></i> {{ $ticket->category->name }}</span>
                        @endif
                    </div>
                </div>
                <div class="ticket-status-col">
                    <span class="badge {{ $ticket->status_badge }}">{{ str_replace('_',' ',$ticket->status) }}</span>
                </div>
            </a>
            @empty
            <div class="empty-state">
                <i class="fa-solid fa-ticket-simple"></i>
                <p>Belum ada tiket</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Top Agents --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-trophy" style="color:var(--warning);margin-right:6px;"></i>Performa Agent</span>
        </div>
        <div class="card-body">
            @forelse($topAgents as $i => $agent)
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <span style="width:28px;height:28px;border-radius:50%;background:var(--gray-100);display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:var(--gray-600);flex-shrink:0;">
                    {{ $i+1 }}
                </span>
                <img src="{{ $agent->avatar_url }}" alt="{{ $agent->name }}"
                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:.875rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $agent->name }}
                    </div>
                    <div style="font-size:.75rem;color:var(--gray-500);">{{ $agent->department ?? 'IT Support' }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:700;font-size:1rem;color:var(--success);">{{ $agent->resolved_count }}</div>
                    <div style="font-size:.72rem;color:var(--gray-400);">diselesaikan</div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fa-solid fa-user-tie"></i>
                <p>Belum ada agent</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
