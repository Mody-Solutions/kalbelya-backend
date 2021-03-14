<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function Data($user_id){
        $user = User::join('user_profile', 'user_profile.user_id', 'users.id')
            ->join('user_addresses', 'user_addresses.user_id', 'users.id')
            ->where('id', $user_id)
            ->where('isPrimary', 1)
            ->get([
                'users.*',
                'user_profile.aboutMe',
                'user_addresses.country',
                'user_addresses.state',
                'user_addresses.city',
                'user_addresses.address',
                'user_addresses.address2',
                'user_addresses.postalCode',
            ])->first();
        $user->img = image();
        return $user;
    }
}
