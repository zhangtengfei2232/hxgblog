<?php

namespace App\Model;


class Comment extends BaseModel
{
    protected $table = 'comment';
    protected static $select_field = ['come_id', 'come_content', 'come_father_id', 'comment.created_at', 'nick_name', 'head_portrait','comment.phone'];
    static $comment_data = [];
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

}