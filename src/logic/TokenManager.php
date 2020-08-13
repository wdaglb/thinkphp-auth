<?php


namespace ke\auth\logic;


use think\facade\Cache;

class TokenManager
{

    private $prefix = 'admin';


    private $expire_in;


    private $token;


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
     * 获得当前令牌
     * @return string
     */
    public function get()
    {
        if (empty($this->token)) {
            $list = explode(' ', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
            if (count($list) != 2) {
                return false;
            }
            list($type, $this->token) = $list;
        }
        return $this->token;
    }


    /**
     * 创建令牌
     *
     * @param string $key 用户标识
     * @param int $expire_in 有效时间
     * @return string
     */
    public function create($key, $expire_in)
    {
        $this->expire_in = $expire_in;
        $this->token = strtoupper(md5(uniqid($key) . mt_rand(0, 9999999)));
        $this->setCache('auth:' . $this->token, $key);
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
        $this->get();
        if (empty($this->token)) {
            return false;
        }
        $cache = $this->getCache('auth:' . $this->token);

        if (empty($cache)) {
            return false;
        }
        return $cache;
    }


    /**
     * 刷新令牌有效期
     * @param int $expire_in
     * @return false
     */
    public function refresh($expire_in)
    {
        if (empty($this->token)) {
            throw new \InvalidArgumentException('要先verify');
        }
        $this->expire_in = $expire_in;

        $cache = $this->getCache('auth:' . $this->token);

        if (empty($cache)) {
            throw new \InvalidArgumentException('登录态丢失');
        }
        // 更新有效期
        $this->setCache('auth:'. $this->token, $cache);

        return true;
    }


    /**
     * 删除令牌
     */
    public function remove()
    {
        $this->rmCache('auth:' . $this->token);
    }

}
