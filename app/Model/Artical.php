<?php
namespace App\Model;


class Artical extends BaseModel
{
    protected $table = 'artical';
//    protected $timeField = 'arti_create_time';
    //查询最新的前5篇文章
    public static function selectNewArticalData()
    {
        return self::convertTime(Artical::orderBy('arti_create_time','desc')->limit(5)->get(),"arti_create_time");
    }
    //查询点击率最高的前5篇文章
    public static function selectBrowseTopData()
    {
        return Artical::orderBy('arti_browse', 'desc')->limit(6)->get();
    }
    //搜索某一篇文章的所有内容
    public static function selectAloneArticalData($art_id)
    {
        return self::convertTime(Artical::where('arti_id',$art_id)->get(),"arti_create_time");
    }

    //根据文章ID查找文章
    public static function byIdSelectArticalData($art_id_datas)
    {
        return self::convertTime(Artical::whereIn('arti_id', $art_id_datas)->get(),'arti_create_time', true);

    }


}