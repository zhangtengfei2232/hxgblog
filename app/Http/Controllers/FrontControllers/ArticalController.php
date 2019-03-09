<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Model\Artical;
use App\Model\ArticalType;
use App\Model\Type;
use Illuminate\Http\Request;

class ArticalController extends Controller
{
    //根据文章类型搜索文章
    public function byTypeSelectArtical(Request $request)
    {
        ($request->has('art_type_id')) ? $art_type_id = $request->art_type_id : $art_type_id = 1;
        ($request->has('total')) ? $page = $request->page : $page = 0;
        $art_id_datas      = ArticalType::byTypeSelectArticalId($art_type_id, $page);
        $datas['articals'] = Artical::byIdSelectArticalData($art_id_datas);
        $datas['art_types'] = Type::selectAllTypeData();
        return responseToJson(0,'success',$datas);
    }



}