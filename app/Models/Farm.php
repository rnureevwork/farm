<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Farm extends Model
{
    protected $fillable = ['name', 'contact_phone'];
    
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'farm_users');
    }
}
