<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = ['name', 'duration_minutes', 'price', 'active'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2',
                'active' => 'boolean'];
    }

    public function professionals(): BelongsToMany
    {
        return $this->belongsToMany(Professional::class);
    }
}
