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

namespace app\adminapi\validate\setting;


use app\common\model\auth\Admin;
use app\common\validate\BaseValidate;
use think\facade\Config;

class AdminValidate extends BaseValidate
{
    protected $rule = [
        'admin_id' => 'require',
        'avatar' => 'require',
        'name' => 'require',
        'account' => 'require|checkAccount',
        'password' => 'length:6,32|checkPassword',
        'new_password' => 'requireWith:password|length:6,32',
        'confirm_password' => 'requireWith:password|confirm:new_password',
    ];

    protected $message = [
        'admin_id.require' => '参数错误',
        'avatar.require' => '请选择头像',
        'account.require' => '请输入账号',
        'name.require' => '请输入名称',
        'password.length' => '当前密码长度须在6-32位字符',
        'new_password.requireWith' => '修改密码时新的密码必填',
        'new_password.length' => '新的密码长度须在6-32位字符',
        'confirm_password.requireWith' => '修改密码时确认密码必填',
        'confirm_password.confirm' => '确认密码与新密码不匹配',
    ];


    public function sceneSetAdmin()
    {
        return $this->only(['admin_id','avatar','account','name','password','new_password','confirm_password']);
    }


    /**
     * @notes 检验账号是否被占用
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/4/18 2:45 下午
     */
    public function checkAccount($value, $rule, $data)
    {
        $where[] = ['account', '=', $value];
        $where[] = ['id', '<>', $data['admin_id']];
        $admin = Admin::where($where)->findOrEmpty();
        if (!$admin->isEmpty()) {
            return '账号已被占用';
        }
        return true;
    }


    /**
     * @notes 检验当前密码是否正确
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/4/18 3:05 下午
     */
    public function checkPassword($value, $rule, $data)
    {
        $passwordSalt = Config::get('project.unique_identification');
        $password = create_password($value, $passwordSalt);
        $where[] = ['password', '=', $password];
        $where[] = ['id', '=', $data['admin_id']];
        $admin = Admin::where($where)->findOrEmpty();
        if ($admin->isEmpty()) {
            return '当前密码不正确';
        }
        return true;
    }
}