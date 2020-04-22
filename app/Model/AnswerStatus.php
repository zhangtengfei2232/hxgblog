<?php


namespace App\Model;



use Illuminate\Support\Facades\Log;

class AnswerStatus extends BaseModel
{
    protected $table = 'answer_state';

    /**
     * 判断用户是否有权限访问相册
     * @param $album_data
     * @return array
     */
    public static function isHasJurisdiction($album_data)
    {
        $album_information = [];
        foreach ($album_data as $data){
            $data['is_has_password'] = true;
            $data['is_has_photo'] = false;
            $data['first_photo_path'] = " ";
            $is_answer = false;
            Log::info(session('user'));
            if (! empty($data['alb_question']) && ! empty(session('user'))) {  //如果相册有密保,且有用户登录
                $num = AnswerStatus::where([['alb_id', $data['alb_id']],
                    ['user_id', session('user')->user_id]])->count();
                if ($num > 0) {
                    $is_answer = true; //查看当前用户之前，是否回答过此相册的密保
                }
            }
            if (empty($data['alb_question']) || $is_answer) {    //无密保或者用户已经回答过密保
                $data['is_has_password'] = false;
                //相册照片不为空，查询第一张照片
                if ($data['photo_num'] > 0) {
                    $data['first_photo_path'] = Photo::selectAlbumFirstPhoto($data['alb_id']);
                    $data['is_has_photo'] = true;
                }
            }
            array_push($album_information, $data);
        }
        return $album_information;
    }


    /**
     * 删除关于此相册的所有回答问题状态
     * @param $album_id
     * @return bool
     */
    public static function deleteAnswerStatus($album_id)
    {
        AnswerStatus::where('alb_id', $album_id)->delete();
        return true;
    }


}
