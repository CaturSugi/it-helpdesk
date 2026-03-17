@extends('layouts.app')
@section('title', 'Manajemen Kategori')
@section('page-title', 'Manajemen Kategori')

@section('content')
<div class="grid-2">
    {{-- Category List --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-tags" style="color:var(--primary);margin-right:6px;"></i>Daftar Kategori</span>
        </div>
        @if($categories->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-tags"></i>
            <p>Belum ada kategori</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Total Tiket</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="width:36px;height:36px;border-radius:var(--radius-sm);background:{{ $cat->color }}20;display:flex;align-items:center;justify-content:center;font-size:.9rem;color:{{ $cat->color }};">
                                    <i class="fa-solid {{ $cat->icon }}"></i>
                                </span>
                                <div>
                                    <div style="font-weight:600;">{{ $cat->name }}</div>
                                    @if($cat->description)
                                    <div style="font-size:.78rem;color:var(--gray-400);">{{ $cat->description }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $cat->tickets_count }} tiket</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.categories.delete', $cat) }}"
                                  onsubmit="return confirm('Hapus kategori {{ $cat->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-sm"
                                        style="color:var(--danger);border-color:var(--danger);">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Add Category Form --}}
    <div class="card" style="align-self:start;">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-plus" style="color:var(--success);margin-right:6px;"></i>Tambah Kategori</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Kategori <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control"
                           placeholder="Contoh: Hardware, Network, Software..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">Ikon (Font Awesome class)</label>
                    <div class="input-group">
                        <i class="fa-solid fa-icons input-group-icon"></i>
                        <input type="text" name="icon" class="form-control"
                               value="fa-tag" placeholder="fa-tag" id="iconInput">
                    </div>
                    <div class="form-hint">
                        Gunakan class FA seperti: fa-desktop, fa-wifi, fa-bug, fa-print, fa-database
                    </div>
                    <div style="margin-top:8px;padding:10px;background:var(--gray-50);border-radius:var(--radius-sm);text-align:center;">
                        <i id="iconPreview" class="fa-solid fa-tag" style="font-size:1.5rem;color:var(--primary);"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Warna</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="color" name="color" id="colorPicker" value="#6366f1"
                               style="width:48px;height:38px;border:1px solid var(--gray-300);border-radius:var(--radius-sm);cursor:pointer;padding:2px;">
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            @foreach(['#6366f1','#0ea5e9','#22c55e','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316'] as $color)
                            <button type="button"
                                    style="width:28px;height:28px;border-radius:50%;background:{{ $color }};border:2px solid transparent;cursor:pointer;transition:border-color .2s;"
                                    onclick="document.getElementById('colorPicker').value='{{ $color }}'"
                                    onmouseover="this.style.borderColor='#000'"
                                    onmouseout="this.style.borderColor='transparent'">
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="2"
                              placeholder="Deskripsi singkat kategori..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-plus"></i> Tambah Kategori
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('iconInput').addEventListener('input', function() {
    const preview = document.getElementById('iconPreview');
    preview.className = 'fa-solid ' + this.value;
});
</script>
@endpush
@endsection
