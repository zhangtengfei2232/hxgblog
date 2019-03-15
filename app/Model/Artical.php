<?php
namespace App\Model;


class Artical extends BaseModel
{
    protected $table = 'artical';

    //查询最新的前5篇文章
    public static function selectNewArticalData()
    {
        return Artical::orderBy('created_at','desc')->limit(5)->get();
    }
    //查询点击率最高的前5篇文章
    public static function selectBrowseTopData()
    {
        return Artical::orderBy('arti_browse', 'desc')->limit(6)->get();
    }
    //搜索某一篇文章的所有内容
    public static function selectAloneArticalData($art_id)
    {
        return Artical::where('arti_id',$art_id)->get();
    }

    //根据文章ID查找文章
    public static function byIdSelectArticalData($art_id_datas)
    {
        return self::timeResolution(Artical::whereIn('arti_id', $art_id_datas)->get());

    }
    //文章浏览量加 '1'
    public static function addArticalBrowseData($art_id)
    {
        $art_browse = Artical::select('arti_browse')->where('arti_id', $art_id)->get();
        return Artical::where('arti_id', $art_id)->update(
               ['arti_browse' => ($art_browse[0]->arti_browse + 1)]);
    }


}