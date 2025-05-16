<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAddress extends Model
{
    use HasFactory;

    /**
     * Pola możliwe do wypełnienia masowo
     * @var array
     */
    protected $fillable = [
        'email',
        'user_id'
    ];

    /**
     * Relacja do modelu User
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
