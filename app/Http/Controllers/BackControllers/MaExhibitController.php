<?php
namespace App\Http\Controllers\BackControllers;

use App\Http\Controllers\Controller;
use App\Model\Exhibit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaExhibitController extends Controller
{
    /**
     * 添加展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function addExhibit(Request $request)
    {
        if (! $request->isMethod('POST')) {
            return responseToJson(1,'你请求的方式不对');
        }
        $is_has_music = false;
        $exhibit_dist = $request->exht_dist;
        if ($exhibit_dist == 4) {                       //判断是否为音乐上传
            $music_file = $request->file('exht_music');
            $music_lyric = $request->file('exht_lyric');
            $is_has_music = true;
            $music_disk = config('upload.music');
            $lyric_disk = config('upload.music_lyric');
            $validate_music = judgeReceiveFiles($music_file, 2);
            if ($validate_music['code'] == 1) {
                return responseToJson(1,$validate_music['msg']);
            }
            $validate_lyric = judgeReceiveFiles($music_lyric, 3);
            if ($validate_lyric['code'] == 1) {
                return responseToJson(1,$validate_lyric['msg']);
            }
            $upload_music = uploadFile($music_file, $music_disk, true);
            if ($upload_music['code'] == 1) {
                return responseToJson(1,'添加音乐文件失败');
            }
            $upload_lyric = uploadFile($music_lyric,$lyric_disk, true);
            if ($upload_lyric['code'] == 1) {
                deleteFile($upload_music['data'],$music_disk);  //歌词文件上传失败，上传成功的音乐，删除
                return responseToJson(1,'添加歌词文件失败');
            }
            $data['exht_name']    = $upload_music['data'];
            $data['exht_content'] = $upload_lyric['data'];
        } else {
            $data['exht_content'] = $request->exht_content;
            $data['exht_name']    = $request->exht_name;
        }
        $validate_data = validateExhibit($data);
        if ($validate_data['code'] == 1) {
            return responseToJson(1,$validate_data['msg']);
        }
        $data['exht_distinguish'] = $exhibit_dist;
        $data['created_at']       = time();
        ($is_has_music) ? $msg = "上传音乐失败" : $msg = "添加名言失败";
        Exhibit::beginTransaction();
        try{
            if (Exhibit::addExhibitData($data)) {
                Exhibit::commit();
                ($is_has_music) ? $msg = "上传音乐成功" : $msg = "添加名言成功";
                return responseToJson(0,$msg);
            }
        } catch (\Exception $e) {
            if ($is_has_music) {
                deleteFile($data['exht_name'],$music_disk);  //数据库更新失败，上传成功的音乐和歌词，删除
                deleteFile($data['exht_content'],$music_lyric);
            }
            Exhibit::rollBack();
            return responseToJson(1,$msg);
        }
        return responseToJson(1,$msg);
    }


    /**
     * 删除展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteExhibit(Request $request)
    {
        $exht_id_data = $request->exht_id_data;
        if ($request->exht_dist == 4) {
            $road_data = Exhibit::selectMusicRoad($exht_id_data);
            $music_road = $road_data[0];
            $lyric_road = $road_data[1];
            Exhibit::beginTransaction();
            try {
                $delete_music_data = Exhibit::deleteExhibitData($exht_id_data);
                if ($delete_music_data) {
                    Exhibit::commit();
                }
            } catch (\Exception $e) {
                Exhibit::rollBack();
                return responseToJson(1,'删除展览内容失败');
            }
            deleteMultipleFile($music_road, config('upload.music'));
            deleteMultipleFile($lyric_road, config('upload.music_lyric'));
            return responseToJson(0,'删除展览内容成功');
        }
        return (Exhibit::deleteExhibitData($exht_id_data)) ? responseToJson(0,'删除展览内容成功') : responseToJson(1,'删除展览内容失败');
    }

    /**
     * 修改展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function updateExhibit(Request $request)
    {
        $data['exht_id'] = $request->exht_id;
        $data['exht_content'] = $request->exht_content;
        $data['exht_name'] = $request->exht_name;
        $validate_data = validateExhibit($data);
        if ($validate_data['code'] == 1) {
            return responseToJson(1,$validate_data['msg']);
        }
        return (Exhibit::updateExhibitData($data)) ? responseToJson(0,'修改名言成功') : responseToJson(1,'修改名言成功');
    }

    /**
     * 根据展览类别查询内容
     * @param Request $request
     * @return JsonResponse
     */
    public function selectExhibit(Request $request)
    {
        return responseToJson(0,'查询成功',Exhibit::selectExhibitData($request->exht_dist, $request->total, $request->page));
    }

    /**
     * 查询单个展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function selectAloneExhibit(Request $request)
    {
        return responseToJson(0,'查询成功',Exhibit::selectAloneExhibitData($request->exht_id));
    }

    /**
     * 根据时间查询展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function byTimeSelectExhibit(Request $request)
    {
        return responseToJson(0,'查询成功', Exhibit::selectExhibitData($request->exht_dist, $request->total, $request->page, $request->time));
    }

    /**
     * 替换展览内容
     * @return JsonResponse
     */
    public function replaceExhibit(Request $request)
    {
        return (Exhibit::replaceExhibitData($request->orig_select_id, $request->new_select_id)) ? responseToJson(0,'替换名言成功') : responseToJson(1,'替换名言失败');
    }



}
