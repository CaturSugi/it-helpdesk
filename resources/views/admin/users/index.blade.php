@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="card">
    {{-- Filter --}}
    <form method="GET" class="filter-bar">
        <div class="input-group search-input">
            <i class="fa-solid fa-search input-group-icon"></i>
            <input type="text" name="search" class="form-control"
                   placeholder="Cari nama atau email..."
                   value="{{ request('search') }}">
        </div>
        <select name="role" class="form-control" style="max-width:160px;">
            <option value="">Semua Role</option>
            <option value="admin"  {{ request('role')==='admin'  ? 'selected' : '' }}>Admin</option>
            <option value="agent"  {{ request('role')==='agent'  ? 'selected' : '' }}>Agent</option>
            <option value="user"   {{ request('role')==='user'   ? 'selected' : '' }}>User</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        @if(request()->hasAny(['search','role']))
        <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-xmark"></i> Reset
        </a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Departemen</th>
                    <th>Role</th>
                    <th>Total Tiket</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="{{ $user->avatar_url }}" alt=""
                                 style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                            <span style="font-weight:600;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--gray-500);font-size:.85rem;">{{ $user->email }}</td>
                    <td style="font-size:.85rem;">{{ $user->department ?? '—' }}</td>
                    <td>
                        @php
                            $roleBadge = ['admin'=>'badge-danger','agent'=>'badge-warning','user'=>'badge-secondary'];
                        @endphp
                        <span class="badge {{ $roleBadge[$user->role] ?? 'badge-secondary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td style="font-weight:600;text-align:center;">{{ $user->tickets_count }}</td>
                    <td>
                        @if($user->is_active)
                        <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:.5rem;"></i> Aktif</span>
                        @else
                        <span class="badge badge-secondary"><i class="fa-solid fa-circle" style="font-size:.5rem;"></i> Nonaktif</span>
                        @endif
                    </td>
                    <td style="font-size:.8rem;color:var(--gray-500);">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <button class="btn btn-outline btn-sm"
                                onclick="openEditModal({{ json_encode($user) }})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fa-solid fa-users"></i>
                            <p>Tidak ada pengguna ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--gray-100);">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- Edit User Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:var(--radius-lg);width:100%;max-width:500px;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-lg);">
        <div style="padding:20px 24px;border-bottom:1px solid var(--gray-200);display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:1rem;font-weight:700;">Edit Pengguna</h3>
            <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;color:var(--gray-400);font-size:1.2rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editForm" method="POST" style="padding:24px;">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" id="edit_role" class="form-control">
                        <option value="user">User</option>
                        <option value="agent">Agent</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Departemen</label>
                    <input type="text" name="department" id="edit_department" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status Akun</label>
                <select name="is_active" id="edit_is_active" class="form-control">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Password Baru <span style="color:var(--gray-400);font-weight:400;">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                <button type="button" onclick="closeEditModal()" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(user) {
    document.getElementById('edit_name').value       = user.name;
    document.getElementById('edit_email').value      = user.email;
    document.getElementById('edit_role').value       = user.role;
    document.getElementById('edit_department').value = user.department ?? '';
    document.getElementById('edit_is_active').value  = user.is_active ? '1' : '0';
    document.getElementById('editForm').action = '/admin/users/' + user.id;
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endpush
@endsection
