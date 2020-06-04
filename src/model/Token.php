<?php
/**
 * +----------------------------------------------------------------------
 * | Name: keAdmin
 * | Author King east To 1207877378@qq.com
 * +----------------------------------------------------------------------
 */


namespace ke\auth\model;


use think\Db;
use think\facade\Cache;
use think\facade\Cookie;

class Token
{
    private $prefix = 'admin';

    private $expire_in;

    private $token;


    /**
     * 设置cookie
     * @param $key
     * @param $value
     */
    protected function setCookie($key, $value)
    {
        Cookie::set($this->prefix . '_' . $key, $value, $this->expire_in);
    }


    /**
     * 设置缓存
     * @param $key
     * @param $value
     */
    protected function setCache($key, $value)
    {
        Cache::set($this->prefix . ':' . $key, $value, $this->expire_in);
    }


    /**
     * 读取缓存
     * @param $key
     * @return mixed
     */
    protected function getCache($key)
    {
        return Cache::get($this->prefix . ':' . $key);
    }


    /**
     * 删除缓存
     * @param $key
     * @return bool
     */
    protected function rmCache($key)
    {
        return Cache::rm($this->prefix . ':' . $key);
    }


    /**
     * 创建令牌
     *
     * @param User $info 令牌存储信息
     * @param int $expire 有效时间
     * @return string
     */
    public function create(User $info, $expire = 28800)
    {
        $this->expire_in = $expire;
        $this->token = strtoupper(md5(uniqid($info->id) . mt_rand(0, 9999999)));
        $this->setCookie('token', $this->token);
        $this->setCache('auth:' . $this->token, [
            'expire_in'=>$expire,
            'id'=>$info->id,
        ]);
        return $this->token;
    }


    /**
     * 校验令牌合法性
     * 成功返回用户信息
     *
     * @param string $token
     * @return false|User
     */
    public function verify($token)
    {
        $this->token = $token;
        $cache = $this->getCache('auth:' . $token);

        if (empty($cache)) {
            return false;
        }
        // 更新有效期
        $this->expire_in = $cache['expire_in'];
        $this->setCookie('token', $token);
        $this->setCache('auth:'. $token, [
            'expire_in'=>$this->expire_in,
            'id'=>$cache['id']
        ]);

        return User::where('id', $cache['id'])->find();
    }


    /**
     * 删除令牌
     * @param $token
     */
    public function remove($token = null)
    {
        if (is_null($token)) {
            $token = $this->token;
        }
        $this->rmCache('auth:' . $token);
    }

}
