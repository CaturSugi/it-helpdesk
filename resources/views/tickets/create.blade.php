@extends('layouts.app')
@section('title', 'Buat Tiket Baru')
@section('page-title', 'Buat Tiket Baru')

@section('content')
<div style="max-width:760px;margin:0 auto;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-ticket" style="color:var(--primary);margin-right:8px;"></i>
                Form Pengajuan Tiket IT Support
            </span>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Judul / Subjek Masalah <span class="required">*</span></label>
                    <input type="text" name="subject" class="form-control"
                           placeholder="Ringkasan singkat masalah Anda..."
                           value="{{ old('subject') }}" required>
                    <div class="form-hint">Tulis judul yang jelas dan spesifik agar tim IT dapat langsung memahami.</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tingkat Urgensi <span class="required">*</span></label>
                        <div class="priority-options">
                            @foreach([
                                'low'      => ['label'=>'Rendah',  'icon'=>'fa-arrow-down',  'color'=>'var(--success)'],
                                'medium'   => ['label'=>'Sedang',  'icon'=>'fa-minus',        'color'=>'var(--secondary)'],
                                'high'     => ['label'=>'Tinggi',  'icon'=>'fa-arrow-up',     'color'=>'var(--warning)'],
                                'critical' => ['label'=>'Kritis',  'icon'=>'fa-fire',         'color'=>'var(--danger)'],
                            ] as $val => $opt)
                            <div class="priority-radio">
                                <input type="radio" name="priority" id="p_{{ $val }}" value="{{ $val }}"
                                       {{ old('priority','medium') === $val ? 'checked' : '' }}>
                                <label for="p_{{ $val }}" style="--pcolor:{{ $opt['color'] }};">
                                    <i class="fa-solid {{ $opt['icon'] }}" style="color:{{ $opt['color'] }};"></i>
                                    {{ $opt['label'] }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Masalah <span class="required">*</span></label>
                    <textarea name="description" class="form-control" rows="6"
                              placeholder="Jelaskan masalah Anda secara detail:&#10;- Apa yang terjadi?&#10;- Kapan masalah mulai terjadi?&#10;- Langkah-langkah yang sudah dicoba?&#10;- Perangkat / sistem yang terpengaruh?"
                              required>{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Lampiran <span style="color:var(--gray-400);font-weight:400;">(opsional)</span></label>
                    <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <strong>Klik atau seret file ke sini</strong>
                        <span style="display:block;font-size:.8rem;color:var(--gray-400);margin-top:4px;">
                            Format: JPG, PNG, PDF, DOC, XLS, ZIP — Maks 10MB per file
                        </span>
                    </div>
                    <input type="file" id="fileInput" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip" style="display:none;">
                    <div id="fileList" style="margin-top:8px;"></div>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const fileList  = document.getElementById('fileList');

fileInput.addEventListener('change', updateFileList);

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('drag-over');
});
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    fileInput.files = e.dataTransfer.files;
    updateFileList();
});

function updateFileList() {
    fileList.innerHTML = '';
    const files = fileInput.files;
    if (!files.length) return;
    for (let i = 0; i < files.length; i++) {
        const f = files[i];
        const size = f.size > 1048576 ? (f.size/1048576).toFixed(1)+' MB' : (f.size/1024).toFixed(0)+' KB';
        const item = document.createElement('div');
        item.style.cssText = 'display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--gray-50);border-radius:var(--radius-sm);margin-bottom:6px;font-size:.83rem;';
        item.innerHTML = `<i class="fa-solid fa-file" style="color:var(--primary);"></i>
            <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${f.name}</span>
            <span style="color:var(--gray-400);">${size}</span>`;
        fileList.appendChild(item);
    }
}
</script>
@endpush
@endsection
