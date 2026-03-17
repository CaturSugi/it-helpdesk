@extends('layouts.app')
@section('title', 'Tiket #' . $ticket->ticket_number)
@section('page-title', 'Detail Tiket')

@section('content')
<div style="max-width:860px;margin:0 auto;">

    {{-- Ticket Header Card --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header" style="flex-wrap:wrap;gap:12px;">
            <div style="flex:1;min-width:0;">
                <div style="font-family:var(--font-mono);font-size:.78rem;color:var(--gray-400);margin-bottom:4px;">
                    {{ $ticket->ticket_number }}
                </div>
                <h1 style="font-size:1.1rem;font-weight:700;color:var(--gray-900);">{{ $ticket->subject }}</h1>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0;align-items:center;flex-wrap:wrap;">
                <span class="badge {{ $ticket->priority_badge }}">
                    <i class="fa-solid {{ $ticket->priority_icon }}"></i> {{ ucfirst($ticket->priority) }}
                </span>
                <span class="badge {{ $ticket->status_badge }}">
                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                </span>
                @if(!in_array($ticket->status, ['closed','resolved']))
                <form method="POST" action="{{ route('tickets.close', $ticket) }}"
                      onsubmit="return confirm('Tutup tiket ini?')">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm" style="color:var(--gray-500);">
                        <i class="fa-solid fa-xmark"></i> Tutup Tiket
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Meta info bar --}}
        <div style="padding:12px 24px;background:var(--gray-50);border-bottom:1px solid var(--gray-200);display:flex;flex-wrap:wrap;gap:16px;font-size:.8rem;color:var(--gray-500);">
            <span><i class="fa-solid fa-calendar-plus"></i> Dibuat {{ $ticket->created_at->format('d M Y, H:i') }}</span>
            @if($ticket->category)
            <span><i class="fa-solid fa-tag"></i> {{ $ticket->category->name }}</span>
            @endif
            @if($ticket->assignedAgent)
            <span><i class="fa-solid fa-user-tie"></i> Ditangani oleh {{ $ticket->assignedAgent->name }}</span>
            @else
            <span style="color:var(--warning);"><i class="fa-solid fa-user-clock"></i> Menunggu penugasan agent</span>
            @endif
            <span><i class="fa-solid fa-clock"></i> Diperbarui {{ $ticket->updated_at->diffForHumans() }}</span>
        </div>

        {{-- Original description --}}
        <div class="card-body">
            <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:16px;font-size:.9rem;color:var(--gray-700);white-space:pre-wrap;line-height:1.7;">
                {{ $ticket->description }}
            </div>

            @if($ticket->attachments->isNotEmpty())
            <div style="margin-top:14px;">
                <p style="font-size:.8rem;font-weight:600;color:var(--gray-500);margin-bottom:8px;">
                    <i class="fa-solid fa-paperclip"></i> Lampiran
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach($ticket->attachments as $att)
                    <a href="{{ $att->url }}" target="_blank"
                       style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:var(--gray-100);border-radius:var(--radius-sm);font-size:.8rem;color:var(--gray-700);text-decoration:none;"
                       onmouseover="this.style.background='var(--primary-light)'"
                       onmouseout="this.style.background='var(--gray-100)'">
                        <i class="fa-solid fa-file"></i>
                        {{ $att->original_name }}
                        <span style="color:var(--gray-400);">({{ $att->formatted_size }})</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Replies --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-comments" style="color:var(--secondary);margin-right:6px;"></i>
                Percakapan ({{ $ticket->replies->count() }})
            </span>
        </div>
        <div class="card-body">
            @forelse($ticket->replies->filter(fn($r) => !$r->is_internal) as $reply)
            @php $isAgent = $reply->user->isAgent(); $isMe = $reply->user_id === auth()->id(); @endphp
            <div class="reply-bubble {{ $isAgent ? 'is-agent' : '' }}" style="margin-bottom:14px;">
                <div class="reply-header">
                    <img src="{{ $reply->user->avatar_url }}" alt="" class="reply-avatar">
                    <div>
                        <div class="reply-name">
                            {{ $reply->user->name }}
                            @if($isMe) <span style="font-size:.7rem;color:var(--primary);font-weight:400;">(Anda)</span> @endif
                        </div>
                        <div style="font-size:.72rem;color:var(--gray-400);">
                            {{ $isAgent ? 'IT Support Team' : 'Pengguna' }}
                        </div>
                    </div>
                    <div class="reply-time">{{ $reply->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="reply-message">{{ $reply->message }}</div>
            </div>
            @empty
            <div class="empty-state" style="padding:30px;">
                <i class="fa-solid fa-comment-dots"></i>
                <p>Belum ada balasan. Tim IT Support akan segera merespons tiket Anda.</p>
            </div>
            @endforelse

            {{-- Reply Form --}}
            @if(!in_array($ticket->status, ['closed']))
            <div style="margin-top:20px;border-top:1px solid var(--gray-200);padding-top:20px;">
                <form method="POST" action="{{ route('tickets.reply', $ticket) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Tambah Komentar / Informasi Tambahan</label>
                        <textarea name="message" class="form-control" rows="4"
                                  placeholder="Tulis informasi tambahan atau pertanyaan lanjutan..." required></textarea>
                    </div>
                    <div style="text-align:right;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane"></i> Kirim
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="alert alert-info" style="margin-top:16px;">
                <i class="fa-solid fa-lock"></i>
                Tiket ini sudah ditutup. Buat tiket baru jika masalah belum terselesaikan.
            </div>
            @endif
        </div>
    </div>

    {{-- Activity Timeline --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-clock-rotate-left" style="color:var(--gray-400);margin-right:6px;"></i>
                Riwayat Aktivitas
            </span>
        </div>
        <div class="card-body">
            <div class="timeline">
                @foreach($ticket->activities as $act)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">{{ $act->description }}</div>
                    <div class="timeline-date">{{ $act->created_at->format('d M Y, H:i') }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="margin-top:16px;">
        <a href="{{ route('tickets.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Tiket
        </a>
    </div>
</div>
@endsection
