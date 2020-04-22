<?php

namespace App\Model;

class Photo extends BaseModel
{
    protected $table = 'photo';


    /**
     * 添加相册
     * @param $album_photo
     * @param $album_id
     * @return bool
     */
    public static function addAlbumPhotoData($album_photo, $album_id)
    {
        foreach ($album_photo as $photo){
            if (! Photo::insert(['photo_path' => $photo, 'alb_id' => $album_id, 'created_at' => time()])) {
                return false;
            }
        }
        return true;

    }


    /**
     * 查询最新的6张照片
     * @return mixed
     */
    public static function selectNewPhotoData()
    {
        return Photo::select('photo_path')->orderBy('created_at', 'desc')->limit(6)->get();
//        ->where('alb_question',"")->leftJoin('album','photo.alb_id','=','album.alb_id')
    }


    /**
     * 删除相册照片
     * @param $del_photo_id_data
     * @return bool
     */
    public static function deleteMultiplePhotoData($del_photo_id_data)
    {
         return (Photo::whereIn('pho_id', $del_photo_id_data)->delete() == count($del_photo_id_data));
    }


    /**
     * 查询相册的第一张照片
     * @param $album_id
     * @return mixed
     */
    public static function selectAlbumFirstPhoto($album_id)
    {
        return Photo::select('photo_path')->where('alb_id', $album_id)->orderBy('created_at', 'desc')->first()->photo_path;
    }


    /**
     * 根据相册ID查询照片
     * @param $album_id
     * @param $page
     * @return mixed
     */
    public static function byAlbumIdSelectPhotoData($album_id, $page)
    {
        return Photo::where('alb_id', $album_id)->orderBy('created_at', 'desc')->offset($page * 2)->limit(2)->get();
    }


    /**
     * 查询多个相册的第一张最新照片
     * @param $album_data
     * @return mixed
     */
    public static function selectMulAlumFirstPhoto($album_data)
    {
        foreach ($album_data as $key => $album){
            $album_data[$key]['is_has_photo'] = false;
            if ($album_data[$key]['photo_num'] > 0) {
                $album_data[$key]['first_photo_path'] = self::selectAlbumFirstPhoto($album['alb_id']);
                $album_data[$key]['is_has_photo'] = true;
            }
            (empty($album_data[$key]['alb_question'])) ? $album_data[$key]['is_has_question'] = false : $album_data[$key]['is_has_question'] = true;
        }
        return $album_data;
    }


    /**
     * 查询单个相册的所有照片
     * @param $album_id
     * @return array
     */
    public static function selectAlbumImgRoad($album_id)
    {
        $album_img_road = [];
        $img_road= Photo::select('photo_path')->where('alb_id', $album_id)->get()->toArray();
        foreach ($img_road as $key => $road) array_push($album_img_road, $img_road[$key]['photo_path']);
        return $album_img_road;
    }


    /**
     * 删除相册照片
     * @param $album_id
     * @return bool
     */
    public static function deleteAlbumImgData($album_id)
    {
        Photo::where('alb_id', $album_id)->delete();
        return true;
    }


    /**
     * 查询相册照片
     * @param $album_id
     * @return mixed
     */
    public static function selectAlbumImageData($album_id)
    {
        return Photo::where('alb_id', $album_id)->get();
    }

}
