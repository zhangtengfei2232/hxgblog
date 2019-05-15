<?php
namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Users extends Authenticatable
{
    use Notifiable;
    protected $table = 'users';                 //重写表名
    protected $primaryKey = 'user_id';          //重写表的主键ID
    protected $dateFormat = 'U';                //重写表的时间存储格式为时间戳
    protected $fillable = ['nick_name', 'email', 'password',
        'head_portrait', 'introduce', 'phone', 'distinguish', 'sex'];
    /**
     * 添加用户信息
     * @param $data
     * @return bool
     */
    public static function addUserData($data)
    {
        $data = self::hashPassword($data);
        $user = new static($data);
        DB::beginTransaction();
        try{
            $user->save();
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }

    /**修改用户信息
     * @param $data
     */
    public static function updateUserData($data)
    {
        $data = self::hashPassword($data);
    }
    /**
     * 如果有密码就加密
     * @param $data
     * @return mixed
     */
    private static function hashPassword($data)
    {
        if (array_has($data, 'password')) {
            $data['password'] = bcrypt($data['password']);
        }
        return $data;
    }

    /**
     * 更新token
     * @return mixed|string
     */
    public function generateToken()
    {
        $this->api_token = str_random(128);
        $this->updated_token_at = millisecond();
        $this->save();
        return $this->api_token;
    }

    /**
     * 获取用户信息
     * @param $user_phone
     * @return mixed
     */
    public static function getUserInformationData($user_phone)
    {
        return Users::where('phone', $user_phone)->select(
            'nick_name','email','head_portrait','introduce','phone','sex','distinguish'
        )->first();
    }

    /**
     * 判断用户手机号是否存在
     * @param $user_phone
     * @return bool
     */
    public static function isPhoneExist($user_phone)
    {
        return (Users::where('phone','=', $user_phone)->count()) > 1 ?  true : false;
    }

    /**
     * 判断用户的昵称是否被占用
     * @param $user_nick_name
     * @return bool
     */
    public static function isNickNameExist($user_nick_name, $phone){
        return (Users::where([
            ['phone','!=', $phone],
            ['nick_name','=', $user_nick_name]
        ])->count()) > 1 ? true : false;
    }
    public static function updateUserInformationData($data)
    {
        Users::where('phone', $data['phone'])->update($data);
    }

    /**
     * 查询以前的用户头像文件路径
     * @param $phone
     * @return mixed
     */
    public static function selectOldHeadPortrait($phone)
    {
        return Users::where('phone', $phone)->select('head_portrait')->first()->head_portrait;

    }




}