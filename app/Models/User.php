<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'department', 'phone', 'avatar', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return in_array($this->role, ['admin', 'agent']);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff&size=128';
    }
}
