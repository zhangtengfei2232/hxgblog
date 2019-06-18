<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Model\Artical;
use App\Model\ArticalType;
use App\Model\Comment;
use App\Model\Type;
use Illuminate\Http\Request;

class ArticalController extends Controller
{
    /**
     * 显示文章页面
     * @return \Illuminate\Http\JsonResponse
     */
    public function showArticalPage()
    {
        $datas['art_types'] = Type::selectAllTypeData();
        $art_id_datas = ArticalType::byTypeSelectArticalId($datas['art_types'][0]->type_id, 0);
        $datas['articals'] = Artical::byIdSelectArticalData($art_id_datas);
        return responseToJson(0,'success',$datas);
    }

    /**d
     * 根据文章类型搜索文章
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byTypeSelectArtical(Request $request)
    {
        ($request->has('page')) ? $page = $request->page : $page = 0;
        $art_id_datas = ArticalType::byTypeSelectArticalId($request->type_id, $page);
        $datas['articals'] = Artical::byIdSelectArticalData($art_id_datas);
        return responseToJson(0,'success', $datas);
    }

    /**
     * 显示文章详情页面
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showArticalDetail(Request $request)
    {
        $art_id[0] = $request->art_id;
        $time = time();
        if(isAddArticalBrowse($art_id[0], $time)) {   //满足条件，浏览量加 '1'
            Artical::addArticalBrowseData($art_id[0]);
            session([$art_id[0] => $time]);           //再次存储文章当前访问时间
        }
        $datas['new_articals'] = Artical::selectNewArticalData();
        $datas['browse_top']   = Artical::selectBrowseTopData();
        $datas['comments']     = Comment::selectTopLevelComment($art_id[0]);
        $datas['artical_data'] = Artical::byIdSelectArticalData($art_id);
        return responseToJson(0,'success', $datas);
    }

    //根据文章名字模糊查询文章
    public function byNameSelectArtical(Request $request)
    {



    }

    /**
     * 添加回复评论
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReplayComment(Request $request){
        if(empty(session('user'))) return responseToJson(1,'请你重新登录!');
        $data['come_content']   = $request->replay_content;
        $validate_content       = validateCommentContent($data['come_content']);
        if($validate_content['code'] == 1) return responseToJson(1, $validate_content['msg']);
        $data['top_level_id']   = $request->top_level_id;
        $data['come_father_id'] = $request->father_id;
        $data['arti_id']        = $request->art_id;
        $data['phone']          = session('user')->phone;
        $data['created_at']     = time();
        $add_comment            = Comment::addCommentData($data);
        if($add_comment['code'] == 1) responseToJson(1,$add_comment['msg']);
        $art_comment = Comment::selectTopLevelComment($data['arti_id']);//重新查一下文章的评论返回给前台
        return responseToJson(0,$add_comment['msg'], $art_comment);
    }

    /**
     * 添加文章评论
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPublishComment(Request $request)
    {
        if(empty(session('user'))) return responseToJson(1,'请你重新登录!');
        $data['come_content']   = $request->publish_content;
        $validate_content       = validateCommentContent($data['come_content']);
        if($validate_content['code'] == 1) return responseToJson(1, $validate_content['msg']);
        $data['come_father_id'] = 0;
        $data['top_level_id']   = 0;
        $data['arti_id']        = $request->art_id;
        $data['phone']          = session('user')->phone;
        $data['created_at']     = time();
        $add_comment            = Comment::addCommentData($data);
        if($add_comment['code'] == 1) responseToJson(1, $add_comment['msg']);
        $art_comment            = Comment::selectTopLevelComment($data['arti_id']);
        return responseToJson(0,$add_comment['msg'], $art_comment);
    }

    /**
     * 删除文章评论
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteArticalComment(Request $request)
    {
        Comment::beginTransaction();                          //开启事务
        try{
            $come_id = $request->comment_id;
            if(! Comment::deleteCommentData($come_id)){       //中间出现删除失败的也要回滚。
                Comment::rollBack();
                return responseToJson(1,"删除失败");
            }
            $art_id = $request->art_id;
            $art_comment  = Comment::selectTopLevelComment($art_id);
            Comment::commit();                                //提交事务
            return responseToJson(0,"删除成功",$art_comment);
        }catch (\Exception $e){                               //出现异常也要回滚
            Comment::rollBack();                              //回滚
            return responseToJson(1,"删除失败");
        }
    }

    /**
     * 根据文章顶级ID，去查询所有子评论
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byTopIdselectAllComment(Request $request)
    {
        $comment_data = Comment::selectALLChildCommentData($request->top_level_id, session('user')->phone);
        return responseToJson(0,'查询成功', $comment_data);
    }

    //根据文章ID和用户手机号，给文章点评
    public function praiseOrTrampleArtical(Request $request)
    {


    }



}