<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Model\Artical;
use Illuminate\Http\Request;

class showArticalController extends Controller
{
    //显示某一篇文章
    public function showAloneArtical(Request $request)
    {
        $datas['artical']     = Artical::selectAloneArticalData($request->art_id);
        $datas['new_artical'] = Artical::selectNewArticalData();
        $datas['browse_top']  = Artical::selectBrowseTopData();


    }

}