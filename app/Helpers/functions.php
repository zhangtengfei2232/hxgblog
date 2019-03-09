<?php
/**后台返回给前台JSON数据
 * @param $code
 * @param $msg
 * @param $data
 * @return \Illuminate\Http\JsonResponse
 */
function responseToJson($code, $msg, $data) {
    $result = array("code" => $code, "msg" => $msg, "data" => $data);
    return response()->json($result);
}

//处理根据文章类型搜索出来的文章ID
function convertArticaId($art_id_datas) {
    $new_art_id_datas = array();
    foreach ($art_id_datas as  $item) {
        array_push($new_art_id_datas,$item->arti_id);
    }
    return $new_art_id_datas;
}