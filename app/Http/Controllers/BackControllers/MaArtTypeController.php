<?php


namespace App\Http\Controllers\BackControllers;

use App\Http\Controllers\Controller;
use App\Model\ArticalType;
use App\Model\Type;
use Illuminate\Http\Request;

class MaArtTypeController extends Controller
{
    /**
     * 查询文章类型
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArtType(Request $request)
    {
        return responseToJson(0,'查询成功', Type::selectArtTypeData($request->total));
    }

    /**
     * 添加文章类型
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addArtType(Request $request)
    {
        $data['type_name'] = $request->type_name;
        $data['created_at'] = time();
        $data['type_count'] = 0;
        return Type::addArtTypeData($data) ? responseToJson(0,'添加成功') : responseToJson(1,'添加失败');
    }
    public function deleteArtType(Request $request)
    {
        $type_id_data = $request->type_id_data;
        $is_has_art   = ArticalType::judgeTypeHasArt($type_id_data);
        if($is_has_art['code'] == 1) return responseToJson(1,$is_has_art['msg']);
        return Type::deleteArtTypeData($type_id_data) ? responseToJson(0,'删除文章类型成功') : responseToJson(1,'删除文章类型失败');
    }

    /**
     * 修改文章类型
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateArtType(Request $request)
    {
        return Type::updateArtTypeData($request->type_id, $request->type_name) ? responseToJson(0,'修改成功') : responseToJson(1,'修改失败');
    }
    public function byTimeSelectArtType(Request $request)
    {
        return responseToJson(0,'查询成功',Type::selectArtTypeData($request->total, $request->time));
    }



}