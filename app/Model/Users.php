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
        'head_portrait', 'introduce', 'phone', 'role', 'sex'];


    /**
     * 注册方式区分
     */
    CONST ACT_NUM_PWD = 1;              //账号密码注册
    CONST BAI_DU      = 2;              //百度账号注册
    CONST ALI_PAY     = 3;              //支付宝注册
    CONST QQ          = 4;              //qq注册
    CONST WEI_BO      = 5;              //微博注册
    CONST GITHUB      = 6;              //github 注册


    /**
     * 注册方式字段标识
     */
    CONST ACT_NUM_PWD_FIELD = 'act_num_pwd';
    CONST BAI_DU_FIELD      = 'bai_du';
    CONST ALI_PAY_FIELD     = 'ali_pay';
    CONST QQ_FIELD          = 'qq';
    CONST WEI_BO_FIELD      = 'wei_bo';
    CONST GITHUB_FIELD      = 'github';


    /**
     * 注册方式标识映射
     * @var array
     */
    public static $THIRD_PARTY_LOGIN_MAP = array(
        self::ACT_NUM_PWD => self::ACT_NUM_PWD_FIELD,
        self::BAI_DU      => self::BAI_DU_FIELD,
        self::ALI_PAY     => self::ALI_PAY_FIELD,
        self::QQ          => self::QQ_FIELD,
        self::WEI_BO      => self::WEI_BO_FIELD,
        self::GITHUB      => self::GITHUB_FIELD
    );

    CONST DEFAULT_NICK_NAME_PREFIX_FIELD = '用户';

    /**
     * 登录方式
     */
    CONST LOGIN_WAY_ACT_NUM_PWD = 1;
    CONST LOGIN_WAY_SMS         = 2;
    CONST LOGIN_WAY_THIRD_PARTY = 3;

    /**
     * 登录方式字段标识
     */
     CONST LOGIN_WAY_ACT_NUM_PWD_FIELD = '账号';
     CONST LOGIN_WAY_SMS_FIELD         = '短信';
     CONST LOGIN_WAY_THIRD_PARTY_FIELD = '第三方';

    /**
     * 登录方式标识映射
     */
    public static $LOGIN_WAY_MAP = array(
        self::LOGIN_WAY_ACT_NUM_PWD => self::LOGIN_WAY_ACT_NUM_PWD_FIELD,
        self::LOGIN_WAY_SMS         => self::LOGIN_WAY_SMS_FIELD,
        self::LOGIN_WAY_THIRD_PARTY => self::LOGIN_WAY_THIRD_PARTY_FIELD
    );

    CONST ALI_PAY_HD_PT_EXT_NAME = '.JPEG';
    CONST BAI_DU_HD_PT_EXT_NAME  = '.JPEG';
    CONST QQ_HD_PT_EXT_NAME      = '.JPEG';
    CONST WEI_BO_HD_PT_EXT_NAME  = '.JPEG';
    CONST GITHUB_HD_PT_EXT_NAME  = '.JPEG';


    /**
     * 添加用户信息
     * @param $data
     * @param $register_way
     * @return bool
     */
    public static function addUserData($data, $register_way = Users::ACT_NUM_PWD)
    {
        //如果是账号密码注册，密码需要HASH，认证时候用
        if ($register_way == self::ACT_NUM_PWD) {
            $data = self::hashPassword($data);
        }
        $user = new static($data);
        DB::beginTransaction();
        try {
            $user->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }


    /**
     * 修改用户信息
     * @param $data
     * @return mixed
     */
    public static function updateUserData($data)
    {
         return self::hashPassword($data);
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
            'nick_name','email','head_portrait','introduce','phone','sex','role'
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
     * @param $phone
     * @return bool
     */
    public static function isNickNameExist($user_nick_name, $phone){
        return (Users::where([
            ['phone','!=', $phone],
            ['nick_name','=', $user_nick_name]
        ])->count()) > 1 ? true : false;
    }

    /**
     * 修改用户信息
     * @param $data
     */
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


    /**
     * 查询用户的角色
     * @param $phone
     * @return mixed
     */
    public static function selectUserRoles($phone)
    {
        return Users::select('role')->where('phone', $phone)->first()->role;
    }


    /**
     * 判断是否有这个用户
     * @param $phone
     * @return mixed
     */
    public static function validateUser($phone)
    {
        return Users::where('phone',$phone)->exists();
    }


    /**
     * 获取用户实例
     * @param $phone
     * @return mixed
     */
    public static function getUserData($phone)
    {
        return Users::where('phone', $phone)->first();
    }


    /**
     * 修改用户密码
     * @param $phone
     * @param $new_password
     * @return bool
     */
    public static function updatePassword($phone, $new_password)
    {
        return Users::where('phone', $phone)->update(['password' => bcrypt($new_password)]) > 0;
    }


    /**
     * 根据 access_token 查询用户
     * @param $access_token
     * @return mixed
     */
    public static function getThirdPartyUserData($access_token)
    {
        return Users::where('access_token', $access_token)->get();
    }


    /**
     * 统计用户总数
     * @return int 用户总数
     */
    public static function selectUserNum()
    {
        return Users::count();
    }


}
