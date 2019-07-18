<?php

namespace App\Model;


class ArticalType extends BaseModel
{
    protected $table = 'artical_type';

    /**
     * 根据文章类型ID去查文章ID
     * @param $art_type_id
     * @param $page
     * @return mixed
     */
    public static function byTypeSelectArticalId($art_type_id, $page)
    {
        $page = $page * 2;
        return ArticalType::select('arti_id')->where('type_id', $art_type_id)
               ->offset($page)->limit(2)->get();
    }

    /**
     * 查询文章的文章类型ID
     * @param $art_id
     * @return mixed
     */
    public static function selectArticalTypeName($art_id)
    {
        return ArticalType::select('type_name')->leftJoin('Type', 'type.type_id', '=', 'artical_type.type_id')
               ->where('arti_id', $art_id)->get();
    }

    public static function selectArticalTypeId($art_id)
    {
        return ArticalType::select('type_id')->where('arti_id', $art_id)->get();
    }
    /**
     * 根据文章类型ID数组，查询文章ID
     */
    public static function byTypeIdselectArticalId($type_id_data, $total)
    {
        return ArticalType::select('arti_id')->whereIn('type_id', $type_id_data)->paginate($total);
    }

    /**
     * 修改文章的类型
     * @param $art_id
     * @param $type_id_data
     * @return bool
     */
    public static function updateArticalTypeData($art_id, $type_id_data, $orig_art_type)
    {
        $del_type = array_diff($orig_art_type, $type_id_data);
        $ins_type = array_diff($type_id_data, $orig_art_type);
        if(!empty($ins_type)){
            if(! self::insertArticalTypeData($art_id, $ins_type)) return false;
        }
        if(!empty($del_type)){
            if(! self::deleteArticalTypeData($art_id, $del_type)) return false;
        }
        return true;
    }

    /**
     * 添加单个文章的类型
     * @param $art_id
     * @param $type_id_data
     * @return bool
     */
    public static function insertArticalTypeData($art_id, $type_id_data)
    {
        foreach ($type_id_data as $ins_type_id){
            if(! ArticalType::insert(['arti_id' => $art_id,'type_id' => $ins_type_id])) return false;
        }
        return true;
    }

    /**
     * 删除单个文章的类型
     * @param $art_id
     * @param $type_id_data
     * @return bool
     */
    public static function deleteArticalTypeData($art_id, $type_id_data)
    {
        foreach ($type_id_data as $del_type_id){
            if(ArticalType::where([['arti_id',$art_id], ['type_id',$del_type_id]])->delete() == 0) return false;
        }
        return true;
    }

    /**
     * 删除文章时候，查询文章类型数量 | 以 'type_id实际值'作为数组索引，对应的数量作为值
     * @param $art_id_data
     * @return array
     */
    public static function selectArtTypeNum($art_id_data)
    {
        $art_type_data = [];
        $art_type_id = ArticalType::select('type_id')->whereIn('arti_id', $art_id_data)->get()->toArray();
        foreach ($art_type_id as $type_id){
            if(array_key_exists($type_id['type_id'],$art_type_data)){
                $art_type_data[$type_id['type_id']] = $art_type_data[$type_id['type_id']] + 1;
                continue;
            }
            $art_type_data[$type_id['type_id']] = 1;
        }
        return $art_type_data;
    }

}