<?php

namespace App\Model;


class Type extends BaseModel
{
    protected $table = 'type';

    //查文章的所有标签
    public static function selectAllTypeData()
    {
        return Type::all();
    }


}