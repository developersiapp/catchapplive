<?php

namespace catchapp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DJ extends Model
{
    use SoftDeletes;

    protected $table="djs";
    protected $dates=['deleted_at'];

    public function assigned_club()
    {
        return $this->hasOne(Club::class,'id','assigned_clubs');
    }
}
