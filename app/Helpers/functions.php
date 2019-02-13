<?php

function responseToJson($code, $msg, $data){
    $result = array("code" => $code, "msg" => $msg, "data" => $data);
    return response()->json($result);
}