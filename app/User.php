<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    public $timestamps = false;
    protected $table = 'users';
    const tableName = 'users';

    const CacheKey = 'liy_users_';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getCacheKey($user_id)
    {
        return self::CacheKey . $user_id;
    }

    public static function getOne($user_id)
    {
        return Cache::remember(self::getCacheKey($user_id), env('CACHE_TTL', 300), function () use ($user_id) {
            return DB::table(self::tableName)->where('id', $user_id)
                ->where('status', 0)
                ->where('is_delete', 0)->first();
        });
    }
}
