<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = [
            'total_tickets'    => Ticket::count(),
            'open_tickets'     => Ticket::where('status', 'open')->count(),
            'in_progress'      => Ticket::where('status', 'in_progress')->count(),
            'resolved_today'   => Ticket::where('status', 'resolved')->whereDate('resolved_at', today())->count(),
            'critical'         => Ticket::where('priority', 'critical')->whereNotIn('status', ['resolved', 'closed'])->count(),
            'unassigned'       => Ticket::whereNull('assigned_to')->whereNotIn('status', ['resolved', 'closed'])->count(),
            'total_users'      => User::where('role', 'user')->count(),
            'total_agents'     => User::where('role', 'agent')->count(),
        ];

        $recentTickets = Ticket::with(['user', 'category', 'assignedAgent'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $ticketsByStatus = [
            'open'        => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'pending'     => Ticket::where('status', 'pending')->count(),
            'resolved'    => Ticket::where('status', 'resolved')->count(),
            'closed'      => Ticket::where('status', 'closed')->count(),
        ];

        $ticketsByPriority = [
            'low'      => Ticket::where('priority', 'low')->count(),
            'medium'   => Ticket::where('priority', 'medium')->count(),
            'high'     => Ticket::where('priority', 'high')->count(),
            'critical' => Ticket::where('priority', 'critical')->count(),
        ];

        // Last 7 days ticket trend
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trend[] = [
                'date'  => $date->format('d M'),
                'count' => Ticket::whereDate('created_at', $date->toDateString())->count(),
            ];
        }

        $topAgents = User::where('role', 'agent')
            ->withCount(['assignedTickets as resolved_count' => function ($q) {
                $q->where('status', 'resolved');
            }])
            ->orderByDesc('resolved_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentTickets', 'ticketsByStatus',
            'ticketsByPriority', 'trend', 'topAgents'
        ));
    }

    // ─── Tickets ──────────────────────────────────────────────────────────

    public function tickets(Request $request)
    {
        $query = Ticket::with(['user', 'category', 'assignedAgent']);

        if ($request->status)   $query->where('status', $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        if ($request->category) $query->where('category_id', $request->category);
        if ($request->agent)    $query->where('assigned_to', $request->agent);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('ticket_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $tickets    = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $categories = Category::all();
        $agents     = User::whereIn('role', ['admin', 'agent'])->get();

        return view('admin.tickets.index', compact('tickets', 'categories', 'agents'));
    }

    public function showTicket(Ticket $ticket)
    {
        $ticket->load(['replies.user', 'attachments.user', 'activities.user', 'category', 'assignedAgent', 'user']);
        $agents = User::whereIn('role', ['admin', 'agent'])->where('is_active', true)->get();
        $categories = Category::all();
        return view('admin.tickets.show', compact('ticket', 'agents', 'categories'));
    }

    public function updateTicket(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status'           => 'required|in:open,in_progress,pending,resolved,closed',
            'priority'         => 'required|in:low,medium,high,critical',
            'assigned_to'      => 'nullable|exists:users,id',
            'category_id'      => 'nullable|exists:categories,id',
            'resolution_notes' => 'nullable|string',
        ]);

        $oldStatus = $ticket->status;
        $oldAgent  = $ticket->assigned_to;

        if ($data['status'] === 'resolved' && $oldStatus !== 'resolved') {
            $data['resolved_at'] = now();
        }
        if ($data['status'] === 'closed' && $oldStatus !== 'closed') {
            $data['closed_at'] = now();
        }

        $ticket->update($data);

        // Log activities
        if ($oldStatus !== $data['status']) {
            TicketActivity::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => Auth::id(),
                'action'      => 'status_changed',
                'description' => Auth::user()->name . ' mengubah status dari "' . $oldStatus . '" ke "' . $data['status'] . '"',
            ]);
        }
        if ($oldAgent !== ($data['assigned_to'] ?? null)) {
            $agentName = isset($data['assigned_to']) ? User::find($data['assigned_to'])?->name : 'Tidak Ditugaskan';
            TicketActivity::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => Auth::id(),
                'action'      => 'assigned',
                'description' => Auth::user()->name . ' menugaskan tiket ke ' . $agentName,
            ]);
        }

        return back()->with('success', 'Tiket berhasil diperbarui.');
    }

    public function replyTicket(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'message'     => 'required|string',
            'is_internal' => 'boolean',
        ]);

        TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'message'     => $data['message'],
            'is_internal' => $request->boolean('is_internal'),
        ]);

        TicketActivity::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'action'      => 'replied',
            'description' => Auth::user()->name . ($request->boolean('is_internal') ? ' menambahkan catatan internal' : ' menambahkan balasan'),
        ]);

        return back()->with('success', 'Balasan berhasil dikirim!');
    }

    // ─── Users ────────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $query = User::query();
        if ($request->role)   $query->where('role', $request->role);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        $users = $query->withCount('tickets')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|in:admin,agent,user',
            'department' => 'nullable|string|max:100',
            'is_active'  => 'boolean',
            'password'   => 'nullable|min:8',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    // ─── Categories ───────────────────────────────────────────────────────

    public function categories()
    {
        $categories = Category::withCount('tickets')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'color'       => 'required|string',
            'icon'        => 'required|string',
            'description' => 'nullable|string',
        ]);
        Category::create($data);
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function deleteCategory(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
