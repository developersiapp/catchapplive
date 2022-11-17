<?php

namespace catchapp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use SoftDeletes;

    protected $table="clubs";
    protected $dates=['deleted_at'];

    public static $cities = [
        1=>
            [ 'id' =>1,
                'Title' =>'Huntsville',
                'State' =>'1'
            ],
        2=>
            [ 'id' =>2,'Title' =>'Anchorage',
                'State' =>'4'
            ],
        3=>
            [ 'id' =>3,'Title' =>'Juneau',
                'State' =>'1'
            ],
        4=>
            [ 'id' =>4,'Title' =>'Tucson',
                'State' =>'2'
            ],
        5=>
            [ 'id' =>5,'Title' =>'Ambala',
                'State' =>'3'
            ],
        6=>
            [ 'id' =>6,'Title' =>'Birmingham',
                'State' =>'4'
            ],
        7=>
            [ 'id' =>7,'Title' =>'Bedford heights',
                'State' =>'5'
            ],
        8=>
            [ 'id' =>8,'Title' =>'Columbus heights',
                'State' =>'5'
            ],
        9=>
            [ 'id' =>9,'Title' =>'Cleveland',
                'State' =>'5'
            ],

        10=>
            [ 'id' =>10,'Title' =>'Miami',
                'State' =>'6'
            ],
        11=>
            [ 'id' =>11,'Title' =>'Dallas',
                'State' =>'7'
            ],
        12=>
            [ 'id' =>12,'Title' =>'Atlanta',
                'State' =>'8'
            ],
        13=>
            [ 'id' =>13,'Title' =>'Las Vegas',
                'State' =>'9'
            ],
        14=>
            [ 'id' =>14,'Title' =>'Los Angeles',
                'State' =>'10'
            ],
        15=>
            [ 'id' =>15,'Title' =>'New York',
                'State' =>'10'
            ],
            16=>
            [ 'id' =>16,'Title' =>'Columbus',
                'State' =>'5'
            ],
        17=>
            [ 'id' =>17,'Title' =>'Kampala',
                'State' =>'11'
            ],
        18=>
            [ 'id' =>18,'Title' =>'Lagos',
                'State' =>'12'
            ],
        19=>
            [ 'id' =>19,'Title' =>'California',
                'State' =>'10'
            ],
        20=>
            [ 'id' =>20,'Title' =>'Florida',
                'State' =>'6'
            ],
        21=>
            [ 'id' =>21,'Title' =>'Georgia',
                'State' =>'8'
            ],
        22=>
            [ 'id' =>22,'Title' =>'Uganda',
                'State' =>'11'
            ],
        23=>
            [ 'id' =>23,'Title' =>'London',
                'State' =>'13'
            ],
        24=>
            [ 'id' =>24,'Title' =>'Manchester',
                'State' =>'13'
            ],
        25=>
            [ 'id' =>25,'Title' =>'Paris',
                'State' =>'14'
            ],
    ];

    public static $states = [
        1=>
            [
                'id'=>'1',
                'Title' =>'Alaska',
                'Country' =>'1'
            ],
        2=>
            [
                'id'=>'2',
                'Title' =>'Arizona',
                'Country' =>'1'
            ],
        3=>
            [
                'id'=>'3',
                'Title' =>'Haryana',
                'Country' =>'2'
            ],
        4=>
            [   'id'=>'4',
                'Title' =>'Alabama',
                'Country' =>'1'
            ],
        5=>
            [   'id'=>'5',
                'Title' =>'Ohio',
                'Country' =>'1'
            ],
        6=>
            [   'id'=>'6',
                'Title' =>'Florida',
                'Country' =>'1'
            ],  
        7=>
            [   'id'=>'7',
                'Title' =>'Texas',
                'Country' =>'1'
            ],  
        8=>
            [   'id'=>'8',
                'Title' =>'Georgia',
                'Country' =>'1'
            ],  
        9=>
            [   'id'=>'9',
                'Title' =>'Nevada',
                'Country' =>'1'
            ],
        10=>
            [   'id'=>'10',
                'Title' =>'California',
                'Country' =>'1'
            ],
        11=>
            [   'id'=>'11',
                'Title' =>'Uganda',
                'Country' =>'4'
            ],
        12=>
            [   'id'=>'12',
                'Title' =>'Lagos',
                'Country' =>'5'
            ],
        13=>
            [   'id'=>'13',
                'Title' =>'England',
                'Country' =>'6'
            ],
        14=>
            [   'id'=>'14',
                'Title' =>'Paris',
                'Country' =>'7'
            ],
            
    ];

    public static $countries = [
        1 =>'United States Of America',
        2 =>'India',
        3 =>'Georgia',
        4=>'Uganda',
        5=>'Nigeria',
        6=>'United Kingdom',
        7=>'France'
    ];

    public function mobileStream()
    {
        return $this->hasOne(ClubStream::class, 'id', 'club_id');
    }

    public function webStream()
    {
        return $this->hasOne(ClubWebStream::class, 'id', 'club_id');
    }
}
