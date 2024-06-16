<?php

namespace App\Models;

use App\Enums\WonGiftStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

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
