<?php

namespace App\Model;

class ArticalType extends BaseModel
{
    protected $table = 'artical_type';
    //根据文章类型ID去查文章ID
    public static function byTypeSelectArticalId($art_type_id, $page)
    {
        $page = $page * 3;
        return ArticalType::select('arti_id')->where('type_id', $art_type_id)
               ->offset($page)->limit(3)->get();
    }



}