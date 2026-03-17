<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'subject', 'description', 'status',
        'priority', 'category_id', 'user_id', 'assigned_to',
        'resolution_notes', 'due_date', 'resolved_at', 'closed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class)->orderBy('created_at', 'desc');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $year = date('Y');
        $latest = self::whereYear('created_at', $year)->max('id') ?? 0;
        return $prefix . $year . str_pad($latest + 1, 5, '0', STR_PAD_LEFT);
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'low'      => 'badge-success',
            'medium'   => 'badge-info',
            'high'     => 'badge-warning',
            'critical' => 'badge-danger',
            default    => 'badge-secondary',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'open'        => 'badge-primary',
            'in_progress' => 'badge-warning',
            'pending'     => 'badge-secondary',
            'resolved'    => 'badge-success',
            'closed'      => 'badge-dark',
            default       => 'badge-secondary',
        };
    }

    public function getPriorityIconAttribute(): string
    {
        return match ($this->priority) {
            'low'      => 'fa-arrow-down',
            'medium'   => 'fa-minus',
            'high'     => 'fa-arrow-up',
            'critical' => 'fa-fire',
            default    => 'fa-minus',
        };
    }
}
