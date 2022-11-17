<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 12/6/19
 * Time: 11:13 AM
 */

namespace catchapp\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStory extends Model
{
    use SoftDeletes;
    protected $table = "user_stories";
    protected $dates = ['deleted_at'];
    public static $story_type = [
        1 => 'Text',
        2 => 'Image',
        3 => 'Video'
    ];

    public function user_detail()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}