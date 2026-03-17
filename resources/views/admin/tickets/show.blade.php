@extends('layouts.app')
@section('title', 'Detail Tiket #' . $ticket->ticket_number)
@section('page-title', 'Detail Tiket')

@section('content')
<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

    {{-- ── Left Column ────────────────────────────────────────────── --}}
    <div>

        {{-- Ticket Header --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <div>
                    <span style="font-family:var(--font-mono);font-size:.8rem;color:var(--gray-400);">
                        {{ $ticket->ticket_number }}
                    </span>
                    <h2 style="font-size:1.1rem;font-weight:700;color:var(--gray-900);margin-top:4px;">
                        {{ $ticket->subject }}
                    </h2>
                </div>
                <div style="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;">
                    <span class="badge {{ $ticket->priority_badge }}">
                        <i class="fa-solid {{ $ticket->priority_icon }}"></i>
                        {{ ucfirst($ticket->priority) }}
                    </span>
                    <span class="badge {{ $ticket->status_badge }}">
                        {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <img src="{{ $ticket->user->avatar_url }}" alt=""
                         style="width:38px;height:38px;border-radius:50%;object-fit:cover;">
                    <div>
                        <div style="font-weight:600;font-size:.9rem;">{{ $ticket->user->name }}</div>
                        <div style="font-size:.78rem;color:var(--gray-400);">
                            {{ $ticket->user->email }}
                            @if($ticket->user->department) · {{ $ticket->user->department }} @endif
                            · {{ $ticket->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
                <div style="background:var(--gray-50);border-radius:var(--radius-sm);padding:16px;font-size:.9rem;color:var(--gray-700);white-space:pre-wrap;line-height:1.7;">
                    {{ $ticket->description }}
                </div>

                {{-- Attachments on original ticket --}}
                @if($ticket->attachments->isNotEmpty())
                <div style="margin-top:14px;">
                    <p style="font-size:.8rem;font-weight:600;color:var(--gray-500);margin-bottom:8px;">
                        <i class="fa-solid fa-paperclip"></i> Lampiran
                    </p>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach($ticket->attachments as $att)
                        <a href="{{ $att->url }}" target="_blank"
                           style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:var(--gray-100);border-radius:var(--radius-sm);font-size:.8rem;color:var(--gray-700);text-decoration:none;transition:background var(--transition);"
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
            <div class="card-body" style="padding-top:16px;">
                @forelse($ticket->replies as $reply)
                @php $isAgent = $reply->user->isAgent(); @endphp
                <div class="reply-bubble {{ $isAgent ? 'is-agent' : '' }} {{ $reply->is_internal ? 'is-internal' : '' }}">
                    <div class="reply-header">
                        <img src="{{ $reply->user->avatar_url }}" alt="" class="reply-avatar">
                        <div>
                            <div class="reply-name">{{ $reply->user->name }}</div>
                            <div style="font-size:.72rem;color:var(--gray-400);">
                                {{ ucfirst($reply->user->role) }}
                                @if($reply->is_internal)
                                <span class="badge badge-warning" style="margin-left:4px;font-size:.65rem;padding:1px 6px;">
                                    <i class="fa-solid fa-eye-slash"></i> Catatan Internal
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="reply-time">{{ $reply->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="reply-message">{{ $reply->message }}</div>
                </div>
                @empty
                <div class="empty-state" style="padding:30px;">
                    <i class="fa-solid fa-comment-slash"></i>
                    <p>Belum ada percakapan</p>
                </div>
                @endforelse

                {{-- Reply Form --}}
                @if(!in_array($ticket->status, ['closed']))
                <div style="margin-top:20px;border-top:1px solid var(--gray-200);padding-top:20px;">
                    <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Balas Tiket</label>
                            <textarea name="message" class="form-control" rows="4"
                                      placeholder="Tulis balasan untuk pengguna..." required></textarea>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                            <label style="display:flex;align-items:center;gap:8px;font-size:.85rem;cursor:pointer;">
                                <input type="checkbox" name="is_internal" value="1">
                                <i class="fa-solid fa-eye-slash" style="color:var(--warning);"></i>
                                Catatan Internal (tidak terlihat pengguna)
                            </label>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Balasan
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>

        {{-- Activity Log --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-clock-rotate-left" style="color:var(--gray-400);margin-right:6px;"></i>
                    Log Aktivitas
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
    </div>

    {{-- ── Right Column ────────────────────────────────────────────── --}}
    <div>
        {{-- Update Ticket Form --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-sliders" style="color:var(--primary);margin-right:6px;"></i>Kelola Tiket</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            @foreach(['open'=>'Terbuka','in_progress'=>'Sedang Diproses','pending'=>'Pending','resolved'=>'Selesai','closed'=>'Ditutup'] as $val => $label)
                            <option value="{{ $val }}" {{ $ticket->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Prioritas</label>
                        <select name="priority" class="form-control">
                            @foreach(['low'=>'Rendah','medium'=>'Sedang','high'=>'Tinggi','critical'=>'Kritis'] as $val => $label)
                            <option value="{{ $val }}" {{ $ticket->priority === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $ticket->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tugaskan ke Agent</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">— Belum Ditugaskan —</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }} ({{ ucfirst($agent->role) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan Resolusi</label>
                        <textarea name="resolution_notes" class="form-control" rows="3"
                                  placeholder="Catatan penyelesaian tiket...">{{ $ticket->resolution_notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        {{-- Ticket Info --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-circle-info" style="color:var(--gray-400);margin-right:6px;"></i>Informasi Tiket</span>
            </div>
            <div class="card-body" style="padding:0;">
                @php
                    $infoItems = [
                        ['label'=>'Nomor Tiket',  'value'=>$ticket->ticket_number, 'mono'=>true],
                        ['label'=>'Dibuat',        'value'=>$ticket->created_at->format('d M Y, H:i')],
                        ['label'=>'Diperbarui',    'value'=>$ticket->updated_at->diffForHumans()],
                        ['label'=>'Selesai',       'value'=>$ticket->resolved_at?->format('d M Y, H:i') ?? '—'],
                        ['label'=>'Ditutup',       'value'=>$ticket->closed_at?->format('d M Y, H:i') ?? '—'],
                        ['label'=>'Pemohon',       'value'=>$ticket->user->name],
                        ['label'=>'Dept.',         'value'=>$ticket->user->department ?? '—'],
                        ['label'=>'Agent',         'value'=>$ticket->assignedAgent?->name ?? 'Belum ditugaskan'],
                    ];
                @endphp
                @foreach($infoItems as $item)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 20px;border-bottom:1px solid var(--gray-100);font-size:.83rem;">
                    <span style="color:var(--gray-500);">{{ $item['label'] }}</span>
                    <span style="font-weight:600;{{ isset($item['mono']) ? 'font-family:var(--font-mono);' : '' }}color:var(--gray-800);">
                        {{ $item['value'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media(max-width:900px){
    .ticket-detail-grid{grid-template-columns:1fr!important;}
}
</style>
@endpush
@endsection
