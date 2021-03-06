<?php


namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Model\BaseModel;
use App\Model\Exhibit;
use App\Model\LeaveMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveMessageController extends Controller
{
    /**
     * 查询留言信息
     * @param Request $request
     * @return JsonResponse
     */
    public function selectLeaveMessage(Request $request)
    {
        $data['leave_msg_data'] = dealFormatLeaveMessage(BaseModel::timeResolution(LeaveMessage::selectTopLevelMessage(config('select_field.leave_message'), $request->input('page')), false));
        $data['leave_say_data'] = Exhibit::selectPresentExhibitData(3);
        return responseToJson(0, '查询成功', $data);
    }


    /**
     * 添加留言
     * @param Request $request
     * @return JsonResponse
     */
    public function addLeaveMessage(Request $request)
    {
        $user_info = session('user');
        if (empty($user_info)) {
            return responseToJson(1, '请你重新登录');
        }
        $data['msg_content'] = $request->input('msg_content');
        $validate_content    = validateCommentContent($data['msg_content']);
        if ($validate_content['code'] == 1) {
            return responseToJson(1, $validate_content['msg']);
        }
        $data['user_id']       = session('user')->user_id;
        $data['msg_father_id'] = 0;
        $data['created_at']    = time();
        $add_replay_message = LeaveMessage::addLeaveMessage($data);
        if ($add_replay_message['code'] == 1) {
            return responseToJson(1, $add_replay_message['msg']);
        }
        $time = explode('-', date('Y-m-d', $data['created_at']));
        $data['years'] = $time[0];
        $data['monthDay'] = $time[1] . '-' . $time[2];
        unset($data['created_at']);
        $data['is_mine'] = true;
        $data['msg_id']        = $add_replay_message['data'];
        $data['child_message'] = null;
        $data['head_portrait'] = $user_info->head_portrait;
        $data['nick_name']     = $user_info->nick_name;
        $data['msg_count']     = 0;
        (session('user')->role == 1) ? $data['is_admin'] = true : $data['is_admin'] = false;
        return responseToJson(0, "回复成功", $data);
    }


    /**
     * 回复留言
     * @param Request $request
     * @return JsonResponse
     */
    public function replayMessage(Request $request)
    {
        if (empty(session('user'))) {
            return responseToJson(1, '请你重新登录');
        }
        $data['msg_content'] = $request->input('msg_content');
        $validate_content    = validateCommentContent($data['msg_content']);
        if ($validate_content['code'] == 1) {
            return responseToJson(1, $validate_content['msg']);
        }
        $data['user_id']          = session('user')->user_id;
        $data['msg_father_id']    = $request->input('father_id');
        $data['msg_top_level_id'] = $request->input('top_level_id');
        $data['created_at']       = time();
        $add_replay_message = LeaveMessage::addLeaveMessage($data, 2);
        if ($add_replay_message['code'] == 1) {
            return responseToJson(1, $add_replay_message['msg']);
        }
        $father_info = LeaveMessage::selectFatherInformation($data['msg_father_id']);
        $data['created_at'] = date('Y-m-d', $data['created_at']);
        $data['msg_id'] = $add_replay_message['data'];
        $data['father_nick_name'] = $father_info->nick_name;
        $data['father_user_id']   = $father_info->user_id;
        return responseToJson(0, "回复成功", $data);
    }


    /**
     * 删除留言
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteLeaveMessage(Request $request)
    {
        LeaveMessage::beginTransaction();
        try {
            //其中有一个留言删除失败,直接回滚
            if (! LeaveMessage::deleteData(config('select_field.leave_message'), $request->input('msg_id'))) {
                LeaveMessage::rollBack();
                return responseToJson(1, '删除失败');
            }
            LeaveMessage::commit();
            return responseToJson(0, '删除成功');
        } catch (\Exception $e) {
            LeaveMessage::rollBack();
            return responseToJson(1, '删除失败');
        }
    }



}
