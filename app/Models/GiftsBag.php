<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GiftsBag
 *
 * @property int $id
 * @property int $user_id
 * @property string $probability
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gift> $gifts
 * @property-read int|null $gifts_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\GiftsBagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|GiftsBag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftsBag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftsBag query()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftsBag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftsBag whereProbability($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftsBag whereUserId($value)
 * @mixin \Eloquent
 */
class GiftsBag extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'probability'
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
            get: fn($value, $attributes) => $attributes['probability'] * 10 > random_int(1, 1000)
        );
    }
}
