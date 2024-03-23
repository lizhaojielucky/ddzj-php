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

namespace app\adminapi\validate\user;

use app\common\model\user\User;
use app\common\validate\BaseValidate;

class UserValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkUser',
        'field' => 'require',
        'value' => 'require|checkField',
        'adjust_action' => 'require|in:1,2',
        'adjust_num' => 'require|float|gt:0|checkAdjustNum',
    ];

    protected $message = [
        'id.require' => '请选择用户',
        'field.require' => '请选择操作',
        'value.require' => '请输入内容',
        'adjust_action.require' => '请选择余额增减',
        'adjust_action.in' => '调余额增减值错误',
        'adjust_num.require' => '请输入调整金额',
        'adjust_num.float' => '调整金额必须为浮点数',
        'adjust_num.gt' => '调整金额必须大于零',
    ];


    public function sceneEditInfo()
    {
        return $this->only(['id', 'field', 'value']);
    }

    public function sceneAdjustBalance()
    {
        return $this->only(['id', 'adjust_action', 'adjust_num']);
    }


    /**
     * @notes 用户验证
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/5/24 9:49 上午
     */
    public function checkUser($value,$rule,$data)
    {
        $result = User::where('id',$value)->findOrEmpty();
        if ($result->isEmpty()) {
            return '用户不存在';
        }
        return true;
    }

    /**
     * @notes 验证更新信息
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/5/24 9:50 上午
     */
    public function checkField($value, $rule, $data)
    {
        $allowField = ['real_name','mobile','sex','account'];

        if (!in_array($data['field'], $allowField)) {
            return '用户信息不允许更新';
        }


        switch ($data['field']) {
            case 'mobile':
                if (false == $this->validate($data['value'], 'mobile', $data)) {
                    return '手机号码格式错误';
                }

                //验证手机号码是否存在
                $mobile = User::where([['id', '<>', $data['id']], ['mobile', '=', $data['value']]])
                    ->find();
                if ($mobile) {
                    return '手机号码已存在';
                }


                if ($value == 'account') {
                    $user = User::where([
                        ['account', '=', $data['value']],
                        ['id', '<>', $data['id']]
                    ])->findOrEmpty();
                    if (!$user->isEmpty()) {
                        return '账号已存在!';
                    }
                }

                break;

        }
        return true;
    }

    /**
     * @notes 校验调整余额
     * @param $vaule
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/4/12 11:58 上午
     */
    protected function checkAdjustNum($vaule,$rule,$data)
    {
        $user = User::find($data['id']);

        if(1 == $data['adjust_action']){
            return true;
        }
        $surplusMoeny = $user->user_money - $vaule;
        if($surplusMoeny < 0){
            return '用户可用余额仅剩'.$user->user_money;
        }

        return true;
    }
}