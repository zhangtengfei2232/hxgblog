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
        $exhibit_dist = $request->input('exh_dist');
        if ($exhibit_dist == 4) {                       //判断是否为音乐上传
            $music_file = $request->file('exh_music');
            $music_lyric = $request->file('exh_lyric');
            $is_has_music = true;
            $validate_music = judgeReceiveFiles($music_file, 2);
            if ($validate_music['code'] == 1) {
                return responseToJson(1, $validate_music['msg']);
            }
            $validate_lyric = judgeReceiveFiles($music_lyric, 3);
            if ($validate_lyric['code'] == 1) {
                return responseToJson(1, $validate_lyric['msg']);
            }
            $upload_music = uploadFile($music_file, MUSIC_FOLDER_NAME, true);
            if ($upload_music['code'] == 1) {
                return responseToJson(1, '添加音乐文件失败');
            }
            $upload_lyric = uploadFile($music_lyric, MUSIC_LYRIC_FOLDER_NAME, true);
            if ($upload_lyric['code'] == 1) {
                deleteFile($upload_music['data'], MUSIC_FOLDER_NAME);  //歌词文件上传失败，上传成功的音乐，删除
                return responseToJson(1, '添加歌词文件失败');
            }
            $data['exh_name']    = $upload_music['data'];
            $data['exh_content'] = $upload_lyric['data'];
        } else {
            $data['exh_content'] = $request->input('exh_content');
            $data['exh_name']    = $request->input('exh_name');
        }
        $validate_data = validateExhibit($data);
        if ($validate_data['code'] == 1) {
            return responseToJson(1, $validate_data['msg']);
        }
        $data['exh_distinguish'] = $exhibit_dist;
        $data['created_at']       = time();
        ($is_has_music) ? $msg = '上传音乐失败' : $msg = '添加名言失败';
        Exhibit::beginTransaction();
        try {
            if (Exhibit::addExhibitData($data)) {
                Exhibit::commit();
                ($is_has_music) ? $msg = '上传音乐成功' : $msg = '添加名言成功';
                return responseToJson(0, $msg);
            }
        } catch (\Exception $e) {
            if ($is_has_music) {
                deleteFile($data['exh_name'], MUSIC_FOLDER_NAME);  //数据库更新失败，上传成功的音乐和歌词，删除
                deleteFile($data['exh_content'], MUSIC_LYRIC_FOLDER_NAME);
            }
            Exhibit::rollBack();
            return responseToJson(1, $msg);
        }
        return responseToJson(1, $msg);
    }


    /**
     * 删除展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteExhibit(Request $request)
    {
        $exh_id_data = $request->input('exh_id_data');
        if ($request->input('exh_dist') == 4) {
            $road_data = Exhibit::selectMusicRoad($exh_id_data);
            $music_road = $road_data[0];
            $lyric_road = $road_data[1];
            Exhibit::beginTransaction();
            try {
                $delete_music_data = Exhibit::deleteExhibitData($exh_id_data);
                if ($delete_music_data) {
                    Exhibit::commit();
                }
            } catch (\Exception $e) {
                Exhibit::rollBack();
                return responseToJson(1,'删除展览内容失败');
            }
            deleteMultipleFile($music_road, config('upload.music'));
            deleteMultipleFile($lyric_road, config('upload.music_lyric'));
            return responseToJson(0, '删除展览内容成功');
        }
        return (Exhibit::deleteExhibitData($exh_id_data)) ? responseToJson(0, '删除展览内容成功') : responseToJson(1, '删除展览内容失败');
    }


    /**
     * 修改展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function updateExhibit(Request $request)
    {
        $data['exh_id'] = $request->input('exh_id');
        $data['exh_content'] = $request->input('exh_content');
        $data['exh_name'] = $request->input('exh_name');
        $validate_data = validateExhibit($data);
        if ($validate_data['code'] == 1) {
            return responseToJson(1, $validate_data['msg']);
        }
        return (Exhibit::updateExhibitData($data)) ? responseToJson(0, '修改名言成功') : responseToJson(1, '修改名言成功');
    }


    /**
     * 根据展览类别查询内容
     * @param Request $request
     * @return JsonResponse
     */
    public function selectExhibit(Request $request)
    {
        return responseToJson(0, '查询成功', Exhibit::selectExhibitData($request->input('exh_dist'), $request->input('total'), $request->input('page')));
    }


    /**
     * 查询单个展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function selectAloneExhibit(Request $request)
    {
        return responseToJson(0, '查询成功', Exhibit::selectAloneExhibitData($request->input('exh_id')));
    }


    /**
     * 根据时间查询展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function byTimeSelectExhibit(Request $request)
    {
        return responseToJson(0, '查询成功', Exhibit::selectExhibitData($request->input('exh_dist'), $request->input('total'), $request->input('page'), $request->input('time')));
    }


    /**
     * 替换展览内容
     * @param Request $request
     * @return JsonResponse
     */
    public function replaceExhibit(Request $request)
    {
        return (Exhibit::replaceExhibitData($request->input('orig_select_id'), $request->input('new_select_id'))) ? responseToJson(0, '替换名言成功') : responseToJson(1, '替换名言失败');
    }



}
