<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ObtainFileController extends Controller
{
    /**
     * 请求用户头像资源
     * @param Request $request
     * @return BinaryFileResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getPhoto(Request $request)
    {
        $disk     = $request->disk;
        $filename = $request->filename;
        return $this->getFileContent($disk, $filename);
    }

    /**
     * 获取文件资源
     * @param $disk
     * @param $filename
     * @return BinaryFileResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getFileContent($disk, $filename)
    {
        $temp_path = tempnam(sys_get_temp_dir(), $filename);
        file_put_contents($temp_path, Storage::disk($disk)->get($filename));
        $downResponse = new BinaryFileResponse($temp_path);
        return $downResponse;
    }

    /**从Storage文件下，下载文件
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function downloadFile(Request $request)
    {
        $file = 'app/public/'.$request->file;
        if(!file_exists(storage_path($file))) return redirect('showEmptyView');//判断文件是否存在
        return response()->download(storage_path($file));
    }

    /**
     * 根据IP获取当前用户所在城市
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCityName()
    {
        $city_name   = getUserPosition("218.29.60.105");
        if($city_name['code'] == 1) return responseToJson(1,'获取失败');
        $data['city_name']     = $city_name['data']['data']['city'];
        return responseToJson(0,'查询成功',$data);
    }

}