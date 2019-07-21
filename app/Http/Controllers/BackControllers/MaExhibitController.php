<?php
namespace App\Http\Controllers\BackControllers;

use App\Http\Controllers\Controller;
use App\Model\Exhibit;
use Illuminate\Http\Request;

class MaExhibitController extends Controller
{
    /**
     * 添加展览内容
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addExhibit(Request $request)
    {
        if(! $request->isMethod('POST')) return responseToJson(1,'你请求的方式不对');
        $is_has_music = false;
        if($request->hasFile('exht_music')){                  //判断是否为音乐上传
            $music_file = $request->file('exht_music');
            $is_has_music = true;
            $disk = config('upload.music');
            $validate_music = judgeReceiveFiles($music_file, 2);
            if($validate_music['code'] == 1) return responseToJson(1,'音乐文件上传不合法');
            $upload_music = uploadFile($music_file, $disk, true);
            if($upload_music['code'] == 1) return responseToJson(1,'添加音乐文件失败');
            $data['exht_name']    = $upload_music['data'][0];
            $data['exht_content'] = $upload_music['data'][1];
        }else{
            $data['exht_content'] = $request->exht_content;
            $data['exht_name']    = $request->exht_name;
        }
        $validate_data = validateExhibit($data);
        if($validate_data['code'] == 1) return responseToJson(1,$validate_data['msg']);
        $data['exht_distinguish'] = $request->exht_dist;
        $data['created_at']       = time();
        ($is_has_music) ? $msg = "上传音乐失败" : $msg = "添加名言失败";
        Exhibit::beginTransaction();
        try{
            if(Exhibit::addExhibitData($data)){
                Exhibit::commit();
                ($is_has_music) ? $msg = "上传音乐成功" : $msg = "添加名言成功";
                return responseToJson(0,$msg);
            }
        }catch (\Exception $e){
            if($is_has_music){
                deleteFile($data['exht_content'],$disk);  //数据库更新失败，上传成功的音乐，删除
            }
            Exhibit::rollBack();
            return responseToJson(1,$msg);
        }
        return responseToJson(1,$msg);
    }


    public function deleteMotto()
    {

    }

    /**
     * 修改展览内容
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateExhibit(Request $request)
    {
        $data['exht_id'] = $request->exht_id;
        $data['exht_content'] = $request->exht_content;
        $data['exht_name'] = $request->exht_name;
        $validate_data = validateExhibit($data);
        if($validate_data['code'] == 1) return responseToJson(1,$validate_data['msg']);
        return (Exhibit::updateExhibitData($data)) ? responseToJson(0,'修改名言成功') : responseToJson(1,'修改名言成功');
    }

    /**
     * 根据展览类别查询内容
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectExhibit(Request $request)
    {
        return responseToJson(0,'查询成功',Exhibit::selectExhibitData($request->exht_dist, $request->total, $request->page));

    }

    /**
     * 查询单个展览内容
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectAloneExhitbit(Request $request)
    {
        return responseToJson(0,'查询成功',Exhibit::selectAloneExhibitData($request->exht_id));
    }

    /**
     * 根据时间查询展览内容
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byTimeSelectExhibit(Request $request)
    {
        return responseToJson(0,'查询成功', Exhibit::selectExhibitData($request->exht_dist, $request->total, $request->page, $request->time));
    }

    /**
     * 替换展览内容
     * @return \Illuminate\Http\JsonResponse
     */
    public function replaceExhibit(Request $request)
    {
        return (Exhibit::replaceExhibitData($request->orig_select_id, $request->new_select_id)) ? responseToJson(0,'替换名言成功') : responseToJson(1,'替换名言失败');
    }



}