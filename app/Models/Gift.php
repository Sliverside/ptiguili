<?php

namespace App\Models;

use App\Enums\WonGiftStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Znck\Eloquent\Traits\BelongsToThrough;

class Gift extends Model
{
    use BelongsToThrough;
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'relative_probability',
    ];

    public function giftsBag()
    {
        return $this->belongsTo(GiftsBag::class);
    }

    public function owner()
    {
        return $this->belongsToThrough(User::class, GiftsBag::class);
    }

    public function wallets()
    {
        return $this->belongsToMany(Wallet::class, 'won_gifts');
    }

    public function wons()
    {
        return $this->hasMany(WonGift::class);
    }

    public function oldestWon(?User $user, ?WonGiftStatusEnum $status): ?WonGift
    {
        return $this->hasOne(WonGift::class)->ofMany(['id' => 'min'], function (Builder $builder) use ($user, $status) {
            if ($status) {
                $builder->where('status', $status);
            }
            if ($user) {
                $builder->whereBelongsTo($user->wallet);
            }
        })->first();
    }

    public function scopeWithProbability(Builder $builder): Builder
    {
        return $builder
            ->select(DB::raw("{$builder->from}.*, {$builder->from }.relative_probability /
                (
                    SELECT SUM(g.relative_probability) FROM {$builder->from} as g
                    WHERE g.gifts_bag_id = {$builder->from}.gifts_bag_id and g.gifts_bag_id IS not NULL
                ) *
                (
                    SELECT probability FROM gifts_bags as b
                    WHERE b.id = {$builder->from}.gifts_bag_id
                )
            as probability"));
    }

    public function scopeWonBy(Builder $builder, User $user): Builder
    {
        return $builder->whereRelation('wallets', 'wallets.id', $user->wallet->id);
    }

    public function scopeWithCountWins(Builder $builder, ?User $user, ?WonGiftStatusEnum $status): Builder
    {
        return $builder->withCount([
            'wons as count_wins'.($status ? '_'.$status->name : '') => function (Builder $builder) use ($user, $status) {
                if ($user) {
                    $builder->whereBelongsTo($user->wallet);
                }
                if ($status) {
                    $builder->where('status', $status);
                }
            },
        ]);
    }

    public function scopeWhereHasWins(Builder $builder, ?User $user, ?WonGiftStatusEnum $status)
    {
        return $builder->whereHas('wons', function (Builder $builder) use ($user, $status) {
            if ($user) {
                $builder->whereBelongsTo($user->wallet);
            }
            if ($status) {
                $builder->where('status', $status);
            }
        });
    }

    public function scopeOwnedBy(Builder $builder, ?User $user): Builder
    {
        return $builder->whereRelation('owner', function (Builder $builder) use ($user) {
            return $builder->where('users.id', $user->id);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int,self>  $gifts
     * @return self
     * */
    public static function randomGift(Collection $gifts)
    {
        $lotterie = new Collection();
        foreach ($gifts as $gift) {
            for ($i = 0; $i < $gift->relative_probability * 10; $i++) {
                $lotterie->add($gift);
            }
        }

        return $lotterie->random();
    }
}
