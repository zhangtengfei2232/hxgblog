<?php


namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Model\Album;
use App\Model\AnswerStatus;
use App\Model\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * 显示所有相册
     * @return JsonResponse
     */
    public function selectAllAlbumInformation()
    {
        return responseToJson(0, '查找成功', dealFormatResourceURL(AnswerStatus::isHasJurisdiction(Album::selectAllAlbumData()), array(ALBUM_FIRST_PHOTO_FIELD_NAME)));
    }


    /**
     * 判断用户输入的相册问题答案是否正确
     * @param Request $request
     * @return JsonResponse
     */
    public function judgeQuestionAnswer(Request $request)
    {
        $is_correct = Album::judgeQuestionAnswerData($request->input('album_id'), $request->input('answer'));
        return ($is_correct) ? responseToJson(0, '回答正确') : responseToJson(1, '回答错误');
    }


    /**
     * 根据相册ID，查询照片
     * @param Request $request
     * @return JsonResponse
     */
    public function byAlbumIdSelectPhoto(Request $request)
    {
        return responseToJson(0, '查询成功', dealFormatResourceURL(Photo::byAlbumIdSelectPhotoData($request->input('album_id'), $request->input('page')), array(ALBUM_PHOTO_FIELD_NAME)));
    }

}
