<?php


namespace catchapp\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClubStream extends Model
{
    use SoftDeletes;
    protected $table="club_stream";
    protected $dates= ['deleted_at'];
}
