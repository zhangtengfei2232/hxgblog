<?php
namespace App\Http\Controllers\BackControllers;

use App\Http\Controllers\Controller;
use App\Model\Artical;
use App\Model\ArticalType;
use App\Model\Comment;
use App\Model\Type;
use Illuminate\Http\Request;

class MaArticalController extends Controller
{
    /**
     * 添加文章信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addArtical(Request $request)
    {
        if(!$request->isMethod('POST')) return responseToJson(1,'你请求的方式不对');
        $data['arti_title']   = $request->arti_title;
        $data['arti_content'] = $request->arti_content;
        $validate_data = validateArticalData($data);
        if(empty($request->arti_type)) return responseToJson(1,'你没有选择文章类型');
        if($validate_data['code'] == 1) return responseToJson(1,$validate_data['msg']);
        if(! $request->hasFile('art_cover')) return responseToJson(1,'请你上传文章封面');
        $art_cover = $request->art_cover;
        $validate_art_cover = judgeReceiveFiles($art_cover);
        if($validate_art_cover['code'] == 1) return responseToJson(1,$validate_art_cover['msg']);
        $disk = config('upload.artical');
        $upload_art_cover = uploadFile($art_cover, $disk);
        if($upload_art_cover['code'] == 1) return responseToJson(1,$upload_art_cover['msg']);
        $data['arti_cover'] = $upload_art_cover['data'];
        $data['created_at']          = time();
        Artical::beginTransaction();
        try{
            $add_art      = Artical::addArticalData($data);
            $art_type     = explode(',',$request->arti_type);
            $add_art_type = ArticalType::insertArticalTypeData($add_art, $art_type);
            $update_type_num = Type::increaseArticalTypeNum($art_type);
            if($add_art && $add_art_type && $update_type_num){
                Artical::commit();
                return responseToJson(0,'添加文章成功');
            }
        }catch (\Exception $e){
            Artical::rollBack();
            deleteFile($data['arti_cover'], $disk);
            return responseToJson(1,'添加文章失败');
        }
    }

    /**
     * 删除文章所有信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteArtical(Request $request)
    {
        $art_id_data = $request->art_id_data;
        Artical::beginTransaction();
        $art_cover_road = Artical::selectArticalCoverRoad($art_id_data);
        $type_id_num    = ArticalType::selectArtTypeNum($art_id_data);
        try{
            $del_art_all_infor = Artical::deleteArticalRelevantData(config('deleteartical'), $art_id_data);
            $updat_type_num    = Type::reduceArticalTypeNum($type_id_num);
            if($del_art_all_infor && $updat_type_num){
                Artical::commit();
                deleteMultipleFile($art_cover_road, config('upload.artical'));  //删除文章的封面
                return responseToJson(0,'删除文章成功');
            }
        }catch (\Exception $e){
            Artical::rollBack();
            return responseToJson(1,"删除文章失败");
        }

        return responseToJson(0,"删除文章失败");
    }

    /**
     * 修改文章
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateArtical(Request $request)
    {
        if(!$request->isMethod('POST')) return responseToJson(1,'请求方式不对');
        $data['arti_title']   = $request->arti_title;
        $data['arti_content'] = $request->arti_content;
        $validate_data = validateArticalData($data);
        if($validate_data['code'] == 1) return responseToJson(1,$validate_data['msg']);
        $data['arti_id']   = $request->arti_id;
        ($request->is_update_cover == "true") ? $is_update_cover = true : $is_update_cover = false;
        if($is_update_cover) {
            $art_cover = $request->art_cover;
            $validate_art_cover = judgeReceiveFiles($art_cover);
            if($validate_art_cover['code'] == 1) return responseToJson(1,$validate_art_cover['msg']);
            $disk = config('upload.artical');
            $upload_art_cover = uploadFile($art_cover, $disk);
            if($upload_art_cover['code'] == 1) return responseToJson(1,$upload_art_cover['msg']);
            $data['arti_cover'] = $upload_art_cover['data'];
        }
        Artical::beginTransaction();
        try{
            $art_type = explode(',',$request->arti_type);
            $orig_art_type = explode(',', $request->orig_arti_type);
            $update_art = Artical::updateArticalData($data);
            $update_art_type = ArticalType::updateArticalTypeData($data['arti_id'], $art_type, $orig_art_type);
            if($update_art && $update_art_type){
                if($is_update_cover) {                   //如果更改文章封面，删除以前的
                    if(! deleteFile($request->arti_cover,$disk)){
                        Artical::rollBack();
                        return responseToJson(1,'修改失败');
                    }
                }
                Artical::commit();
                return responseToJson(0,'修改成功');
            }
            //更新失败，删除上传成功的文章封面
            if($is_update_cover) deleteFile($upload_art_cover, $disk);
        }catch (\Exception $e){
            Artical::rollBack();
            //出现异常，删除上传成功的文章封面
            if($is_update_cover) deleteFile($upload_art_cover, $disk);
            return responseToJson(1,'修改失败');
        }
    }

    /**
     * 获取文章列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArtical(Request $request)
    {
        $data['art_data']      = Artical::getArticalData($request->total);
        $data['art_type_data'] = Type::selectAllTypeData();
        return responseToJson(0,'查询成功', $data);
    }
    /**
     * 组合查询文章
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function combinateSelectArtical(Request $request)
    {
        (!empty($request->time)) ? $time = $request->time : $time = "";
        (!empty($request->art_name)) ? $art_name = $request->art_name : $art_name = "";
        return responseToJson(0,'查询成功',Artical::selectArticalData($art_name, $time, $request->total));
    }

    /**
     * 根据文章类型搜索
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byTypeSelectArtical(Request $request)
    {
        $art_id_data = [];
        $data = ArticalType::byTypeIdselectArticalId($request->type_id_data, $request->total);
        $data = json_decode(json_encode($data));
        for($i = 0; $i < count($data->data); $i++) $art_id_data[$i] = ($data->data)[$i]->arti_id;
        $information['art_data'] = Artical::byIdSelectArticalData($art_id_data ,2);
        $information['total']    = $data->total;
        return responseToJson(0,'查询成功', $information);
    }

    /**
     * 查询单个文章信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAloneArtical(Request $request)
    {
        $art_type_id_data = [];
        $art_id = $request->art_id;
        $art_data['artical']       = Artical::selectAloneArticalData($art_id);
        $art_data['art_type']      = ArticalType::selectArticalTypeId($art_id);
        foreach ($art_data['art_type'] as $type) array_push($art_type_id_data, $type->type_id);
        $art_data['art_type'] = $art_type_id_data;
        $art_data['art_type_data'] = Type::selectAllTypeData();
        return responseToJson(0,'查询成功', $art_data);
    }




}