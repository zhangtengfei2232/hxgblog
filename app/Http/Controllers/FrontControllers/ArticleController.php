<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Model\Article;
use App\Model\ArticleType;
use App\Model\Comment;
use App\Model\Exhibit;
use App\Model\PraiseTrample;
use App\Model\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * 显示文章页面
     * @return JsonResponse
     */
    public function showArticlePage()
    {
        $data['art_types'] = Type::selectAllTypeData();
        $art_id_data       = ArticleType::byTypeSelectArticleId($data['art_types'][0]->type_id, 0);
        $data['articles']  = dealFormatResourceURL(Article::byIdSelectArticleData($art_id_data), array(ARTICLE_COVER_FIELD_NAME));
        return responseToJson(0, 'success', $data );
    }



    /**
     * 根据文章类型搜索文章
     * @param Request $request
     * @return JsonResponse
     */
    public function typeSelectArticle(Request $request)
    {
        $page             = ($request->has('page')) ? $request->input('page') : 0;
        $art_id_data      = ArticleType::byTypeSelectArticleId($request->input('type_id'), $page);
        $data['articles'] = dealFormatResourceURL(Article::byIdSelectArticleData($art_id_data), array(ARTICLE_COVER_FIELD_NAME));
        return responseToJson(0, 'success', $data);
    }


    /**
     * 显示文章详情页面
     * @param Request $request
     * @return JsonResponse
     */
    public function showArticleDetail(Request $request)
    {
        $art_id[0] = $request->input('art_id');
        $time = time();
        if (isAddArticleBrowse($art_id[0], $time)) {   //满足条件，浏览量加 '1'
            Article::addArticleBrowseData($art_id[0]);
            session([$art_id[0] => $time]);           //再次存储文章当前访问时间
        }
//        $data['new_articles']          = Article::selectNewArticleData();            //最新文章
//        $data['browse_top']            = Article::selectBrowseTopData();             //浏览最多的文章
        $data['comments']              = dealFormatArticleComment(Comment::selectTopLevelMessage(config('select_field.comment'), '', $art_id[0])); //文章评论
        $data['article_data']          = Article::byIdSelectArticleData($art_id, 2);    //文章数据
        $data['praise_trample_status'] = PraiseTrample::selectArticlePraiseTrample($art_id[0]);
        $data['article_types']         = ArticleType::selectArticleTypeName($art_id[0]);
        $data['music_path']            = dealFormatResourceURL(Exhibit::selectPresentMusicFile(4), array(MUSIC_FIELD_NAME))[0]['exh_name'];
        $data['art_say']               = Exhibit::selectPresentExhibitData(2);
        $music_lyric_path              = storage_path() . DIRECTORY_SEPARATOR . RESOURCE_ROUTE_DIR . MUSIC_LYRIC_FOLDER_NAME . DIRECTORY_SEPARATOR . Exhibit::selectPresentExhibitData(4);
        $data['music_lyric']           = file_get_contents($music_lyric_path);
        return responseToJson(0, 'success', $data);
    }


    /**
     * 添加回复评论
     * @param Request $request
     * @return JsonResponse
     */
    public function sendReplayComment(Request $request){
        if (empty(session('user'))) {
            return responseToJson(1, '请你重新登录!');
        }
        $data['come_content'] = $request->input('replay_content');
        $validate_content     = validateCommentContent($data['come_content']);
        if ($validate_content['code'] == 1) {
            return responseToJson(1, $validate_content['msg']);
        }
        $data['top_level_id']   = $request->input('top_level_id');
        $data['come_father_id'] = $request->input('father_id');
        $data['art_id']         = $request->input('art_id');
        $data['user_id']        = session('user')->user_id;
        $data['created_at']     = time();
        $add_comment            = Comment::addCommentData($data);
        if ($add_comment['code'] == 1) {
            return responseToJson(1, $add_comment['msg']);
        }
        $art_comment = Comment::selectTopLevelMessage(config('select_field.comment'), '', $data['art_id']);//重新查一下文章的评论返回给前台
        return responseToJson(0, $add_comment['msg'], $art_comment);
    }


    /**
     * 添加文章评论
     * @param Request $request
     * @return JsonResponse
     */
    public function addPublishComment(Request $request)
    {
        if (empty(session('user'))) {
            return responseToJson(1, '请你重新登录!');
        }
        $data['come_content'] = $request->input('publish_content');
        $validate_content     = validateCommentContent($data['come_content']);
        if ($validate_content['code'] == 1) {
            return responseToJson(1, $validate_content['msg']);
        }
        $data['come_father_id'] = 0;
        $data['top_level_id']   = 0;
        $data['art_id']         = $request->input('art_id');
        $data['user_id']        = session('user')->user_id;
        $data['created_at']     = time();
        $add_comment            = Comment::addCommentData($data);
        if ($add_comment['code'] == 1) {
            return responseToJson(1, $add_comment['msg']);
        }
        $art_comment = Comment::selectTopLevelMessage(config('select_field.comment'), '', $data['art_id']);
        return responseToJson(0, $add_comment['msg'], $art_comment);
    }


    /**
     * 删除文章评论
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteArticleComment(Request $request)
    {
        Comment::beginTransaction();                          //开启事务
        try {
            $come_id = $request->input('comment_id');
            if (! Comment::deleteData(config('select_field.comment'), $come_id)) {       //中间出现删除失败的也要回滚。
                Comment::rollBack();
                return responseToJson(1, "删除失败");
            }
            $art_id = $request->input('art_id');
            $art_comment = Comment::selectTopLevelMessage(config('select_field.comment'), '', $art_id);
            Comment::commit();                                //提交事务
            return responseToJson(0, "删除成功", $art_comment);
        } catch (\Exception $e) {                               //出现异常也要回滚
            Comment::rollBack();                              //回滚
            return responseToJson(1, "删除失败");
        }
    }


    /**
     * 根据文章顶级ID，去查询所有子评论
     * @param Request $request
     * @return JsonResponse
     */
    public function byTopIdSelectAllComment(Request $request)
    {
        $comment_data = Comment::selectALLChildMessageData(config('select_field.comment'), $request->input('top_level_id'), session('user')->user_id);
        return responseToJson(0, '查询成功', $comment_data);
    }


    /**
     * 根据文章ID和用户手机号，给文章赞/踩
     * @param Request $request
     * @return JsonResponse
     */
    public function praiseOrTrampleArticle(Request $request)
    {
        $praise_trample_num = PraiseTrample::addPraiseTrampleData($request->input('praise_trample_status'), $request->input('art_id'));
        return ($praise_trample_num['code'] == 0) ? responseToJson(0, $praise_trample_num['msg'], $praise_trample_num['data'])
               : responseToJson(1, $praise_trample_num['msg']);
    }


    /**
     * 查询文章所有类型
     * @return JsonResponse
     */
    public function getArticleAllType()
    {
        return responseToJson(0, '查询成功', Type::selectAllTypeData());
    }

}
