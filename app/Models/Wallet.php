<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'coins',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'coins_update_at' => 'date',
    ];

    public $timestamps = false;

    public function wonGifts()
    {
        return $this->belongsToMany(Gift::class, 'won_gifts')
            ->withPivot('created_at')
            ->orderByPivot('id', 'desc');
    }
}
