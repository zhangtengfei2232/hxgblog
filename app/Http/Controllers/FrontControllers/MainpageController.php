<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Model\Article;
use App\Model\Exhibit;
use App\Model\LeaveMessage;
use App\Model\Photo;

class MainpageController extends Controller
{
    //首页
    public function showMainPage()
    {
        $data['new_article']   = Article::timeResolution(Article::selectNewArticleData());
        $data['browse_top']    = Article::timeResolution(Article::selectBrowseTopData());
        $data['new_photo']     = Photo::selectNewPhotoData();
        $data['exhibit_data']  = explode('+', Exhibit::selectPresentExhibitData(1));
        $data['leave_message'] = LeaveMessage::selectAllLeaveMessage(config('selectfield.leave_message'));
        $data['leave_message'] = dealFormatResourceURL($data['leave_message'], array(HEAD_PORTRAIT_FOLDER_NAME, ARTICLE_COVER_FOLDER_NAME));
        return responseToJson(0,"success", $data);
    }


}
