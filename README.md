# 🎫 HelpDesk Pro — IT Support Ticketing System
> Sistem Tiket IT Support berbasis **Laravel 11** dengan desain modern

---

## ✨ Fitur Unggulan

### 👥 Multi-Role Access
| Role    | Akses |
|---------|-------|
| Admin   | Full access: dashboard, tiket, user, kategori |
| Agent   | Kelola & balas tiket, internal notes |
| User    | Buat & lacak tiket sendiri |

### 🎫 Manajemen Tiket
- Buat tiket dengan 4 level prioritas (Rendah / Sedang / Tinggi / Kritis)
- Lampiran file (JPG, PNG, PDF, DOC, XLS, ZIP — maks 10MB)
- Status tracking: Open → In Progress → Pending → Resolved → Closed
- Percakapan real-time antara user & agent
- **Internal Notes** (hanya visible agent/admin)
- Activity log lengkap untuk setiap tiket
- Nomor tiket otomatis (TKTyyyyNNNNN)

### 📊 Admin Dashboard
- Statistik: total tiket, terbuka, diproses, kritis, unassigned
- Grafik tren tiket 7 hari terakhir
- Distribusi status & prioritas (bar chart)
- Performa agent (tiket diselesaikan)
- Tabel tiket terbaru

### 🔍 Filter & Pencarian
- Filter by: status, prioritas, kategori, agent
- Full-text search pada subjek, nomor tiket, nama user
- Pagination dengan navigasi halaman

---

## 🚀 Instalasi

### 1. Buat Proyek Laravel Baru
```bash
composer create-project laravel/laravel it-helpdesk
cd it-helpdesk
```

### 2. Salin File Proyek
Salin semua file dari folder ini ke direktori Laravel Anda:
```
app/Http/Controllers/   → AuthController, TicketController, AdminController
app/Http/Middleware/    → RoleMiddleware
app/Models/             → User, Ticket, Category, TicketReply, TicketAttachment, TicketActivity
app/Policies/           → TicketPolicy
app/Providers/          → AppServiceProvider, AuthServiceProvider
bootstrap/app.php
database/migrations/    → Semua file migration
database/seeders/       → DatabaseSeeder
public/css/app.css
resources/views/        → Semua view (auth, admin, tickets, layouts, partials)
routes/web.php
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_helpdesk
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Setup Database
```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE it_helpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Jalankan migration dan seeder
php artisan migrate --seed

# Setup storage untuk upload file
php artisan storage:link
```

### 5. Jalankan Aplikasi
```bash
php artisan serve
```
Buka: **http://localhost:8000**

---

## 🔐 Akun Demo

| Role    | Email                  | Password  |
|---------|------------------------|-----------|
| Admin   | admin@helpdesk.com     | password  |
| Agent   | budi@helpdesk.com      | password  |
| Agent   | siti@helpdesk.com      | password  |
| User    | andi@company.com       | password  |
| User    | dewi@company.com       | password  |

---

## 📁 Struktur File

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php      # Login, Register, Logout
│   │   ├── TicketController.php    # User: CRUD tiket
│   │   └── AdminController.php     # Admin/Agent: dashboard, tiket, user, kategori
│   └── Middleware/
│       └── RoleMiddleware.php      # Cek role user
├── Models/
│   ├── User.php
│   ├── Ticket.php
│   ├── Category.php
│   ├── TicketReply.php
│   ├── TicketAttachment.php
│   └── TicketActivity.php
└── Policies/
    └── TicketPolicy.php

resources/views/
├── layouts/app.blade.php           # Layout utama (sidebar + topbar)
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── tickets/
│   ├── index.blade.php             # Daftar tiket user
│   ├── create.blade.php            # Form buat tiket
│   └── show.blade.php              # Detail tiket user
├── admin/
│   ├── dashboard.blade.php         # Dashboard admin
│   ├── categories.blade.php        # Manajemen kategori
│   ├── tickets/
│   │   ├── index.blade.php         # Semua tiket (admin)
│   │   └── show.blade.php          # Detail tiket (admin)
│   └── users/
│       └── index.blade.php         # Manajemen pengguna
└── partials/
    └── pagination.blade.php        # Custom pagination

public/css/app.css                  # CSS utama (≈600 baris)
```

---

## 🛠️ Kustomisasi

### Menambah Kategori
Masuk sebagai Admin → Menu Kategori → Isi form & simpan.

### Mengubah Warna Tema
Edit variabel CSS di `public/css/app.css`:
```css
:root {
    --primary:       #5b5ef4;   /* Warna utama */
    --primary-dark:  #4446d8;
    --dark:          #1e1b4b;   /* Warna sidebar */
}
```

### Notifikasi Email
Tambahkan di `TicketController` dan `AdminController`:
```php
// Kirim notifikasi setelah tiket dibuat / dibalas
Mail::to($ticket->user->email)->send(new TicketCreatedMail($ticket));
```

---

## 📋 Requirement

- PHP >= 8.2
- Laravel 11
- MySQL / MariaDB / PostgreSQL
- Composer
- Node.js (opsional, untuk Vite jika digunakan)
