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
        if (! empty($time)) {
            $exhibit = $exhibit->whereBetween('created_at',$time);
        }
        $exhibit = $exhibit->paginate($total);
        $data['total'] = $exhibit->total();
        $exhibit = ((array)json_decode(json_encode($exhibit)))['data'];
        if (empty($time) && $page == 1) {
            $selected = json_decode(json_encode(Exhibit::where([['exht_distinguish', $exhit_dist],['exht_status',1]])->first()));
            array_unshift($exhibit,$selected);
        }
        foreach ($exhibit as $key => $item){
            if (strlen($exhibit[$key]->exht_content) > 2) {
                $exhibit[$key]->exht_content = mb_substr($exhibit[$key]->exht_content, 0, 2) . "......";
            }
        }
        $data['exhibit_data'] = $exhibit;
        return $data;
    }

    /**
     * 批量删除展览内容
     * @param $exht_id_data
     * @return bool
     */
    public static function deleteExhibitData($exht_id_data)
    {
        return Exhibit::whereIn('exht_id',$exht_id_data)->delete() == count($exht_id_data);
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

    /**
     * 查询当前展示的名言/音乐
     * @param $dist
     * @return mixed
     */
    public static function selectPresentExhibitData($dist)
    {
        return Exhibit::select('exht_content')->where([['exht_distinguish',$dist], ['exht_status', 1]])->first()->exht_content;
    }

    /**
     * 查询当前的音乐文件名
     * @param $dist
     * @return mixed
     */
    public static function selectPresentMusicFile($dist)
    {
        return Exhibit::select('exht_name')->where([['exht_distinguish',$dist], ['exht_status', 1]])->first()->exht_name;
    }

    /**
     * 批量查询音乐文件
     * @param $music_id
     * @return array
     */
    public static function selectMusicRoad($music_id)
    {
        $music_road = [];
        $music_lyric = [];
        $road_data = Exhibit::select('exht_name', 'exht_content')->whereIn('exht_id', $music_id)->get()->toArray();
        foreach ($road_data as $key => $road){
            array_push($music_road, $road_data[$key]['exht_name']);
            array_push($music_lyric, $road_data[$key]['exht_content']);
        }
        return [$music_road, $music_lyric];
    }



}
