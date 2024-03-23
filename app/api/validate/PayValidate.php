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


use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\model\order\Order;
use app\common\model\RechargeOrder;
use app\common\validate\BaseValidate;

class PayValidate extends BaseValidate
{
    protected $rule = [
        'from'      => 'require',
        'pay_way'   => 'require|in:' . PayEnum::BALANCE_PAY . ',' . PayEnum::WECHAT_PAY . ',' . PayEnum::ALI_PAY,
        'order_id'  => 'require|checkOrderId'
    ];

    protected $message = [
        'from.require'      => '参数缺失',
        'pay_way.require'   => '支付方式参数缺失',
        'pay_way.in'        => '支付方式参数错误',
        'order_id.require'  => '订单参数缺失'
    ];

    public function scenePayway()
    {
        return $this->only(['from', 'order_id', 'scene'])
            ->append('scene','require');
    }

    public function scenePrepay()
    {
        return $this->only(['from', 'pay_way', 'order_id'])
            ->append('order_id','checkOrder');
    }


    /**
     * @notes 检验订单id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/28 5:58 下午
     */
    public function checkOrderId($value,$rule,$data)
    {
        switch ($data['from']) {
            case 'order':
                $result = Order::where('id',$value)->findOrEmpty();
                break;
            case 'recharge':
                $result = RechargeOrder::where('id',$value)->findOrEmpty();
                break;
        }
        if ($result->isEmpty()) {
            return '订单不存在';
        }
        return true;
    }

    /**
     * @notes 检验订单状态
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/28 6:02 下午
     */
    public function checkOrder($value,$rule,$data)
    {
        switch ($data['from']) {
            case 'order':
                $result = Order::where('id',$value)->findOrEmpty()->toArray();
                if ($result['order_status'] == OrderEnum::ORDER_STATUS_CLOSE) {
                    return '订单已关闭';
                }
                if ($result['pay_status'] == PayEnum::ISPAID) {
                    return '订单已支付';
                }
                break;
            case 'recharge':
                $result = RechargeOrder::where('id',$value)->findOrEmpty()->toArray();
                if ($result['pay_status'] == PayEnum::ISPAID) {
                    return '订单已支付';
                }
                break;
        }

        return true;
    }
}