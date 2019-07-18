<?php
namespace App\Model;


class Artical extends BaseModel
{
    protected $table = 'artical';


    public static function addArticalData($data)
    {
        return Artical::insertGetId($data);
    }

    /**
     * 修改文章信息
     * @param $data
     * @return bool
     */
    public static function updateArticalData($data)
    {
        return (Artical::where('arti_id', $data['arti_id'])->update($data) > 0) ? true : false ;
    }

    /**
     * 查询最新的前5篇文章
     * @return mixed
     */
    public static function selectNewArticalData()
    {
        return Artical::orderBy('created_at','desc')->limit(4)->get();
    }

    /**
     * 查询点击率最高的前5篇文章
     * @return mixed
     */
    public static function selectBrowseTopData()
    {
        return Artical::orderBy('arti_browse', 'desc')->limit(5)->get();
    }

    /**
     * 搜索某一篇文章的所有内容
     * @param $art_id
     * @return mixed
     */
    public static function selectAloneArticalData($art_id)
    {
        return Artical::where('arti_id',$art_id)->get();
    }

    /**
     * 根据文章ID查找文章
     * @param $art_id_datas
     * @param int $status
     * @return array|mixed
     */
    public static function byIdSelectArticalData($art_id_datas, $status = 1)
    {
        $art_data = Artical::whereIn('arti_id', $art_id_datas)->get();
        return ($status == 1) ? self::timeResolution($art_data) : $art_data;
    }

    /**
     * 文章浏览量加 '1'
     * @param $art_id
     * @return mixed
     */
    public static function addArticalBrowseData($art_id)
    {
        $art_browse = Artical::select('arti_browse')->where('arti_id', $art_id)->get();
        return Artical::where('arti_id', $art_id)->update(
               ['arti_browse' => ($art_browse[0]->arti_browse + 1)]);
    }

    /**
     * 根据文章ID，查询文章的赞/踩
     * @param $art_id
     * @return mixed
     */
    public static function selectArticalPraiseTrampleNum($art_id)
    {
        return Artical::select('arti_praise_points', 'arti_trample_points')->where('arti_id', $art_id)->first();
    }

    /**
     * 根据文章的ID，更新文章的赞/踩
     * @param $status
     * @param $art_id
     * @param $is_first
     * @param bool $is_same
     * @return mixed
     */
    public static function updateArticalPraiseTrample($status, $art_id, $is_first, $is_same = false)
    {
        $num = self::selectArticalPraiseTrampleNum($art_id);
        if($is_first){
            if($status == 1){
                ++$num->arti_praise_points;
                Artical::where('arti_id', $art_id)->update(['arti_praise_points' => $num->arti_praise_points]);
                return $num;
            }
            ++$num->arti_trample_points;
            Artical::where('arti_id', $art_id)->update(['arti_trample_points' => $num->arti_trample_points]);
            return $num;
        }
        if($is_same){
            if($status == 1){
                --$num->arti_praise_points;
                Artical::where('arti_id', $art_id)->update(['arti_praise_points' => $num->arti_praise_points]);
                return $num;
            }
            --$num->arti_trample_points;
            Artical::where('arti_id', $art_id)->update(['arti_trample_points' => $num->arti_trample_points]);
            return $num;
        }else{
            if($status == 1){
                $praise_num = ++$num->arti_praise_points;
                $trample_num = --$num->arti_trample_points;
            }else{
                $praise_num = --$num->arti_praise_points;
                $trample_num = ++$num->arti_trample_points;
            }
            Artical::where('arti_id',$art_id)->update(['arti_praise_points' => $praise_num,'arti_trample_points' => $trample_num]);
        }
        return $num;
    }

    /**
     * 组合查询文章
     * @param $art_name
     * @param $time
     * @param $total
     * @return mixed
     */
    public static function selectArticalData($art_name, $time, $total)
    {
        $query = new Artical();
        if(!empty($time)) $query = $query->whereBetween ('created_at',$time);
        if(!empty($art_name)) $query = $query->where('arti_title', 'like', "%".$art_name."%");
        return $query->paginate($total);
    }

    /**
     * 获取文章信息
     * @param $total
     * @return mixed
     */
    public static function getArticalData($total)
    {
        return Artical::paginate($total);

    }

    /**
     * 查询多个文章的封面路径
     * @param $art_id_data
     * @return array
     */
    public static function selectArticalCoverRoad($art_id_data)
    {
        $art_road_data = [];
        $art_road = Artical::select('arti_cover')->whereIn('arti_id',$art_id_data)->get()->toArray();
        foreach ($art_road as $road) array_push($art_road_data,$road['arti_cover']);
        return $art_road_data;
    }


}