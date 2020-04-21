<?php

namespace App\Model;


class ArticleType extends BaseModel
{
    protected $table = 'article_type';

    /**
     * 根据文章类型ID去查文章ID
     * @param $art_type_id
     * @param $page
     * @return mixed
     */
    public static function byTypeSelectArticleId($art_type_id, $page)
    {
        $page = $page * 2;
        return ArticleType::select('art_id')->where('type_id', $art_type_id)
               ->offset($page)->limit(2)->get();
    }


    /**
     * 查询文章的文章类型ID
     * @param $art_id
     * @return mixed
     */
    public static function selectArticleTypeName($art_id)
    {
        return ArticleType::select('type_name')->leftJoin('type', 'type.type_id', '=', 'article_type.type_id')
               ->where('art_id', $art_id)->get();
    }


    /**
     *查询文章对应的类型
     * @param $art_id
     * @return mixed
     */
    public static function selectArticleTypeId($art_id)
    {
        return ArticleType::select('type_id')->where('art_id', $art_id)->get();
    }


    /**
     * 根据文章类型ID数组，查询文章ID
     * @param $type_id_data
     * @param $total
     * @return
     */
    public static function byTypeIdselectArticleId($type_id_data, $total)
    {
        return ArticleType::select('art_id')->whereIn('type_id', $type_id_data)->paginate($total);
    }


    /**
     * 修改文章的类型
     * @param $art_id
     * @param $type_id_data
     * @param $orig_art_type
     * @return bool
     */
    public static function updateArticleTypeData($art_id, $type_id_data, $orig_art_type)
    {
        $del_type = array_diff($orig_art_type, $type_id_data);
        $ins_type = array_diff($type_id_data, $orig_art_type);
        if (! empty($ins_type) && ! self::insertArticleTypeData($art_id, $ins_type)) {
            return false;
        }
        if (! empty($del_type) && ! self::deleteArticleTypeData($art_id, $del_type)) {
            return false;
        }
        return true;
    }


    /**
     * 添加单个文章的类型
     * @param $art_id
     * @param $type_id_data
     * @return bool
     */
    public static function insertArticleTypeData($art_id, $type_id_data)
    {
        foreach ($type_id_data as $ins_type_id){
            if(! ArticleType::insert(['art_id' => $art_id, 'type_id' => $ins_type_id])) return false;
        }
        return true;
    }


    /**
     * 删除单个文章的类型
     * @param $art_id
     * @param $type_id_data
     * @return bool
     */
    public static function deleteArticleTypeData($art_id, $type_id_data)
    {
        foreach ($type_id_data as $del_type_id){
            if (ArticleType::where([['art_id', $art_id], ['type_id', $del_type_id]])->delete() == 0) {
                return false;
            }
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
        $art_type_id = ArticleType::select('type_id')->whereIn('art_id', $art_id_data)->get()->toArray();
        foreach ($art_type_id as $type_id){
            if (array_key_exists($type_id['type_id'], $art_type_data)) {
                $art_type_data[$type_id['type_id']] = $art_type_data[$type_id['type_id']] + 1;
                continue;
            }
            $art_type_data[$type_id['type_id']] = 1;
        }
        return $art_type_data;
    }


    /**
     * 判断类型是否有文章存在
     * @param $type_id_data
     * @return mixed
     */
    public static function judgeTypeHasArt($type_id_data)
    {
        foreach ($type_id_data as $key => $id) {
            $is_has_art = ArticleType::where('type_id', $id)->count() > 0;
            if ($is_has_art) {
                return responseState(1, '你所选类型有文章，无法删除！');
            }
        }
        return responseState(0, '验证通过');
    }

}
