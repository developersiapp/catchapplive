<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 18/6/19
 * Time: 10:31 AM
 */

namespace catchapp\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}