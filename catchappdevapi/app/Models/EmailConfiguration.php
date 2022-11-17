<?php

namespace catchapp\Models;

use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{
    protected $table="email_configuration";

    public static $email_types =[
        1=>'New User Registeration',
        2=>'New Club Registeration',
        3=>'New DJ Registeration',
        4=>'Forgot Password Email',
    ];
}
