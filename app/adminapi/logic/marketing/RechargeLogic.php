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

namespace app\adminapi\logic\marketing;


use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

class RechargeLogic extends BaseLogic
{
    /**
     * @notes 设置充值设置
     * @param $params
     * @author ljj
     * @date 2022/11/17 4:43 下午
     */
    public function setSettings($params)
    {
        ConfigService::set('recharge', 'recharge_open', $params['recharge_open']);
        ConfigService::set('recharge', 'min_recharge_amount', $params['min_recharge_amount'] ?? '');
        return true;
    }

    /**
     * @notes 获取充值设置
     * @return array
     * @author ljj
     * @date 2022/11/17 4:46 下午
     */
    public function getSettings()
    {
        return [
            'recharge_open' => ConfigService::get('recharge', 'recharge_open',1),
            'min_recharge_amount' => ConfigService::get('recharge', 'min_recharge_amount'),
        ];
    }
}