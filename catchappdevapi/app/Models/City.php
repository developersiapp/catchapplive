<?php


namespace catchapp\Models;


use Illuminate\Database\Eloquent\Model;

class City extends  Model
{
    protected $table='cities';

    public function clubs()
    {
        return $this->hasMany(Club::class, 'city', 'id');
    }
}