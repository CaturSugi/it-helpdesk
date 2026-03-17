<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ────────────────────────────────────────────────────
        $categories = [
            ['name' => 'Hardware',         'color' => '#ef4444', 'icon' => 'fa-desktop',       'description' => 'Masalah perangkat keras komputer'],
            ['name' => 'Software',         'color' => '#6366f1', 'icon' => 'fa-laptop-code',   'description' => 'Instalasi & error aplikasi'],
            ['name' => 'Jaringan/Network', 'color' => '#0ea5e9', 'icon' => 'fa-wifi',           'description' => 'Koneksi internet & LAN'],
            ['name' => 'Email',            'color' => '#f59e0b', 'icon' => 'fa-envelope',       'description' => 'Masalah email korporat'],
            ['name' => 'Printer',          'color' => '#22c55e', 'icon' => 'fa-print',          'description' => 'Perangkat cetak & scan'],
            ['name' => 'Server',           'color' => '#8b5cf6', 'icon' => 'fa-server',         'description' => 'Server & virtualisasi'],
            ['name' => 'Akses & Izin',     'color' => '#f97316', 'icon' => 'fa-key',            'description' => 'Hak akses & password'],
            ['name' => 'Lainnya',          'color' => '#64748b', 'icon' => 'fa-circle-question','description' => 'Kategori umum'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // ── Users ─────────────────────────────────────────────────────────
        $admin = User::create([
            'name'       => 'Administrator IT',
            'email'      => 'admin@helpdesk.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'department' => 'IT Department',
            'phone'      => '081234567890',
            'is_active'  => true,
        ]);

        $agents = [];
        $agentData = [
            ['name' => 'Budi Santoso',   'email' => 'budi@helpdesk.com',   'dept' => 'IT Support'],
            ['name' => 'Siti Rahayu',    'email' => 'siti@helpdesk.com',   'dept' => 'IT Support'],
            ['name' => 'Ahmad Fauzi',    'email' => 'ahmad@helpdesk.com',  'dept' => 'IT Infrastructure'],
        ];

        foreach ($agentData as $a) {
            $agents[] = User::create([
                'name'       => $a['name'],
                'email'      => $a['email'],
                'password'   => Hash::make('password'),
                'role'       => 'agent',
                'department' => $a['dept'],
                'is_active'  => true,
            ]);
        }

        $users = [];
        $userData = [
            ['name' => 'Andi Wijaya',     'email' => 'andi@company.com',    'dept' => 'Finance'],
            ['name' => 'Dewi Kusuma',     'email' => 'dewi@company.com',    'dept' => 'HR'],
            ['name' => 'Riko Pratama',    'email' => 'riko@company.com',    'dept' => 'Marketing'],
            ['name' => 'Maya Indah',      'email' => 'maya@company.com',    'dept' => 'Operations'],
            ['name' => 'Fajar Nugroho',   'email' => 'fajar@company.com',   'dept' => 'Accounting'],
            ['name' => 'Rina Melati',     'email' => 'rina@company.com',    'dept' => 'Procurement'],
        ];

        foreach ($userData as $u) {
            $users[] = User::create([
                'name'       => $u['name'],
                'email'      => $u['email'],
                'password'   => Hash::make('password'),
                'role'       => 'user',
                'department' => $u['dept'],
                'is_active'  => true,
            ]);
        }

        // ── Sample Tickets ────────────────────────────────────────────────
        $ticketData = [
            [
                'subject'     => 'Komputer tidak bisa booting setelah update Windows',
                'description' => "Setelah melakukan Windows Update tadi pagi, komputer saya tidak bisa masuk ke Windows. Muncul layar biru (BSOD) dengan kode error INACCESSIBLE_BOOT_DEVICE.\n\nSpesifikasi: Dell Latitude 7420, Windows 11 Pro\nLangkah sudah dicoba: Restart beberapa kali, tapi tetap BSOD.",
                'status'      => 'open',
                'priority'    => 'high',
                'category'    => 1,
                'user'        => 0,
                'agent'       => 0,
            ],
            [
                'subject'     => 'Microsoft Office 365 tidak bisa dibuka',
                'description' => "Ketika mencoba membuka Excel atau Word, muncul pesan error \"Microsoft Office is not properly installed\". Saya sudah coba restart namun masalah tetap ada.\n\nError code: 0x80070005",
                'status'      => 'in_progress',
                'priority'    => 'medium',
                'category'    => 2,
                'user'        => 1,
                'agent'       => 0,
            ],
            [
                'subject'     => 'Koneksi VPN perusahaan terputus terus',
                'description' => "VPN Cisco AnyConnect selalu disconnect setiap 15-20 menit sekali. Sangat mengganggu pekerjaan karena saya bekerja dari rumah dan semua sistem internal hanya bisa diakses via VPN.",
                'status'      => 'in_progress',
                'priority'    => 'high',
                'category'    => 3,
                'user'        => 2,
                'agent'       => 1,
            ],
            [
                'subject'     => 'Email tidak bisa kirim ke external domain',
                'description' => "Sejak 2 hari lalu saya tidak bisa mengirim email ke domain selain @company.com. Email keluar tapi tidak sampai ke tujuan, tidak ada bounce notice juga.",
                'status'      => 'pending',
                'priority'    => 'high',
                'category'    => 4,
                'user'        => 3,
                'agent'       => 2,
            ],
            [
                'subject'     => 'Printer HP LaserJet tidak terdeteksi di network',
                'description' => "Printer HP LaserJet Pro M404dn di ruang Finance tidak terdeteksi oleh komputer-komputer di lantai 2. Padahal sebelumnya berjalan dengan baik.",
                'status'      => 'resolved',
                'priority'    => 'low',
                'category'    => 5,
                'user'        => 4,
                'agent'       => 0,
            ],
            [
                'subject'     => 'Server file sharing sangat lambat',
                'description' => "Transfer file dari dan ke server NAS terasa sangat lambat dalam 3 hari terakhir. Biasanya kecepatan bisa 100MB/s, sekarang hanya 2-5 MB/s. Ini menghambat pekerjaan tim yang sering share file besar.",
                'status'      => 'open',
                'priority'    => 'critical',
                'category'    => 6,
                'user'        => 5,
                'agent'       => null,
            ],
            [
                'subject'     => 'Lupa password akun SAP ERP',
                'description' => "Saya lupa password akun SAP saya. Sudah coba fitur reset tapi email konfirmasi tidak kunjung datang ke inbox maupun spam.",
                'status'      => 'resolved',
                'priority'    => 'medium',
                'category'    => 7,
                'user'        => 0,
                'agent'       => 1,
            ],
            [
                'subject'     => 'Monitor bergaris horizontal di bagian tengah',
                'description' => "Monitor Dell 24 inch di meja saya menampilkan garis horizontal berwarna hijau di tengah layar. Sudah coba ganti kabel HDMI namun masalah tetap ada. Kemungkinan masalah di panel LCD.",
                'status'      => 'open',
                'priority'    => 'low',
                'category'    => 1,
                'user'        => 1,
                'agent'       => null,
            ],
        ];

        foreach ($ticketData as $i => $data) {
            $agentId = $data['agent'] !== null ? ($agents[$data['agent']]->id ?? null) : null;

            $ticket = Ticket::create([
                'ticket_number'  => Ticket::generateTicketNumber(),
                'subject'        => $data['subject'],
                'description'    => $data['description'],
                'status'         => $data['status'],
                'priority'       => $data['priority'],
                'category_id'    => $data['category'],
                'user_id'        => $users[$data['user']]->id,
                'assigned_to'    => $agentId,
                'resolved_at'    => $data['status'] === 'resolved' ? now()->subHours(rand(1,24)) : null,
                'created_at'     => now()->subDays(rand(1,14))->subHours(rand(0,12)),
            ]);

            TicketActivity::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => $users[$data['user']]->id,
                'action'      => 'created',
                'description' => 'Tiket dibuat oleh ' . $users[$data['user']]->name,
                'created_at'  => $ticket->created_at,
            ]);

            if ($agentId) {
                TicketActivity::create([
                    'ticket_id'   => $ticket->id,
                    'user_id'     => $admin->id,
                    'action'      => 'assigned',
                    'description' => 'Tiket ditugaskan ke ' . ($agents[$data['agent']]->name ?? 'Agent'),
                    'created_at'  => $ticket->created_at->addMinutes(15),
                ]);

                // Sample agent reply
                TicketReply::create([
                    'ticket_id'  => $ticket->id,
                    'user_id'    => $agentId,
                    'message'    => "Halo " . $users[$data['user']]->name . ",\n\nTerima kasih telah menghubungi IT Support. Saya sudah menerima tiket Anda dan sedang dalam proses investigasi.\n\nSaya akan segera menindaklanjuti dan memberi kabar perkembangan dalam waktu dekat.\n\nSalam,\nIT Support Team",
                    'is_internal'=> false,
                    'created_at' => $ticket->created_at->addMinutes(30),
                ]);

                TicketActivity::create([
                    'ticket_id'   => $ticket->id,
                    'user_id'     => $agentId,
                    'action'      => 'replied',
                    'description' => ($agents[$data['agent']]->name ?? 'Agent') . ' menambahkan balasan',
                    'created_at'  => $ticket->created_at->addMinutes(30),
                ]);
            }

            if ($data['status'] === 'resolved') {
                $ticket->update(['resolution_notes' => 'Masalah telah diselesaikan. Tiket ditutup.']);
                TicketActivity::create([
                    'ticket_id'   => $ticket->id,
                    'user_id'     => $agentId ?? $admin->id,
                    'action'      => 'resolved',
                    'description' => 'Tiket diselesaikan',
                    'created_at'  => $ticket->resolved_at,
                ]);
            }
        }

        $this->command->info('✅ Seeding selesai!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@helpdesk.com', 'password'],
                ['Agent', 'budi@helpdesk.com',  'password'],
                ['Agent', 'siti@helpdesk.com',  'password'],
                ['User',  'andi@company.com',   'password'],
                ['User',  'dewi@company.com',   'password'],
            ]
        );
    }
}
