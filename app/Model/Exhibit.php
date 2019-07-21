<?php

namespace App\Model;


class Exhibit extends BaseModel
{
    protected $table = 'exhibit';

    public static function addExhibitData($data)
    {
        return Exhibit::insert($data);
    }
    /**
     * 查询展览内容
     * @param $exhit_dist
     * @param $total
     * @param $page
     * @param array $time
     * @return mixed
     */
    public static function selectExhibitData($exhit_dist, $total, $page, $time = [])
    {
        $exhibit  = Exhibit::where([['exht_status','<>',1],['exht_distinguish', $exhit_dist]]);
        if(!empty($time)){
            $exhibit = $exhibit->whereBetween('created_at',$time);
        }
        $exhibit = $exhibit->paginate($total);
        $data['total'] = $exhibit->total();
        $exhibit = ((array)json_decode(json_encode($exhibit)))['data'];
        if(empty($time) && $page == 1){
            $selected = json_decode(json_encode(Exhibit::where([['exht_distinguish', $exhit_dist],['exht_status',1]])->first()));
            array_unshift($exhibit,$selected);
        }
        foreach ($exhibit as $key => $item){
            if(strlen($exhibit[$key]->exht_content) > 2){
                $exhibit[$key]->exht_content = mb_substr($exhibit[$key]->exht_content,0,2) . "......";
            }
        }
        $data['exhibit_data'] = $exhibit;
        return $data;
    }

    /**
     * 查询单个展览内容
     * @param $exht_id
     * @return mixed
     */
    public static function selectAloneExhibitData($exht_id)
    {
        return Exhibit::select('exht_content')->where('exht_id', $exht_id)->first();
    }

    /**
     * 更新展览内容
     * @param $data
     * @return bool
     */
    public static function updateExhibitData($data)
    {
        return Exhibit::where('exht_id', $data['exht_id'])->update($data) > 0;

    }

    /**
     * 替换展览内容
     * @param $orig_select_id
     * @param $new_select_id
     * @return bool
     */
    public static function replaceExhibitData($orig_select_id, $new_select_id)
    {
      return Exhibit::where('exht_id', $orig_select_id)->update(['exht_status' => 0]) > 0 && Exhibit::where('exht_id', $new_select_id)->update(['exht_status' => 1]) > 0;
    }

}