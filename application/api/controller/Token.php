<?php

namespace app\api\controller;
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods", "*");//允许任何method
header("Access-Control-Allow-Headers", "*");//允许任何自定义header
header("Access-Control-Allow-Credentials", "true");//允许跨域cookie
use app\common\controller\Api;
use fast\Random;

/**
 * Token接口
 */
class Token extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     * 检测Token是否过期
     *
     */
    public function check()
    {
        $token = $this->auth->getToken();
        $tokenInfo = \app\common\library\Token::get($token);
        $this->success('', ['token' => $tokenInfo['token'], 'expires_in' => $tokenInfo['expires_in']]);
    }

    /**
     * 刷新Token
     *
     */
    public function refresh()
    {
        //删除源Token
        $token = $this->auth->getToken();
        \app\common\library\Token::delete($token);
        //创建新Token
        $token = Random::uuid();
        \app\common\library\Token::set($token, $this->auth->id, 2592000);
        $tokenInfo = \app\common\library\Token::get($token);
        $this->success('', ['token' => $tokenInfo['token'], 'expires_in' => $tokenInfo['expires_in']]);
    }
}
