<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'phone',
        'password',
        'address',
        'residence_address',
        'workplace',
        'province_code',
        'district_code',
        'ward_code',
        'detail_address',
        'role',
        'status',
        'points',
        'avatar',
        'age',
        'gender',
        'friend_invite_code',
        'referral_code',
        'google_id',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = static::generateUniqueReferralCode();
            }
        });
    }

    public static function generateUniqueReferralCode()
    {
        do {
            $code = 'GD' . strtoupper(bin2hex(random_bytes(3))); // e.g. GDA1B2C3
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'points' => 'integer',
        ];
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    public function receivedFriendships()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function dailyCheckins()
    {
        return $this->hasMany(DailyCheckin::class);
    }

    public function gameAttempts()
    {
        return $this->hasMany(GameAttempt::class);
    }

    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function salesSchedules()
    {
        return $this->hasMany(SalesSchedule::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWarehouse(): bool
    {
        return $this->role === 'warehouse';
    }

    public function isSales(): bool
    {
        return $this->role === 'sales';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['warehouse', 'sales']);
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'warehouse', 'sales']);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
