<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ObtainFileController extends Controller
{
    //请求用户头像资源
    public function getPhoto(Request $request)
    {
        $disk     = $request->disk;
        $filename = $request->filename;
        $temp_path = tempnam(sys_get_temp_dir(), $filename);
        file_put_contents($temp_path, Storage::disk($disk)->get($filename));
        $downResponse = new BinaryFileResponse($temp_path);
        return $downResponse;
    }
    //请求文章资源
    public function getArticalResources()
    {

    }

}