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

namespace app\adminapi\validate\marketing;


use app\common\validate\BaseValidate;

class RechargeValidate extends BaseValidate
{
    protected $rule = [
        'recharge_open' => 'require|in:0,1',
        'min_recharge_amount' => 'float|egt:0',
    ];

    protected $message = [
        'recharge_open.require' => '请选择充值功能状态',
        'recharge_open.in' => '充值功能状态值错误',
        'min_recharge_amount.float' => '最低充值金额必须为浮点数',
        'min_recharge_amount.egt' => '最低充值金额必须大于等于0',
    ];


    public function sceneSetSettings()
    {
        return $this->only(['recharge_open','min_recharge_amount']);
    }
}