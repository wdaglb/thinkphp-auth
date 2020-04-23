<?php


namespace ke\auth\model;


use think\Db;
use think\facade\Cache;

class Access
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }


    /**
     * 校验当前登录用户权限
     * @param array|string $policy 需要的校验策略
     * @param string $exp 校验方式：and必须所有策略通过返回true
     *                            or只要有一个策略通过则返回true
     * @return bool
     */
    public function hasRule($policy, $exp = 'and')
    {
        $rules = $this->getRule();
        foreach ($rules as $rule) {
            if (is_array($policy)) {
                foreach ($policy as $p) {
                    $is = strtolower($p) === strtolower($rule);
                    if ($is && $exp === 'or') {
                        return true;
                    } else if (!$is && $exp === 'and') {
                        return false;
                    }
                }
                return true;
            } else {
                $is = strtolower($policy) === strtolower($rule);
                if ($is) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * 获取用户拥有的策略
     * @param int|int[] $role_id 单个角色ID或多个角色ID
     * @return string[]
     */
    public function getRule($role_id = null)
    {
        if (is_null($role_id)) {
            $role_id = $this->auth->getRolesId();
        }
        // 获取角色的权限
        return Db::name(Auth::TABLE_ADMIN_ROLE_PERMISSION)
            ->alias('p')
            ->where('role_id', 'in', $role_id)
            ->join(Auth::TABLE_ADMIN_POLICY . ' policy', 'p.policy_id=policy.id')
            ->column('policy.name');
    }


    /**
     * 获取缓存权限
     * @param $id
     * @return array|mixed
     */
    private function getCacheRule($id)
    {
        $list = Cache::get(Auth::TABLE_ADMIN . ':auth:' . $id);
        if (!$list) {
            return [];
        }
        return $list;
    }


    /**
     * 更新权限缓存
     * @param $id
     * @param array $rules
     */
    private function setCacheRule($id, array $rules)
    {
        Cache::set(Auth::TABLE_ADMIN . ':auth:' . $id, $rules);
    }

}
