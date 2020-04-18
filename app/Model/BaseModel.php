<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    protected $casts = [
        'created_at'   => 'date:Y-m-d',
        'updated_at'   => 'datetime:Y-m-d',
    ];
    protected static $information_data = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'updated_token_at',
    ];

    /**
     * @param mixed $value
     * @return false|int|null|string
     */
    public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }
    //文章时间拆分函数
    public static function timeResolution($data, $is_art = true)
    {
        if (! is_array($data)) {
            $data = json_decode(json_encode($data));
        }
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = (array)$data[$i];
            $time = explode('-',$data[$i]['created_at']);
            if ($is_art) {
                if (strlen(strip_tags($data[$i]['arti_content'])) > 450) {
                    $filter_char = array(' ', '\n', '*');
                    $data[$i]['arti_content'] = mb_substr(str_replace($filter_char, '', strip_tags($data[$i]['arti_content'])),0, 150).".......";
                }
                if (strlen($data[$i]['arti_title']) > 30) {
                    $data[$i]['arti_title'] = mb_substr($data[$i]['arti_title'], 0, 20).".......";
                }
            }
            $data[$i]['years']    = $time[0];
            $data[$i]['monthDay'] = $time[1] . '-' . $time[2];
            unset($data[$i]['created_at']);
        }
        return $data;
    }

    /**
     * 查 '某一篇文章的所有顶级评论' / '留言'
     * @param $art_id
     * @return mixed
     */
    public static function selectTopLevelMessage($config_param, $page, $art_id = '')
    {
        ($config_param['table_name'] == "leave_message") ? $is_msg = true : $is_msg = false;
        $data = $config_param['model_name']::select($config_param['select_field'])
            ->leftJoin('users', $config_param['table_name'].'.user_id', '=', 'users.user_id')
            ->where($config_param['father_id_field'], 0);
        //文章评论查询
        ($config_param['table_name'] == 'comment') ? $data = $data->where('arti_id', $art_id)->get()->toArray()
        :$data = $data->offset($page * 2)->orderBy('created_at', 'desc')->limit(2)->get()->toArray();
        (!empty(session()->has('user'))) ? $is_login = true : $is_login = false;
        ($is_login) ? $user_id = session('user')->user_id : $user_id = " ";
        ($is_login && session('user')->role == 1) ? $is_admin = true : $is_admin = false;
        foreach ($data as $key => $value) {
            ($data[$key]['user_id'] == $user_id || $is_admin) ? $data[$key]['is_mine'] = true : $data[$key]['is_mine'] = false;
            $child_comment = self::selectALLChildMessageData($config_param,$data[$key][$config_param['id_field']], $user_id, $data[$key]['is_mine'], $is_msg);
            if ( empty($child_comment)) {
                $child_comment = [];
            }
            $data[$key][$config_param['count']] = count($child_comment);
            $data[$key][$config_param['child_field']] = $child_comment;
            if ($is_msg) {
                $data[$key]['is_admin'] = $is_admin;
            }
            self::$information_data = [];    //因为是static，所以每次查询都要清空子评论内容
        }
        return $data;
    }

    /**
     * 根据 '文章评论/留言' ===>顶级ID，查询所有子评论
     * @param $father_id
     * @return array|void
     */
    public static function selectALLChildMessageData($config_param ,$father_id, $user_id, $is_mine, $is_msg)
    {
        $comment = $config_param['model_name']::select($config_param['select_field'])->orderBy('created_at', 'asc')
            ->leftJoin('users', $config_param['table_name'].'.user_id', '=', 'users.user_id')
            ->where($config_param['father_id_field'], $father_id)->get()->toArray();
        if (empty($comment)) {
            return ;
        }
        foreach ($comment as $key => $data){
            $father_info = $config_param['model_name']::select('nick_name','users.user_id')
                ->leftJoin('users', $config_param['table_name'].'.user_id', '=', 'users.user_id')
                ->where($config_param['id_field'],$comment[$key][$config_param['father_id_field']])
                ->first();
            if (empty($father_info)) {
                continue;
            }
            $comment[$key]['father_nick_name'] = $father_info->nick_name;
            $comment[$key]['father_id'] = $father_info->user_id;
            if ($is_msg) {    //是留言板
                ($is_mine) ? $comment[$key]['is_mine'] = true : $comment[$key]['is_mine'] = false;
            } else {
                ($comment[$key]['user_id'] == $user_id) ? $comment[$key]['is_mine'] = true : $comment[$key]['is_mine'] = false;
            }
        }
        foreach ($comment as $value) {
            array_push(self::$information_data, $value);                  //把查询的数据放到数组中
            self::selectALLChildMessageData($config_param, $value[$config_param['id_field']], $user_id, $is_mine, $is_msg); //递归子查询
        }
        return self::$information_data;
    }

    /**
     * 删除文章评论
     * @param $come_id
     * @return mixed
     */
    public static function deleteData($config_param, $del_id)
    {
        $id_field = $config_param['id_field'];
        $model_name = $config_param['model_name'];
        $children_come_id_data = $model_name::select($id_field)
            ->where($config_param['father_id_field'],$del_id)->get();
        //当前评论没有子评论
        if ($children_come_id_data->isEmpty()) {
            return $model_name::where($id_field,$del_id)->delete();
        }
        foreach ($children_come_id_data as $id_data) $is_delete = self::deleteData($config_param, $id_data->$id_field);
        if (! $is_delete) {
            return $is_delete;          //如果其中有一个删除失败，直接返回false
        }
        return $model_name::where($id_field, $del_id)->delete();
    }

    /**
     * 删除关于文章的所有数据
     * @param $del_config
     * @param $art_id_data
     * @return bool
     */
    public static function deleteArticleRelevantData($del_config, $art_id_data)
    {
        foreach ($del_config as $config) $config::whereIn('arti_id', $art_id_data)->delete();
        return true;
    }

    //开启事务
    public static function beginTransaction()
    {
        return DB::beginTransaction();
    }
    //没有异常，提交事务
    public static function commit()
    {
        return DB::commit();
    }
    //回滚
    public static function rollBack()
    {
        return DB::rollBack();
    }

}
