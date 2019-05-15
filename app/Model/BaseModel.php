<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    protected $casts = [
        'created_at'   => 'date:Y-m-d',
        'updated_at'   => 'datetime:Y-m-d',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'updated_token_at',
    ];

    /**
     * @param mixed $value
     * @return false|int|null|string
     */
    public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }
    //文章时间拆分函数
    public static function timeResolution($data)
    {
        $data = json_decode(json_encode($data));
        for($i = 0; $i < count($data); $i++){
            $data[$i] = (array)$data[$i];
            $time = explode('-',$data[$i]['created_at']);
            $data[$i]['years']     = $time[0];
            $data[$i]['monthDay'] = $time[1] . '-' . $time[2];
            unset($data[$i]['created_at']);
        }
        return $data;
    }
    //开启事务
    public static function beginTransaction()
    {
        return DB::beginTransaction();
    }
    //没有异常，提交事务
    public static function commit()
    {
        return DB::commit();
    }
    //回滚
    public static function rollBack()
    {
        return DB::rollBack();
    }

}