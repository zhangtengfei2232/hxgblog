<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Collection;

class Type extends BaseModel
{
    protected $table = 'type';
    const UPDATED_AT = null;


    /**
     * 添加文章类型
     * @param $data
     * @return mixed
     */
    public static function addArtTypeData($data)
    {
        return Type::insert($data);
    }


    /**
     * 删除文章类型
     * @param $type_id_data
     * @return bool
     */
    public static function deleteArtTypeData($type_id_data)
    {
        return Type::whereIn('type_id', $type_id_data)->delete() == count($type_id_data);
    }


    /**
     * 修改文章类型
     * @param $type_id
     * @param $type_name
     * @return bool
     */
    public static function updateArtTypeData($type_id, $type_name)
    {
        return Type::where('type_id', $type_id)->update(['type_name' => $type_name]) > 0;
    }


    /**
     * 查文章的所有标签
     * @return Type[]|Collection
     */
    public static function selectAllTypeData()
    {
        return Type::all();
    }


    /**
     * 查询文章类型
     * @param $total
     * @param array $time
     * @return Type
     */
    public static function selectArtTypeData($total, $time = [])
    {
        $typeQuery = new Type();
        if (! empty($time)) {
            $typeQuery = $typeQuery->whereBetween('created_at', $time);
        }
        $typeQuery = $typeQuery->paginate($total);
        return $typeQuery;
    }


    /**
     * 增加文章类型对应的类型总数
     * @param $type_id_data
     * @return bool
     */
    public static function increaseArticleTypeNum($type_id_data)
    {
        foreach ($type_id_data as $type_id){
            $type_num = Type::select('type_count')->where('type_id', $type_id)->first()->type_count + 1;
            if (Type::where('type_id', $type_id)->update(['type_count' => $type_num]) == 0) {
                return false;
            }
        }
        return true;
    }


    /**
     * 删除文章，减少文章类型对应的总数
     * @param $type_id_num
     * @return bool
     */
    public static function reduceArticleTypeNum($type_id_num)
    {
        foreach ($type_id_num as $key => $type_count){
            $ori_type_num = Type::select('type_count')->where('type_id', $key)->first()->type_count;
            if (Type::where('type_id', $key)->update(['type_count' => ($ori_type_num - $type_count)]) == 0) {
                return false;
            }
        }
        return true;
    }

}
