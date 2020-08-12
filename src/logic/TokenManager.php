<?php


namespace ke\auth\logic;


use think\facade\Cache;
use think\facade\Cookie;

class TokenManager
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
        if (empty($_ENV['PHPUNIT'])) {
            Cookie::set($this->prefix . '_' . $key, $value, $this->expire_in);
        } else {
            $_COOKIE[$this->prefix . '_' . $key] = $value;
        }
    }


    /**
     * 获取Cookie
     * @param $key
     * @return mixed
     */
    protected function getCookie($key)
    {
        if (empty($_ENV['PHPUNIT'])) {
            return Cookie::get($this->prefix . '_' . $key);
        } else {
            return $_COOKIE[$this->prefix . '_' . $key];
        }
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
     * 删除cookie
     * @param $key
     */
    protected function rmCookie($key)
    {
        Cache::rm($this->prefix . ':' . $key);
    }


    /**
     * 创建令牌
     *
     * @param string $key 用户标识
     * @param int $expire 有效时间
     * @return string
     */
    public function create($key, $expire = 28800)
    {
        $this->expire_in = $expire;
        $this->token = strtoupper(md5(uniqid($key) . mt_rand(0, 9999999)));
        $this->setCookie('token', $this->token);
        $this->setCache('auth:' . $this->token, [
            'expire_in'=>$expire,
            'key'=>$key,
        ]);
        return $this->token;
    }


    /**
     * 校验令牌合法性
     * 成功返回用户标识
     *
     * @return false|string
     */
    public function verify()
    {
        list($type, $this->token) = explode(' ', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (empty($this->token)) {
            return false;
        }
        $cache = $this->getCache('auth:' . $this->token);

        if (empty($cache)) {
            return false;
        }
        // 更新有效期
        $this->expire_in = $cache['expire_in'];
        $this->setCookie('token', $this->token);
        $this->setCache('auth:'. $this->token, [
            'expire_in'=>$this->expire_in,
            'key'=>$cache['key']
        ]);

        return $cache['key'];
    }


    /**
     * 删除令牌
     */
    public function remove()
    {
        $this->rmCache('auth:' . $this->token);
        $this->rmCookie('token');
    }

}
