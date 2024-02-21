<?php

namespace App\Models;

use App\Enums\WonGiftStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * App\Models\WonGift
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $wallet_id
 * @property int $gift_id
 * @property WonGiftStatusEnum $status
 * @property-read \App\Models\Gift $gift
 * @property-read \App\Models\Wallet $wallet
 *
 * @method static Builder|WonGift newModelQuery()
 * @method static Builder|WonGift newQuery()
 * @method static Builder|WonGift ownedBy(\App\Models\User $user)
 * @method static Builder|WonGift query()
 * @method static Builder|WonGift whereCreatedAt($value)
 * @method static Builder|WonGift whereGiftId($value)
 * @method static Builder|WonGift whereId($value)
 * @method static Builder|WonGift whereStatus($value)
 * @method static Builder|WonGift whereUpdatedAt($value)
 * @method static Builder|WonGift whereWalletId($value)
 *
 * @mixin \Eloquent
 */
class WonGift extends Model
{
    use BelongsToThrough;

    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function owner()
    {
        return $this->belongsToThrough(User::class, [GiftsBag::class, Gift::class]);
    }

    public function winner()
    {
        return $this->belongsToThrough(User::class, Wallet::class);
    }

    public function scopeOwnedBy(Builder $builder, User $user)
    {
        return $builder->whereRelation('owner', function (Builder $builder) use ($user) {
            return $builder->where('users.id', $user->id);
        });
    }

    protected $casts = [
        'status' => WonGiftStatusEnum::class,
    ];
}
