<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\api\validate;


use app\common\model\user\User;
use app\common\validate\BaseValidate;

class UserValidate extends BaseValidate
{
    protected $rule = [
        'field' => 'require|checkField',
        'value' => 'require',
        'code' => 'require',
        'encrypted_data' => 'require',
        'iv' => 'require',
        'mobile' => 'require|mobile',
        'password' => 'require|length:6,20|alphaDash',
        'old_password' => 'require',
    ];

    protected $message = [
        'field.require'     => '参数缺失',
        'value.require'     => '值不存在',
        'code.require' => '参数缺失',
        'encrypted_data.require' => '参数缺失',
        'iv.require' => '参数缺失',
        'mobile.require' => '请输入手机号码',
        'mobile.mobile' => '无效的手机号码',
        'old_password.require' => '请输入原密码',
        'password.require' => '请输入登录密码',
        'password.length' => '登录密码须在6-20位之间',
        'password.alphaDash' => '登录密码须为字母数字下划线或破折号',
    ];

    public function sceneSetInfo()
    {
        return $this->only(['field','value']);
    }

    public function sceneGetMobileByMnp()
    {
        return $this->only(['code', 'encrypted_data', 'iv']);
    }

    public function sceneResetPasswordCaptcha()
    {
        return $this->only(['mobile']);
    }

    public function sceneResetPassword()
    {
        return $this->only(['mobile', 'code', 'password'])
            ->append('password', 'require|length:6,20|alphaDash|checkComplexity');
    }

    public function sceneSetPassword()
    {
        return $this->only(['password']);
    }

    public function sceneChangePassword()
    {
        return $this->only(['password', 'old_password']);
    }

    public function sceneBindMobileCaptcha()
    {
        return $this->only(['mobile']);
    }

    public function sceneChangeMobileCaptcha()
    {
        return $this->only(['mobile']);
    }

    public function sceneBindMobile()
    {
        return $this->only(['mobile', 'code']);
    }


    /**
     * @notes 校验字段
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/24 3:42 下午
     */
    protected function checkField($value,$rule,$data)
    {
        $allowField = [
            'nickname','sex','avatar','mobile', 'account', 'real_name'

        ];
        if(!in_array($value,$allowField)){
            return '参数错误';
        }
        if($value != 'mobile') {
            return true;
        }
        $user = User::where([
            ['mobile', '=', $data['value']],
            ['id', '<>', $data['id']]
        ])->findOrEmpty();
        if(!$user->isEmpty()) {
            return '该手机号已被绑定';
        }

        if ($value == 'account') {
            $user = User::where([
                ['account', '=', $data['value']],
                ['id', '<>', $data['id']]
            ])->findOrEmpty();
            if (!$user->isEmpty()) {
                return '账号已被使用!';
            }
        }


        return true;
    }

    /**
     * @notes 校验密码复杂度
     * @param $value
     * @param $rue
     * @param $data
     * @author Tab
     * @date 2021/12/10 15:06
     */
    public function checkComplexity($value, $rue, $data)
    {
        $lowerCase = range('a', 'z');
        $upperCase = range('A', 'Z');
        $numbers = range(0, 9);
        $cases = array_merge($lowerCase, $upperCase);
        $caseCount = 0;
        $numberCount = 0;
        $passwordArr = str_split(trim(($data['password'] . '')));
        foreach ($passwordArr as $value) {
            if (in_array($value, $numbers)) {
                $numberCount++;
            }
            if (in_array($value, $cases)) {
                $caseCount++;
            }
        }
        if ($numberCount >= 1 && $caseCount >= 1) {
            return true;
        }
        return '密码需包含数字和字母';
    }
}