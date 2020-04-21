<?php

namespace App\Http\Controllers\BackControllers;

use App\Http\Controllers\Controller;
use App\Model\Album;
use App\Model\AnswerStatus;
use App\Model\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaAlbumController extends Controller
{
    /**
     * 添加相册
     * @param Request $request
     * @return JsonResponse
     */
    public function addAlbum(Request $request)
    {
        $data['alb_name']      = $request->input('alb_name');
        $data['alb_introduce'] = $request->input('alb_introduce');
        $validate_data = validateAlbumData($data);
        if ($validate_data['code'] == 1) {
            return responseToJson(1, $validate_data['msg']);
        }
        return (Album::addAlbumData($data)) ? responseToJson(0, '添加相册成功') : responseToJson(1, '添加相册失败');
    }


    /**
     * 删除相册
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAlbum(Request $request)
    {
        $alb_id = $request->input('alb_id');
        $album_road_data = Photo::selectAlbumImgRoad($alb_id);
        Album::beginTransaction();
        try{
            $del_album = Album::deleteAlbumData($alb_id);
            $del_ans = AnswerStatus::deleteAnswerStatus($alb_id);
            $del_photo = Photo::deleteAlbumImgData($alb_id);
            if ($del_album && $del_ans && $del_photo) {
                Album::commit();
                deleteMultipleFile($album_road_data, ALBUM_FOLDER_NAME);
                return responseToJson(0, '删除相册成功');
            }
        }catch (\Exception $e){
            Album::rollBack();
            return responseToJson(1, '删除相册失败');
        }
        return responseToJson(1, '删除相册失败');
    }


    /**
     * 修改相册信息
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAlbumInfo(Request $request)
    {
        $data['alb_name']      = $request->input('alb_name');
        $data['alb_introduce'] = $request->input('alb_introduce');
        $data['alb_id']        = $request->input('alb_id');
        $validate_data = validateAlbumData($data);
        if ($validate_data['code'] == 1) {
            return responseToJson(1, $validate_data['msg']);
        }
        return (Album::updateAlbumData($data)) ? responseToJson(0, '添加相册成功') : responseToJson(1, '添加相册失败');
    }


    /**
     * 添加相册密保
     * @param Request $request
     * @return JsonResponse
     */
    public function addAlbumSecretSecurity(Request $request)
    {
        $data['alb_id']       = $request->input('alb_id');
        $data['alb_question'] = $request->input('alb_question');
        $data['alb_answer']   = $request->input('alb_answer');
        $validate_data = validateAlbumSecSty($data);
        if ($validate_data['code'] == 1) {
            return responseToJson(1, $validate_data['msg']);
        }
        return (Album::updateAlbumData($data)) ? responseToJson(0, '添加密保成功') : responseToJson(1, '添加密保失败');
    }


    /**
     * 删除相册密保
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAlbumSecretSecurity(Request $request)
    {
        $data['alb_id']       = $request->input('alb_id');
        $data['alb_question'] = "";
        $data['alb_answer']   = "";
        Album::beginTransaction();
        try {
            $del_sec_sty    =  Album::updateAlbumData($data);
            $del_answer_status =  AnswerStatus::deleteAnswerStatus($data['alb_id']);
            if ($del_sec_sty && $del_answer_status) {
                Album::commit();
                return responseToJson(0, '删除相册密保成功');
            }
        } catch (\Exception $e) {
            Album::rollBack();
            return responseToJson(1, '删除相册密保失败');
        }
        return responseToJson(1, '修改相册密保失败');
    }


    /**
     * 修改相册密保
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAlbumSecretSecurity(Request $request)
    {
        $data['alb_id']       = $request->input('alb_id');
        $data['alb_question'] = $request->input('alb_question');
        $data['alb_answer']   = $request->input('alb_answer');
        Album::beginTransaction();
        try {
            $update_sec_sty = Album::updateAlbumData($data);
            $del_answer_status = AnswerStatus::deleteAnswerStatus($data['alb_id']);
            if ($update_sec_sty && $del_answer_status) {
                Album::commit();
                return responseToJson(0, '修改相册密保成功');
            }
        } catch (\Exception $e) {
            Album::rollBack();
            return responseToJson(1, '修改相册密保失败');
        }
        return responseToJson(1, '修改相册密保失败');
    }


    /**
     * 查询相册信息
     * @return JsonResponse
     */
    public function getAlbumInfo()
    {
        return responseToJson(0, '查询成功', Photo::selectMulAlumFirstPhoto(Album::selectAllAlbumData()));
    }


    /**
     * 查询相册照片
     * @param Request $request
     * @return JsonResponse
     */
    public function selectAlbumPhoto(Request $request)
    {
        return responseToJson(0,'查询成功', Photo::byAlbumIdSelectPhotoData($request->alb_id, $request->page));
    }


    /**
     * 添加相册图片
     * @param Request $request
     * @return JsonResponse
     */
    public function addAlbumImage(Request $request)
    {
        $album_photo    = $request->file();
        $validate_photo = judgeMultipleFile($album_photo);
        if ($validate_photo['code'] == 1) {
            return responseToJson(1, $validate_photo['msg']);
        }
        Album::beginTransaction();
        try {
            $photo_road_data = [];
            foreach ($album_photo as $key => $photo){
                $photo_road_data[$key] = uploadFile($photo, ALBUM_FOLDER_NAME)['data'];
            }
            $album_id = $request->input('album_id');
            if (Photo::addAlbumPhotoData($photo_road_data, $album_id) &&
                Album::updateAlbumPhotoNum($album_id, count($photo_road_data))) {
                Album::commit();
                return responseToJson(0, '添加相册图片成功');
            }
        } catch (\Exception $e) {
            deleteMultipleFile($photo_road_data, ALBUM_FOLDER_NAME);//数据库异常，删除上传成功的文件，回滚数据
            Album::rollBack();
            return responseToJson(1, '添加相册图片失败');
        }
        return responseToJson(1, '添加相册图片失败');
    }


    /**
     * 删除相册照片
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAlbumPhoto(Request $request)
    {
        $album_id            = $request->input('album_id');
        $del_photo_road_data = $request->input('del_photo_road');
        $del_photo_id_data   = $request->input('del_photo_id');
        Album::beginTransaction();
        try {
            $update_photo_num = Album::updateAlbumPhotoNum($album_id, count($del_photo_road_data), true);
            $delete_photo     = Photo::deleteMultiplePhotoData($del_photo_id_data);
            if ($update_photo_num && $delete_photo) {
                Album::commit();
                deleteMultipleFile($del_photo_road_data, config('upload.image'));
                return responseToJson(0, '删除照片成功');
            }
        } catch (\Exception $e) {
            Album::rollBack();
            return responseToJson(1, '删除照片失败');

        }
        return responseToJson(0, '删除照片失败');
    }
}
