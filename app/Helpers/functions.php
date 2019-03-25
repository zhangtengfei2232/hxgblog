<?php
/**后台返回给前台JSON数据
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

/**处理根据文章类型搜索出来的文章ID
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
    if(($time - session($art_id)) > 10) return true;//判断用户上次访问时间和当前时间的差值是否满足访问条件
    else return false;
}

/**获得毫秒级的时间戳
 * @return float
 */
function millisecond()
{
    return ceil(microtime(true) * 1000);
}