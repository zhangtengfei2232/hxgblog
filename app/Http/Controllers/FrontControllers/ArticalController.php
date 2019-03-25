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
    //显示文章页面
    public function showArticalPage()
    {
        $datas['art_types'] = Type::selectAllTypeData();
        $art_id_datas = ArticalType::byTypeSelectArticalId($datas['art_types'][0]->type_id, 0);
        $datas['articals'] = Artical::byIdSelectArticalData($art_id_datas);
        return responseToJson(0,'success',$datas);
    }
    //根据文章类型搜索文章
    public function byTypeSelectArtical(Request $request)
    {
        ($request->has('page')) ? $page = $request->page : $page = 0;
        $art_id_datas = ArticalType::byTypeSelectArticalId($request->type_id, $page);
        $datas['articals'] = Artical::byIdSelectArticalData($art_id_datas);
        return responseToJson(0,'success', $datas);
    }
    //显示文章详情页面
    public function showArticalDetail(Request $request)
    {
        $art_id[0] = $request->art_id;
        $time   = time();
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


}