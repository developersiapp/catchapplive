<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 5/6/19
 * Time: 2:13 PM
 */

namespace catchapp\Models;


use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class AdminUser extends Model
{
    use HasApiTokens;

    protected $table='admin_users';


    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}