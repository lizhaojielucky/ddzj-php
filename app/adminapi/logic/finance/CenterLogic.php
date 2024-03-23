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

namespace app\adminapi\logic\finance;

use app\common\enum\OrderRefundEnum;
use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\order\Order;
use app\common\model\order\OrderRefund;

class CenterLogic extends BaseLogic
{
    /**
     * @notes 财务中心
     * @return array
     * @author ljj
     * @date 2022/9/9 6:28 下午
     */
    public function center()
    {
        return [
            //累计营业额
            'total_amount' => Order::where(['pay_status'=>PayEnum::ISPAID])->sum('order_amount'),
            //累计成交订单
            'total_order' => Order::where(['pay_status'=>PayEnum::ISPAID])->count(),
            //已退款金额
            'total_refund_amount' => OrderRefund::where(['refund_status'=>OrderRefundEnum::STATUS_SUCCESS])->sum('refund_amount'),
            //待退款金额
            'wait_refund_amount' => OrderRefund::where(['refund_status'=>[OrderRefundEnum::STATUS_ING,OrderRefundEnum::STATUS_FAIL]])->sum('refund_amount'),
        ];
    }
}