<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Model\Article;
use App\Model\Exhibit;
use App\Model\LeaveMessage;
use App\Model\Photo;
use Illuminate\Http\JsonResponse;

class MainPageController extends Controller
{
    /**
     * 首页
     * @return JsonResponse
     */
    public function showMainPage()
    {
        $data['new_article']   = dealFormatResourceURL(Article::timeResolution(Article::selectNewArticleData()), array(ARTICLE_COVER_FIELD_NAME));
        $data['browse_top']    = Article::timeResolution(Article::selectBrowseTopData());
        $data['new_photo']     = dealFormatResourceURL(Photo::selectNewPhotoData(), array(ALBUM_PHOTO_FIELD_NAME));
        $data['exhibit_data']  = explode('+', Exhibit::selectPresentExhibitData(1));
        $data['leave_message'] = dealFormatResourceURL(LeaveMessage::selectAllLeaveMessage(config('select_field.leave_message')), array(HEAD_PORTRAIT_FIELD_NAME));
        return responseToJson(0,"success", $data);
    }


}
