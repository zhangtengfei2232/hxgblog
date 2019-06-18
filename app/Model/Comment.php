<?php

namespace App\Model;

class Comment extends BaseModel
{
    protected $table = 'comment';
    protected static $select_field = ['come_id', 'come_content', 'come_father_id', 'comment.created_at', 'nick_name', 'head_portrait','comment.phone'];
    static $comment_data = [];

    /**
     * 添加文章评论
     * @param $comment_data
     * @return bool
     */
    public static function addCommentData($comment_data)
    {
        try{
            Comment::insert($comment_data);
        }catch (\Exception $e){
            return responseState(1,'评论失败');
        }
        return responseState(0,'评论成功');
    }
    /**
     * 查某一篇文章的所有顶级评论
     * @param $art_id
     * @return mixed
     */
    public static function selectTopLevelComment($art_id)
    {
        $comment_data = Comment::select(self::$select_field)
                      ->leftJoin('users', 'comment.phone', '=', 'users.phone')
                      ->where([['come_father_id', 0],['arti_id', $art_id]])->get()->toArray();
        (!empty(session()->has('user'))) ? $user_phone = session('user')->phone : $user_phone = " ";
        foreach ($comment_data as $key => $value){
            $child_comment = self::selectALLChildCommentData($comment_data[$key]['come_id'], $user_phone);
            $comment_data[$key]['come_count'] = count($child_comment);
            $comment_data[$key]['child_comment'] = $child_comment;
            ($comment_data[$key]['phone'] == $user_phone) ? $comment_data[$key]['is_mine'] = true : $comment_data[$key]['is_mine'] = false;
            self::$comment_data = [];    //因为是static，所以每次查询都要清空子评论内容
        }
        return $comment_data;
    }

    /**
     * 查询某一条顶级评论的子评论数
     * @param $comment_data
     * @return mixed
     */
    public static function selectChildCommentNum($comment_data)
    {
        foreach ($comment_data as $key => $value)
            $comment_data[$key]['come_count'] = Comment::where('top_level_id', $comment_data[$key]['come_id'])->count();
        return $comment_data;
    }

    /**
     * 根据文章评论顶级ID，查询所有子评论
     * @param $father_id
     * @return array|void
     */
    public static function selectALLChildCommentData($father_id, $user_phone)
    {
        $comment = Comment::select(self::$select_field)
                    ->leftJoin('users', 'comment.phone', '=', 'users.phone')
                    ->where('come_father_id', $father_id)->get()->toArray();
        if(empty($comment)) return ;
        foreach ($comment as $key => $data){
            $father_infor = Comment::select('nick_name','users.phone')
                ->leftJoin('users', 'comment.phone', '=', 'users.phone')
                ->where('come_id',$comment[$key]['come_father_id'])
                ->first();
            $comment[$key]['father_nick_name'] = $father_infor->nick_name;
            $comment[$key]['father_phone'] = $father_infor->phone;
            ($comment[$key]['phone'] == $user_phone) ? $comment[$key]['is_mine'] = true : $comment[$key]['is_mine'] = false;
        }
        foreach ($comment as $value) {
            array_push(self::$comment_data, $value);                  //把查询的数据放到数组中
            self::selectALLChildCommentData($value['come_id'], $user_phone); //递归子查询
        }
        return self::$comment_data;
    }

    /**
     * 判断此评论是否为顶级评论
     * @return bool
     */
    public  static function isTopLevelComment($come_id):bool
    {
        return (Comment::where([['come_id', $come_id],['top_level_id', 0]])->count() > 0) ;
    }

    /**
     * 删除文章评论
     * @param $come_id
     * @return mixed
     */
    public static function deleteCommentData($come_id)
    {
        $children_come_id_data = Comment::select('come_id')->where('come_father_id',$come_id)->get();
        //当前评论没有子评论
        if($children_come_id_data->isEmpty())  return Comment::where('come_id',$come_id)->delete();
        foreach ($children_come_id_data as $come_id_data) $is_delete = self::deleteCommentData($come_id_data->come_id);
        if(! $is_delete) return $is_delete;     //如果其中有一个删除失败，直接返回false
        return Comment::where('come_id',$come_id)->delete();
    }

}