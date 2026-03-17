<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketActivity extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'action', 'description', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function user()   { return $this->belongsTo(User::class); }
    public function ticket() { return $this->belongsTo(Ticket::class); }
}
