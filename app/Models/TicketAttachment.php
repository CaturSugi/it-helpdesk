<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'filename', 'original_name', 'mime_type', 'file_size'];

    public function user()   { return $this->belongsTo(User::class); }
    public function ticket() { return $this->belongsTo(Ticket::class); }

    public function getUrlAttribute(): string
    {
        return asset('storage/attachments/' . $this->filename);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
