<?php

namespace App\Model;

class Comment extends BaseModel
{
    protected $table = 'comment';
    static $comment_data = [];

    /**
     * 添加文章评论
     * @param $comment_data
     * @return bool
     */
    public static function addCommentData($comment_data)
    {
        try {
            Comment::insert($comment_data);
        } catch (\Exception $e) {
            return responseState(1, '评论失败');
        }
        return responseState(0, '评论成功');
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
     * 判断此评论是否为顶级评论
     * @param $come_id
     * @return bool
     */
    public  static function isTopLevelComment($come_id):bool
    {
        return (Comment::where([['come_id', $come_id],['top_level_id', 0]])->count() > 0) ;
    }


}
