<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'station_id', 'user_id', 'type', 'message', 'is_resolved', 'triggered_at', 'resolved_at'
    ];
    
    protected $casts = [
        'is_resolved' => 'boolean',
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];
    
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
