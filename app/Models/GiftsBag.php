<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftsBag extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'probability',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gifts()
    {
        return $this->hasMany(Gift::class);
    }

    public function success(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => random_int(1, 1000) < $attributes['probability'] * 10
        );
    }
}
