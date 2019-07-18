<?php

namespace App\Http\Controllers\BackControllers;

use App\Http\Controllers\Controller;
use App\Model\Album;
use App\Model\AnswerStatus;
use App\Model\Photo;
use Illuminate\Http\Request;

class MaAlbumController extends Controller
{

    /**
     * 添加相册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAlbum(Request $request)
    {
        $data['albu_name'] = $request->albu_name;
        $data['albu_introduce'] = $request->albu_introduce;
        $validate_data = validateAlbumData($data);
        if($validate_data['code'] == 1) return responseToJson(1,$validate_data['msg']);
        return (Album::addAlbumData($data)) ? responseToJson(0,'添加相册成功') : responseToJson(1,'添加相册失败');
    }

    /**
     * 删除相册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAlbum(Request $request)
    {
        $albu_id = $request->albu_id;
        $album_road_data = Photo::selectAlbumImgRoad($albu_id);
        Album::beginTransaction();
        try{
            $del_album = Album::deleteAlbumData($albu_id);
            $del_anser = AnswerStatus::deleteAnswerStatus($albu_id);
            $del_photo = Photo::deleteAlbumImgData($albu_id);
            if($del_album && $del_anser && $del_photo){
                Album::commit();
                deleteMultipleFile($album_road_data, config('upload.image'));
                return responseToJson(0,'删除相册成功');
            }
        }catch (\Exception $e){
            Album::rollBack();
            return responseToJson(1,'删除相册失败');
        }
    }

    /**
     * 修改相册信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAlbumInfor(Request $request)
    {
        $data['albu_name'] = $request->albu_name;
        $data['albu_introduce'] = $request->albu_introduce;
        $data['albu_id']        = $request->albu_id;
        $validate_data = validateAlbumData($data);
        if($validate_data['code'] == 1) return responseToJson(1,$validate_data['msg']);
        return (Album::updateAlbumData($data)) ? responseToJson(0,'添加相册成功') : responseToJson(1,'添加相册失败');
    }

    /**
     * 添加相册密保
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAlbumSecretSecurity(Request $request)
    {
        $data['albu_id'] = $request->albu_id;
        $data['albu_question'] = $request->albu_question;
        $data['albu_answer'] = $request->albu_answer;
        $validate_data = validateAlbumSecSty($data);
        if($validate_data['code'] == 1) return responseToJson(1,$validate_data['msg']);
        return (Album::updateAlbumSecStyData($data)) ? responseToJson(0,'添加密保成功') : responseToJson(1,'添加密保失败');
    }
    public function deleteAlbumSecretSecurity(Request $request)
    {
        $album_id = $request->albu_id;




    }
    public function updateAlbumSecretSecurity(Request $request)
    {
        $data['albu_id'] = $request->albu_id;
        $data['albu_question'] = $request->albu_question;
        $data['albu_answer'] = $request->albu_answer;


    }

    /**
     * 查询相册信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlbumInfor()
    {
        return responseToJson(0,'查询成功',Photo::selectMulAlumFirstPhoto(Album::selectAllAlbumData()));
    }




}