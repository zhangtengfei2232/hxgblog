<?php
use Illuminate\Support\Facades\Storage;
/**
 * 后台返回给前台JSON数据
 * @param $code
 * @param $msg
 * @param $data
 * @return \Illuminate\Http\JsonResponse
 */
function responseToJson($code, $msg, $data = [])
{
    $result['code'] = $code;
    $result['msg']  = $msg;
    if(!empty($data)) $result['data'] = $data;
    return response()->json($result);
}

/**
 * 后台验证数据，返回状态消息
 * @param $code
 * @param $msg
 * @param array $data
 * @return mixed
 */
function responseState($code, $msg, $data = []){
    $result['code'] = $code;
    $result['msg']  = $msg;
    if(!empty($data)) $result['data'] = $data;
    return $result;
}
/**
 * 处理根据文章类型搜索出来的文章ID
 * @param $art_id_datas
 * @return array
 */
function convertArticaId($art_id_datas)
{
    $new_art_id_datas = array();
    foreach ($art_id_datas as  $item) {
        array_push($new_art_id_datas,$item->arti_id);
    }
    return $new_art_id_datas;
}
//判断文章浏览量是否可以增加
function isAddArticalBrowse($art_id, $time)
{
    if(!session()->has($art_id))  return true;      //当前文章，没有被访问过
    if(($time - session($art_id)) > 8888) return true;//判断用户上次访问时间和当前时间的差值是否满足访问条件
    else return false;
}

/**
 * 获得毫秒级的时间戳
 * @return float
 */
function millisecond()
{
    return ceil(microtime(true) * 1000);
}

/**
 * 给定时间与时间间隔，计算当前时间与给定时间的时间间隔差是否大于给定的时间间隔
 * @param $time :给定的时间 毫秒级
 * @param int $interval  :给定的时间间隔（分钟） 默认十分钟
 * @return bool  大于：true; 小于：false
 */
function isTimeGreater($time, $interval = 10)
{
    $int = millisecond() - $time;
    $interval = $interval * 60 * 1000;
    return $int > $interval ? true : false;
}

/**
 * 上传文件
 * @param $files
 * @param $disk
 * @param bool $is_music_file
 * @return mixed
 */
function uploadFile($files, $disk, $is_music = false)
{
    $file_name = $files->getClientOriginalName();
    ($is_music) ? $file_path = $file_name : $file_path = uniqid().time() . '-' . $file_name;
    $files->storeAs('./',$file_path, $disk);
    $exist_file = file_exists(storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$disk.DIRECTORY_SEPARATOR.$file_path);
    if($exist_file) return responseState(0,'上传成功',$file_path);
    return responseState(1,'上传失败');
}

/**
 * 删除文件
 * @param $fileRoad
 * @param $disk
 * @return bool
 */
function deleteFile($fileRoad, $disk)
{
    Storage::disk($disk)->delete($fileRoad);
    return !file_exists(storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$disk.DIRECTORY_SEPARATOR.$fileRoad);
}

/**
 * 删除多个文件
 * @param $file_road_data
 * @param $disk
 */
function deleteMultipleFile($file_road_data, $disk)
{
    foreach ($file_road_data as $file_road){
        deleteFile($file_road, $disk);
    }
}

/**
 *
 * @return array|false|string
 */
function getUserIp()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
        $ip = getenv("HTTP_CLIENT_IP");
    }else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    }else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
        $ip = getenv("REMOTE_ADDR");
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
        $ip = $_SERVER['REMOTE_ADDR'];
    } else
    $ip = "unknown";
    return $ip;
}

/**
 * 获取用户地理位置
 * @param $ip
 * @return bool|mixed|string
 */
function getUserPosition($ip)
{
    $url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
    try{
        $ipContent = file_get_contents($url);
        $ipContent = json_decode($ipContent,true);
        return responseState(0,'获取成功', $ipContent);
    }catch (\Exception $e){
        return responseState(1,'获取失败');
    }


}

