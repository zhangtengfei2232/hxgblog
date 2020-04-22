<?php

use App\Http\Controllers\CommonControllers\LoginController;
use App\Model\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
/**
 * 后台返回给前台JSON数据
 * @param $code
 * @param $msg
 * @param $data
 * @return JsonResponse
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
 * @param $art_id_data
 * @return array
 */
function convertArticleId($art_id_data)
{
    $new_art_id_data = array();
    foreach ($art_id_data as  $item) {
        array_push($new_art_id_data,$item->art_id);
    }
    return $new_art_id_data;
}
//判断文章浏览量是否可以增加
function isAddArticleBrowse($art_id, $time)
{
    if (! session()->has($art_id)) {
        return true;      //当前文章，没有被访问过
    }
    if (($time - session($art_id)) > 8888) {
        return true;//判断用户上次访问时间和当前时间的差值是否满足访问条件
    }
    return false;
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
 * @param bool $is_music
 * @return mixed
 */
function uploadFile($files, $disk, $is_music = false)
{
    $file_name = $files->getClientOriginalName();
    ($is_music) ? $file_path = $file_name : $file_path = implode('_', array(uniqid(), time(), $file_name));
    $files->storeAs('./',$file_path, $disk);
    $exist_file = file_exists(storage_path() . RESOURCE_ROUTE_DIR . $disk . DIRECTORY_SEPARATOR.$file_path);
    if ($exist_file) {
        return responseState(0,'上传成功',$file_path);
    }
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
    } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
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
    //$url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;//淘宝
    //$url = 'http://whois.pconline.com.cn/ipJson.jsp?ip=' . $ip . '&json=true';
    //$url = 'https://apis.map.qq.com/ws/location/v1/ip=' . $ip . '&key=KUZBZ-PGO63-C3M3F-YU5RO-DK7JE-AEFT3';//腾讯
    $url = 'http://api.map.baidu.com/location/ip?ip=' . $ip .'&ak=u265UuYotdbAW9RIYhjPn5xoIGdz4EVw';
    try {
        $ipContent = file_get_contents($url);
        $ipContent = json_decode($ipContent, true);
        if (! empty($ipContent['content'])){
            return responseState(0,'获取成功', $ipContent['content']['address_detail']['city']);
        }
    } catch (\Exception $e) {
        return responseState(1,'获取失败');
    }
}

/**
 * 根据城市名字获取当地天气信息
 * @param $city_name
 * @return false|mixed|SimpleXMLElement|string
 */
function getWeatherInfoByCity($city_name)
{
//    $url = 'http://wthrcdn.etouch.cn/WeatherApi?city=' . urlencode($city_name);
    $url = 'http://wthrcdn.etouch.cn/weather_mini?city=' . urlencode($city_name);
    //方法一：
//    $curl = curl_init();
//    curl_setopt($curl, CURLOPT_URL, $url);
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//    $header = [];
//    $header[] = 'Content-Type:application/json;charset=utf-8';
//    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
//    $data = curl_exec($curl);
//    $prefix = dechex(ord($data[0])) . dechex(ord($data[1]));
//    $is_gzip = ('1f8b' == strtolower($prefix));
//    ($is_gzip) && $data = gzdecode($data);
//    curl_close($curl);
    //方法二：获取返回的xml数据
//    $weather_info = gzdecode(file_get_contents($url));
//    $weather_info = simplexml_load_string($weather_info);     //xml转object
//    $weather_info = json_encode($weather_info);               //object转json
//    $weather_info = json_decode($weather_info, true);         //json转array
    $weather_info = json_decode(gzdecode(file_get_contents($url)), true);
    //方法三：获取返回的JSON
    return $weather_info['data']['forecast'];
}


/**
 * @param $url
 * @param null $header
 * @return bool|string
 */
function getHttpResponseGET($url,$header = null) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    if (! empty($header)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    unset($curl);
    return $output;
}

/**
 * 远程获取数据，POST模式
 * @param string $url
 * @param array $param
 * @return bool|string
 */
function getHttpResponsePOST($url = '', $param = array()) {
    if (empty($url)) {
        return false;
    }
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, false);//是否返回响应头信息
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, true);//post提交方式
    if (! empty($param)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //是否将结果返回
    $data = curl_exec($ch);//运行curl
    if (curl_errno($ch)) {
        echo 'Errno'. json_encode(curl_error($ch));//捕抓异常
    }
    curl_close($ch);
    return $data;
}

/**
 *  RSA2加密
 * @param $data
 * @return string|null
 */
function enRSA2($data)
{
    $path = public_path() . '/key/private_key.txt';
    $private_key = file_get_contents($path);
    $str = chunk_split(trim($private_key), 64, "\n");
    $key = "-----BEGIN RSA PRIVATE KEY-----\n$str-----END RSA PRIVATE KEY-----\n";
    $signature = '';
    $signature = openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256) ? base64_encode($signature) : NULL;
    return $signature;
}

/**
 * 支付宝请求参数拼接为urlEncode格式字符串
 * @param $dataArr
 * @return string
 */
function aliPayParamToString($dataArr)
{
    ksort($dataArr);
    $signStr = '';
    foreach ($dataArr as $key => $val) {
        if (empty($signStr)) {
            $signStr = $key.'='.$val;
        } else {
            $signStr .= '&'.$key.'='.$val;
        }
    }
    return $signStr;
}

function rsaSign($str, $private_key_path)
{
    $priKey = file_get_contents($private_key_path);
    $res = openssl_get_privatekey($priKey);
    openssl_sign($str, $sign, $res);
    openssl_free_key($res);
    //base64编码
    $sign = base64_encode($sign);
    return $sign;
}

/** RSA验签
 * $data待签名数据
 * $sign需要验签的签名
 * 验签用支付宝公钥
 * return 验签是否通过 bool值
 */
function verify($data, $sign) {
    //读取支付宝公钥文件
    $path = public_path() . 'key/public_key.pem';
    $pubKey = file_get_contents($path);
    //转换为openssl格式密钥
    $res = openssl_get_publickey($pubKey);
    //调用openssl内置方法验签，返回bool值
    $result = (bool)openssl_verify($data, base64_decode($sign), $res);
    //释放资源
    openssl_free_key($res);
    //返回资源是否成功
    return $result;
}

/**
 * 处理QQ登录异常信息
 * @param $error_msg
 * @param string $msg
 */
function dealQQErrorMessage($error_msg, $msg = '')
{
    if (isset($error_msg['error'])) {
        echo "<h1>$msg</h1>";
        echo "<h3>error:</h3>" . $error_msg['error'];
        echo "<h3>msg:</h3>" . $error_msg['error_description'];
    }
}

/**
 * 处理QQ返回的数据，返回数组形式
 * @param $response
 * @return mixed
 */
function dealQQData($response)
{
    $left_pos  = strpos($response, "(");
    $right_pos = strrpos($response, ")");
    $response = json_decode(substr($response, $left_pos + 1, $right_pos - $left_pos -1), true);
    return $response;
}

/**
 * 更新登录用户认证实例
 * @param bool $is_admin
 * @param int $login_way
 * @param string $data
 * @return array array('user_id' => '1', 'phone' => '11111111111')
 */
function updateLoginAuth($is_admin = false, $login_way = Users::LOGIN_WAY_ACT_NUM_PWD, $data = '')
{
    $login_obj = new LoginController();
    //获取用户实例
    switch ($login_way) {
        case Users::LOGIN_WAY_ACT_NUM_PWD:               //账号密码登录
            $user = Users::getUserData($data);
            break;
        case Users::LOGIN_WAY_SMS:                       //短信登录
            $user = $login_obj->guard()->user();
            break;
        case Users::LOGIN_WAY_THIRD_PARTY:               //第三方登录
            $user = $data;
            break;
        default:
            $user = Users::getUserData($data);
            break;
    }
    $user->generateToken();                              //更新api_token
    Auth::login($user);                                  //改为用户实例认证
    $login_obj->loginSuccess($user, $is_admin);          //登录信息存入session
    $user = $user->toArray();
    //不是第三方登录，把密码去掉
    if ($login_way != Users::LOGIN_WAY_THIRD_PARTY) {
        unset($user['password']);
    }
    return $user;
}

/**
 * 第三方注册时候，将头像下载到本地
 * @param string $url         获取头像文件的URL
 * @param string $save_prefix 保存头像文件名的前缀名
 * @param string $img_ext     保存头像文件名的扩展名
 * @return bool   true 代表保存成功，false 代表保存失败
 */
function downloadHeadPortrait($url, $save_prefix, $img_ext)
{
    $path     = storage_path() . RESOURCE_ROUTE_DIR . HEAD_PORTRAIT_FOLDER_NAME . DIRECTORY_SEPARATOR;
    $filename = implode('_', array($save_prefix, uniqid(), time())) . $img_ext;
    $address  = $path . $filename;
    try {
        ob_start();
        readfile($url);           //输出图片文件
        $img = ob_get_contents(); //得到浏览器输出
        ob_end_clean();           //清除输出并关闭
        file_put_contents($address, $img);
        return $filename;
    } catch (\Exception $e) {
        return false;
    }
}


/**
 * 格式化获取后端资源的URL
 * @param $data
 * @param $deal_type
 */
function dealFormatResourceURL($data, $deal_type)
{
    //原数据
    foreach ($data as $key => &$value) {
        //要转换的字段
        foreach ($deal_type as $index => $item) {
            switch ($item) {
                case ARTICLE_COVER_FIELD_NAME:                          //文章封面
                    $value[$item] = ARTICLE_COVER_URL . $value[$item];
                    break;
                case MUSIC_LYRIC_FIELD_NAME:                            //音乐
                    $value['exh_name'] = MUSIC_URL . $value[$item];
                    break;
                case MUSIC_FIELD_NAME:                                  //音乐歌词
                    $value['exh_content'] = MUSIC_LYRIC_URL . $value[$item];
                    break;
                case HEAD_PORTRAIT_FIELD_NAME:                          //头像
                    $value[$item] = HEAD_PORTRAIT_URL . $value[$item];
                    break;
                case ALBUM_PHOTO_FIELD_NAME:                            //相册图片
                    $value[$item] = ALBUM_PHOTO_URL . $value[$item];
                    break;
            }
        }
    }

}

