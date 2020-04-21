<?php
namespace App\Model;


class Article extends BaseModel
{
    protected $table = 'article';


    /**
     * 添加文章
     * @param $data
     * @return mixed
     */
    public static function addArticleData($data)
    {
        return Article::insertGetId($data);
    }


    /**
     * 修改文章信息
     * @param $data
     * @return bool
     */
    public static function updateArticleData($data)
    {
        return (Article::where('art_id', $data['art_id'])->update($data) > 0) ? true : false ;
    }


    /**
     * 查询最新的前5篇文章
     * @return mixed
     */
    public static function selectNewArticleData()
    {
        return Article::orderBy('created_at','desc')->limit(4)->get();
    }


    /**
     * 查询点击率最高的前5篇文章
     * @return mixed
     */
    public static function selectBrowseTopData()
    {
        return Article::orderBy('art_browse', 'desc')->limit(5)->get();
    }


    /**
     * 搜索某一篇文章的所有内容
     * @param $art_id
     * @return mixed
     */
    public static function selectAloneArticleData($art_id)
    {
        return Article::where('art_id', $art_id)->get();
    }


    /**
     * 根据文章ID查找文章
     * @param $art_id_datas
     * @param int $status
     * @return array|mixed
     */
    public static function byIdSelectArticleData($art_id_datas, $status = 1)
    {
        $art_data = Article::whereIn('art_id', $art_id_datas)->get();
        return ($status == 1) ? self::timeResolution($art_data) : $art_data;
    }


    /**
     * 文章浏览量加 '1'
     * @param $art_id
     * @return mixed
     */
    public static function addArticleBrowseData($art_id)
    {
        $art_browse = Article::select('art_browse')->where('art_id', $art_id)->get();
        return Article::where('art_id', $art_id)->update(
               ['art_browse' => ($art_browse[0]->art_browse + 1)]);
    }


    /**
     * 根据文章ID，查询文章的赞/踩
     * @param $art_id
     * @return mixed
     */
    public static function selectArticlePraiseTrampleNum($art_id)
    {
        return Article::select('art_praise_points', 'art_trample_points')->where('art_id', $art_id)->first();
    }


    /**
     * 根据文章的ID，更新文章的赞/踩
     * @param $status
     * @param $art_id
     * @param $is_first
     * @param bool $is_same
     * @return mixed
     */
    public static function updateArticlePraiseTrample($status, $art_id, $is_first, $is_same = false)
    {
        $num = self::selectArticlePraiseTrampleNum($art_id);
        if ($is_first) {
            if ($status == 1) {
                ++$num->art_praise_points;
                Article::where('art_id', $art_id)->update(['art_praise_points' => $num->art_praise_points]);
                return $num;
            }
            ++$num->art_trample_points;
            Article::where('art_id', $art_id)->update(['art_trample_points' => $num->art_trample_points]);
            return $num;
        }
        if ($is_same) {
            if ($status == 1) {
                --$num->art_praise_points;
                Article::where('art_id', $art_id)->update(['art_praise_points' => $num->art_praise_points]);
                return $num;
            }
            --$num->art_trample_points;
            Article::where('art_id', $art_id)->update(['art_trample_points' => $num->art_trample_points]);
            return $num;
        } else {
            if ($status == 1) {
                $praise_num = ++$num->art_praise_points;
                $trample_num = --$num->art_trample_points;
            } else {
                $praise_num = --$num->art_praise_points;
                $trample_num = ++$num->art_trample_points;
            }
            Article::where('art_id',$art_id)->update(['art_praise_points' => $praise_num,'art_trample_points' => $trample_num]);
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
    public static function selectArticleData($art_name, $time, $total)
    {
        $query = new Article();
        if (! empty($time)) {
            $query = $query->whereBetween ('created_at', $time);
        }
        if (! empty($art_name)) {
            $query = $query->where('art_title', 'like', "%".$art_name."%");
        }
        return $query->paginate($total);
    }


    /**
     * 获取文章信息
     * @param $total
     * @return mixed
     */
    public static function getArticleData($total)
    {
        return Article::paginate($total);
    }


    /**
     * 查询多个文章的封面路径
     * @param $art_id_data
     * @return array
     */
    public static function selectArticleCoverRoad($art_id_data)
    {
        $art_road_data = [];
        $art_road = Article::select('art_cover')->whereIn('art_id', $art_id_data)->get()->toArray();
        foreach ($art_road as $road) {
            array_push($art_road_data, $road['art_cover']);
        }
        return $art_road_data;
    }


}
