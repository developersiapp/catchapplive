<?php

namespace catchapp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'user_name',
        'password',
        'birth_date',
        'gender',
        'registeration_type',
        'client_id',
        'profile_image',
        'oauth_key',
        'device_token',
        'location'
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

    protected $table ="users";

    protected $dates=['deleted_at'];

    public static $registeration_type = [
        1 =>'App Registration',
        2 =>'Facebook',
        3 =>'Google',
        4 =>'Twitter',
        5 =>'Instagram',
        6 =>'Apple',
        7 =>'Web Registeration',
    ];
}
