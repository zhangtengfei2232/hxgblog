<?php

namespace App\Model;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PraiseTrample extends BaseModel
{
    const UPDATED_AT = null;
    protected $table = 'praise_trample';

    /**
     * 查询当前用户点赞/差评的状态
     * @param $art_id
     * @return mixed
     */
    public static function selectArticalPraiseTrample($art_id)
    {
        $data['praise'] = false;
        $data['trample'] = false;
        if (empty(session('user'))) return $data;
        $praise_trample_data = PraiseTrample::select('status')->where([['phone',session('user')->phone]
                                ,['arti_id',$art_id]])->first();
        if (empty($praise_trample_data)) return $data;
        switch ($praise_trample_data->status){
            case 1: $data['praise'] = true; break;
            case 2: $data['trample'] = true; break;
        }
        return $data;
    }

    /**
     * 对文章赞/踩
     * @param $status
     * @param $art_id
     * @return mixed
     */
    public static function addPraiseTrampleData($status, $art_id)
    {
        //先查看赞/踩过此文章的状态
        $status_data = self::selectArticalPraiseTrample($art_id);
        $user_phone = session('user')->phone;
        $data['is_same'] = false;
        $data['is_first'] = false;
        DB::beginTransaction();
        try{
            //如果是首次
            if (!$status_data['praise'] && !$status_data['trample']){
                PraiseTrample::insert(['phone' => $user_phone,'arti_id' => $art_id,'status' => $status]);
                $data['is_first'] = true;
                $num = Artical::updateArticalPraiseTrample($status, $art_id, $data['is_first']);
            }else{
                $praise_trample_status = self::selectPraiseTrampleStatus($art_id, $user_phone);
                if($status == $praise_trample_status){      //操作和上次一样
                    $data['is_same'] = true;
                    PraiseTrample::where([['phone', $user_phone],['arti_id', $art_id]])->delete();
                    $num = Artical::updateArticalPraiseTrample($status, $art_id, $data['is_first'], $data['is_same']);
                }else{
                    PraiseTrample::where([['phone', $user_phone],['arti_id', $art_id]])
                        ->update(['status' => $status]);
                    $num = Artical::updateArticalPraiseTrample($status, $art_id, $data['is_first'], $data['is_same']);
                }
            }
            $data['num'] = $num;
            DB::commit();
            return responseState(0,'点赞成功',$data);
        }catch (\Exception $e){
            DB::rollBack();
            return responseState(1,'点赞失败');
        }
    }

    /**
     * 查询当前用户对当前文章的赞/踩状态
     * @param $art_id
     * @param $user_phone
     * @return mixed
     */
    public static function selectPraiseTrampleStatus($art_id, $user_phone)
    {
        return PraiseTrample::select('status')->where([['arti_id', $art_id],['phone', $user_phone]])->first()->status;
    }





}