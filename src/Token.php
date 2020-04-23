<?php
/**
 * +----------------------------------------------------------------------
 * | Name: keAdmin
 * | Author King east To 1207877378@qq.com
 * +----------------------------------------------------------------------
 */


namespace ke\auth;



class Token
{
    /**
     * 密钥
     * @var $secret
     */
    private $secret;


    public function __construct()
    {
        $this->secret = env('app_secret');
    }


    /**
     * 创建令牌
     *
     * @param array $info 令牌存储信息
     * @param int $expire 有效时间
     * @return string
     */
    public function create($info, $expire = 28800)
    {
        $jsonStr = base64_encode(json_encode($info));
        $expire_time = $_SERVER['REQUEST_TIME'] + $expire;

        $sign = $this->signature($info, $expire_time);
        return $jsonStr . '.' . $expire_time . '.' . $sign;
    }


    /**
     * 校验正确性
     * 成功时返回令牌存储信息(array)
     *
     * @param string $token
     * @return false|array
     */
    public function verify($token)
    {
        try {
            list($info, $expire_time, $sign) = explode('.', $token);
            if ($expire_time <= $_SERVER['REQUEST_TIME']) {
                return false;
            }
            $arrInfo = json_decode(base64_decode($info), true);
            $newSign = $this->signature($arrInfo, $expire_time);

            if ($newSign === $sign) {
                return $arrInfo;
            } else {
                return false;
            }
        }catch (\Exception $e) {
            return false;
        }
    }


    /**
     * 签名
     *
     * @param array $info 令牌存储信息
     * @param int $expire_time 过期时间
     * @return string
     */
    private function signature($info, $expire_time)
    {
        ksort($info);
        $str = '';
        foreach ($info as $k=>$v) {
            $str .= strtolower($k) . ':' . urlencode($v) . ';';
        }
        $time = dechex($expire_time);
        return hash_hmac('sha1', sha1($time . ':' . $str), $this->secret);
    }

}
