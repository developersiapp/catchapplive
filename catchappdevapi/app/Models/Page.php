<?php

namespace catchapp\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{

    protected $table="static_pages";

    public static $page_type=[
        1 =>'privacy-policy',
        2 =>'tnc'
    ];

}
