<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (is_null($user->partner_id)) {
                $user->partner_id = 1;
            }
        });

        static::created(function (self $user) {
            if (! $user->wallet) {
                $user->wallet()->create();
            }
            if (! $user->giftsBag) {
                $user->giftsBag()->create();
            }
        });
    }

    public function giftsBag()
    {
        return $this->hasOne(GiftsBag::class);
    }

    public function gifts()
    {
        return $this->hasManyThrough(Gift::class, GiftsBag::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
