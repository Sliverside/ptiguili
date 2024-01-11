<?php

namespace App\Models;

use App\Enums\WonGiftStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * App\Models\Gift
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $gifts_bag_id
 * @property string $name
 * @property string $description
 * @property string $relative_probability
 * @property-read \App\Models\GiftsBag $giftsBag
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wallet> $wallets
 * @property-read int|null $wallets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WonGift> $wons
 * @property-read int|null $wons_count
 * @method static \Database\Factories\GiftFactory factory($count = null, $state = [])
 * @method static Builder|Gift newModelQuery()
 * @method static Builder|Gift newQuery()
 * @method static Builder|Gift ownedBy(?\App\Models\User $user = null)
 * @method static Builder|Gift query()
 * @method static Builder|Gift whereCreatedAt($value)
 * @method static Builder|Gift whereDescription($value)
 * @method static Builder|Gift whereGiftsBagId($value)
 * @method static Builder|Gift whereHasWins(?\App\Enums\WonGiftStatusEnum $status = null)
 * @method static Builder|Gift whereId($value)
 * @method static Builder|Gift whereName($value)
 * @method static Builder|Gift whereRelativeProbability($value)
 * @method static Builder|Gift whereUpdatedAt($value)
 * @method static Builder|Gift withCountWins(?\App\Models\User $user = null, ?\App\Enums\WonGiftStatusEnum $status = null)
 * @method static Builder|Gift withProbability()
 * @method static Builder|Gift wonBy(\App\Models\User $user)
 * @mixin \Eloquent
 */
class Gift extends Model
{
    use HasFactory;
    use BelongsToThrough;

    protected $fillable = [
        'name',
        'description',
        'relative_probability'
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
        return $this->hasOne(WonGift::class)->ofMany(['id' => 'min'], function(Builder $builder) use($user, $status) {
            if($status) $builder->where('status', $status);
            if($user) $builder->whereBelongsTo($user->wallet);
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

    public function scopeWithCountWins(Builder $builder, ?User $user = null, ?WonGiftStatusEnum $status = null): Builder
    {
        return $builder->withCount([
            'wons as count_wins' . ($status ? '_' . $status->name : '')  => function(Builder $builder) use($user, $status) {
                if($user) $builder->whereBelongsTo($user->wallet);
                if($status) $builder->where('status', $status);
            }
        ]);
    }

    public function scopeWhereHasWins(Builder $builder, ?WonGiftStatusEnum $status = null)
    {
        return $builder->whereHas('wons', function(Builder $builder) use($status) {
            if($status) $builder->where('status', $status);
        });
    }

    public function scopeOwnedBy(Builder $builder, ?User $user = null): Builder
    {
        return $builder->whereRelation('owner', function(Builder $builder) use($user) {
            return $builder->where('users.id', $user->id);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection<int,self> $gifts
     * @return self
     * */
    public static function randomGift(Collection $gifts)
    {
        $lotterie = new Collection();
        foreach($gifts as $gift) {
            for ($i=0; $i < $gift->relative_probability * 10; $i++) {
                $lotterie->add($gift);
            }
        }
        return $lotterie->random();
    }
}
