<?php
namespace App\Model;


class Users extends BaseModel
{
    /**
     * 更新token
     * @return mixed|string
     */
    private $api_token;
    public function generateToken()
    {
        $this->api_token = str_random(128);
        $this->save();
        return $this->api_token;
    }
}