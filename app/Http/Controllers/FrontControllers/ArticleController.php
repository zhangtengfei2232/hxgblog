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
        $datas['art_types'] = Type::selectAllTypeData();
        $art_id_datas = ArticleType::byTypeSelectArticleId($datas['art_types'][0]->type_id, 0);
        $datas['articles'] = Article::byIdSelectArticleData($art_id_datas);
        return responseToJson(0,'success',$datas);
    }

    /**d
     * 根据文章类型搜索文章
     * @param Request $request
     * @return JsonResponse
     */
    public function typeSelectArticle(Request $request)
    {
        ($request->has('page')) ? $page = $request->page : $page = 0;
        $art_id_datas = ArticleType::byTypeSelectArticleId($request->type_id, $page);
        $datas['articles'] = Article::byIdSelectArticleData($art_id_datas);
        return responseToJson(0,'success', $datas);
    }

    /**
     * 显示文章详情页面
     * @param Request $request
     * @return JsonResponse
     */
    public function showArticleDetail(Request $request)
    {
        $art_id[0] = $request->art_id;
        $time = time();
        if (isAddArticleBrowse($art_id[0], $time)) {   //满足条件，浏览量加 '1'
            Article::addArticleBrowseData($art_id[0]);
            session([$art_id[0] => $time]);           //再次存储文章当前访问时间
        }
        $datas['new_articles']          = Article::selectNewArticleData();            //最新文章
        $datas['browse_top']            = Article::selectBrowseTopData();             //浏览最多的文章
        $datas['comments']              = Comment::selectTopLevelMessage(config('selectfield.comment'),'', $art_id[0]); //文章评论
        $datas['article_data']          = Article::byIdSelectArticleData($art_id, 2);    //文章数据
        $datas['praise_trample_status'] = PraiseTrample::selectArticlePraiseTrample($art_id[0]);
        $datas['article_types']         = ArticleType::selectArticleTypeName($art_id[0]);
        $datas['music_path']            = Exhibit::selectPresentMusicFile(4);
        $datas['art_say']               = Exhibit::selectPresentExhibitData(2);
        $music_lyric_path               = storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'music_lyric'.DIRECTORY_SEPARATOR.Exhibit::selectPresentExhibitData(4);
        $datas['music_lyric']           = file_get_contents($music_lyric_path);
        return responseToJson(0,'success', $datas);
    }

    /**
     * 添加回复评论
     * @param Request $request
     * @return JsonResponse
     */
    public function sendReplayComment(Request $request){
        if (empty(session('user'))) {
            return responseToJson(1,'请你重新登录!');
        }
        $data['come_content'] = $request->replay_content;
        $validate_content     = validateCommentContent($data['come_content']);
        if ($validate_content['code'] == 1) {
            return responseToJson(1, $validate_content['msg']);
        }
        $data['top_level_id']   = $request->top_level_id;
        $data['come_father_id'] = $request->father_id;
        $data['arti_id']        = $request->art_id;
        $data['user_id']        = session('user')->user_id;
        $data['created_at']     = time();
        $add_comment            = Comment::addCommentData($data);
        if ($add_comment['code'] == 1) {
            return responseToJson(1,$add_comment['msg']);
        }
        $art_comment = Comment::selectTopLevelMessage(config('selectfield.comment'),'', $data['arti_id']);//重新查一下文章的评论返回给前台
        return responseToJson(0,$add_comment['msg'], $art_comment);
    }

    /**
     * 添加文章评论
     * @param Request $request
     * @return JsonResponse
     */
    public function addPublishComment(Request $request)
    {
        if (empty(session('user'))) {
            return responseToJson(1,'请你重新登录!');
        }
        $data['come_content'] = $request->publish_content;
        $validate_content     = validateCommentContent($data['come_content']);
        if ($validate_content['code'] == 1) {
            return responseToJson(1, $validate_content['msg']);
        }
        $data['come_father_id'] = 0;
        $data['top_level_id']   = 0;
        $data['arti_id']        = $request->art_id;
        $data['user_id']          = session('user')->user_id;
        $data['created_at']     = time();
        $add_comment            = Comment::addCommentData($data);
        if ($add_comment['code'] == 1) {
            return responseToJson(1, $add_comment['msg']);
        }
        $art_comment = Comment::selectTopLevelMessage(config('selectfield.comment'),'', $data['arti_id']);
        return responseToJson(0,$add_comment['msg'], $art_comment);
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
            $come_id = $request->comment_id;
            if(! Comment::deleteData(config('selectfield.comment'), $come_id)){       //中间出现删除失败的也要回滚。
                Comment::rollBack();
                return responseToJson(1,"删除失败");
            }
            $art_id = $request->art_id;
            $art_comment  = Comment::selectTopLevelMessage(config('selectfield.comment'),'', $art_id);
            Comment::commit();                                //提交事务
            return responseToJson(0,"删除成功",$art_comment);
        } catch (\Exception $e) {                               //出现异常也要回滚
            Comment::rollBack();                              //回滚
            return responseToJson(1,"删除失败");
        }
    }

    /**
     * 根据文章顶级ID，去查询所有子评论
     * @param Request $request
     * @return JsonResponse
     */
    public function byTopIdSelectAllComment(Request $request)
    {
        $comment_data = Comment::selectALLChildMessageData(config('selectfield.comment'), $request->top_level_id, session('user')->user_id);
        return responseToJson(0,'查询成功', $comment_data);
    }

    //根据文章ID和用户手机号，给文章赞/踩
    public function praiseOrTrampleArticle(Request $request)
    {
        $praise_trample_num = PraiseTrample::addPraiseTrampleData($request->praise_trample_status, $request->art_id);
        return ($praise_trample_num['code'] == 0) ? responseToJson(0,$praise_trample_num['msg'],$praise_trample_num['data'])
               : responseToJson(1,$praise_trample_num['msg']);
    }

    public function getArticleAllType()
    {
        return responseToJson(0,'查询成功', Type::selectAllTypeData());
    }

}
