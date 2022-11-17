<?php

namespace catchapp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailType extends Model
{
    use SoftDeletes;

    protected $table="email_types";
    protected $dates=['deleted_at'];
}
