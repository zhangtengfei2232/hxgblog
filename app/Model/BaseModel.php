<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    //转化时间的公共函数
    public static function convertTime($data, $time_field, $status = false)
    {
        if($status) return self::timeResolution($data);
        foreach ($data as $item) $item->$time_field = date('Y-m-d',$item->$time_field);
        return $data;
    }
    //文章时间拆分函数
    public static function timeResolution($data)
    {
        $data = json_decode(json_encode($data));
        for($i = 0; $i < count($data); $i++){
            $data[$i] = (array)$data[$i];
            $time = explode('-',date('Y-m-d', $data[$i]['arti_create_time']));
            $data[$i]['year']     = $time[0];
            $data[$i]['monthDay'] = $time[1] . '-' . $time[2];
            unset($data[$i]['arti_create_time']);
        }
        return $data;
    }

}