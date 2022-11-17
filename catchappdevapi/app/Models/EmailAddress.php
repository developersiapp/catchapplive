<?php

namespace catchapp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailAddress extends Model
{
    use SoftDeletes;

    protected $table="email_addresses";
    protected $dates=['deleted_at'];

}
