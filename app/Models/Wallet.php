<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Wallet
 *
 * @property int $id
 * @property int $user_id
 * @property int $coins
 * @property \Illuminate\Support\Carbon $coins_update_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gift> $wonGifts
 * @property-read int|null $won_gifts_count
 * @method static \Database\Factories\WalletFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereCoins($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereCoinsUpdateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereUserId($value)
 * @mixin \Eloquent
 */
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
