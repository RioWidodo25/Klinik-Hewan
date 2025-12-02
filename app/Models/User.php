<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserFavorite> $favorites
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $favoritedProducts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $unreadNotifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pet> $pets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appointment> $appointments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoyaltyPoint> $loyaltyPoints
 * @property-read int $loyalty_points_balance
 * @property-read string $profile_photo_url
 * @method \Illuminate\Database\Eloquent\Relations\HasMany addresses()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany orders()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany favorites()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany favoritedProducts()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany notifications()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany unreadNotifications()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany pets()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany appointments()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany reviews()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany loyaltyPoints()
 */
class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

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
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
        ];
    }

    /**
     * Get the user's addresses
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the user's orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's favorite products
     */
    public function favorites()
    {
        return $this->hasMany(UserFavorite::class);
    }

    /**
     * Get the user's favorited products (many-to-many)
     */
    public function favoritedProducts()
    {
        return $this->belongsToMany(Product::class, 'user_favorites');
    }

    /**
     * Get the user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's unread notifications
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->unread();
    }

    /**
     * Get the user's pets
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * Get the user's appointments
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the user's reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the user's loyalty points transactions
     */
    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Get user's total loyalty points balance
     */
    public function getLoyaltyPointsBalanceAttribute(): int
    {
        return LoyaltyPoint::getUserBalance($this->id);
    }
}
