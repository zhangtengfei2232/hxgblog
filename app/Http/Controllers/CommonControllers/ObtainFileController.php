<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ObtainFileController extends Controller
{
    /**
     * 请求后端资源
     * @param Request $request
     * @return BinaryFileResponse
     * @throws FileNotFoundException
     */
    public function getResource(Request $request)
    {
        $disk     = $request->input('disk');
        $filename = $request->input('filename');
        return $this->getFileContent($disk, $filename);
    }


    /**
     * 获取文件资源
     * @param $disk
     * @param $filename
     * @return BinaryFileResponse
     * @throws FileNotFoundException
     */
    public function getFileContent($disk, $filename)
    {
        $temp_path = tempnam(sys_get_temp_dir(), $filename);
        file_put_contents($temp_path, Storage::disk($disk)->get($filename));
        return new BinaryFileResponse($temp_path);
    }


    /**从Storage文件下，下载文件
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function downloadFile(Request $request)
    {
        $disk = $request->input('disk');
        $filename = $request->input('filename');
        $file = RESOURCE_ROUTE_DIR . $disk . DIRECTORY_SEPARATOR . $filename;
        if (! file_exists(storage_path($file))) {
            redirect('showEmptyView');                                 //判断文件是否存在
        }
        return response()->download(storage_path($file));
    }


    /**
     * 根据IP获取当前用户所在城市
     * @return JsonResponse
     */
    public function getCityInfo()
    {
        $city_name = getUserPosition("218.29.60.105");                 //根据IP获取当地城市名字
        if ($city_name['code'] == 1) {
            return responseToJson(1, '获取失败');
        }
        $data['city_name']    = $city_name['data'];
        $data['weather_info'] = getWeatherInfoByCity($city_name['data']);  //根据城市名字获取当地天气信息
        return responseToJson(0, '查询成功', $data);
    }



}
