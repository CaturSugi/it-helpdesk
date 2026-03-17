<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketAttachment;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['category', 'assignedAgent'])
            ->where('user_id', Auth::id());

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('ticket_number', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $stats = $this->getUserStats();

        return view('tickets.index', compact('tickets', 'stats'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('tickets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'priority'    => 'required|in:low,medium,high,critical',
            'description' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip',
        ]);

        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'subject'       => $data['subject'],
            'description'   => $data['description'],
            'priority'      => $data['priority'],
            'category_id'   => $data['category_id'] ?? null,
            'status'        => 'open',
            'user_id'       => Auth::id(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('attachments', $filename, 'public');
                TicketAttachment::create([
                    'ticket_id'     => $ticket->id,
                    'user_id'       => Auth::id(),
                    'filename'      => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        // Log activity
        TicketActivity::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'action'      => 'created',
            'description' => 'Tiket dibuat oleh ' . Auth::user()->name,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiket #' . $ticket->ticket_number . ' berhasil dibuat!');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['replies.user', 'attachments.user', 'activities.user', 'category', 'assignedAgent', 'user']);
        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $data = $request->validate([
            'message'     => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        $reply = TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'message'     => $data['message'],
            'is_internal' => false,
        ]);

        // If ticket was resolved/closed, reopen it when user replies
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
            TicketActivity::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => Auth::id(),
                'action'      => 'reopened',
                'description' => 'Tiket dibuka kembali oleh ' . Auth::user()->name,
            ]);
        }

        TicketActivity::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'action'      => 'replied',
            'description' => Auth::user()->name . ' menambahkan balasan',
        ]);

        return back()->with('success', 'Balasan berhasil dikirim!');
    }

    public function close(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->update(['status' => 'closed', 'closed_at' => now()]);
        TicketActivity::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => Auth::id(),
            'action'      => 'closed',
            'description' => 'Tiket ditutup oleh ' . Auth::user()->name,
        ]);
        return back()->with('success', 'Tiket berhasil ditutup.');
    }

    private function getUserStats(): array
    {
        $userId = Auth::id();
        return [
            'total'       => Ticket::where('user_id', $userId)->count(),
            'open'        => Ticket::where('user_id', $userId)->where('status', 'open')->count(),
            'in_progress' => Ticket::where('user_id', $userId)->where('status', 'in_progress')->count(),
            'resolved'    => Ticket::where('user_id', $userId)->where('status', 'resolved')->count(),
        ];
    }
}
