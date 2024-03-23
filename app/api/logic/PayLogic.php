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

namespace app\api\logic;


use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\logic\PayNotifyLogic;
use app\common\model\order\Order;
use app\common\model\pay\PayWay;
use app\common\model\RechargeOrder;
use app\common\model\user\User;
use app\common\service\BalancePayService;
use app\common\service\ConfigService;
use app\common\service\WeChatPayService;

class PayLogic extends BaseLogic
{
    /**
     * @notes 支付方式
     * @param $params
     * @return array|false
     * @author ljj
     * @date 2022/2/28 2:56 下午
     */
    public static function payWay($params)
    {
        try {
            $order = [];
            // 获取待支付金额
            if ($params['from'] == 'order') {
                // 订单
                $order = Order::findOrEmpty($params['order_id'])->toArray();
            }
            if ($params['from'] == 'recharge') {
                // 充值
                $order = RechargeOrder::findOrEmpty($params['order_id'])->toArray();
            }

            if (empty($order)) {
                throw new \Exception('订单不存在');
            }

            // 获取订单剩余支付时间
            $cancelUnpaidOrders = ConfigService::get('transaction', 'cancel_unpaid_orders',1);
            $cancelUnpaidOrdersTimes = ConfigService::get('transaction', 'cancel_unpaid_orders_times',30);

            if (empty($cancelUnpaidOrders)) {
                // 不自动取消待支付订单
                $cancelTime = 0;
            } else {
                // 指定时间内取消待支付订单
                $cancelTime = strtotime($order['create_time']) + intval($cancelUnpaidOrdersTimes) * 60;
            }

            $pay_way = PayWay::alias('pw')
                ->join('dev_pay dp', 'pw.pay_id = dp.id')
                ->where(['pw.scene'=>$params['scene'],'pw.status'=>YesNoEnum::YES])
                ->field('dp.id,dp.name,dp.pay_way,dp.image,pw.is_default')
                ->order(['sort'=>'asc','id'=>'desc'])
                ->select()
                ->toArray();
            foreach ($pay_way as $k=>&$item) {
                if ($item['pay_way'] == PayEnum::WECHAT_PAY) {
                    $item['extra'] = '微信快捷支付';
                }

                if ($item['pay_way'] == PayEnum::BALANCE_PAY) {
                    $user_money = User::where(['id' => $params['user_id']])->value('user_money');
                    $item['extra'] = '可用余额:'.$user_money;
                }
                // 充值时去除余额支付
                if ($params['from'] == 'recharge' && $item['pay_way'] == PayEnum::BALANCE_PAY) {
                    unset($pay_way[$k]);
                }
            }

            return [
                'lists' => array_values($pay_way),
                'order_amount' => $order['order_amount'],
                'cancel_time' => $cancelTime,
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 支付
     * @param $payWay // 支付方式
     * @param $from //订单来源(商品订单?充值订单?其他订单?)
     * @param $order_id //订单id
     * @param $terminal //终端
     * @return array|bool|string|void
     * @throws \Exception
     * @author 段誉
     * @date 2021/7/29 14:49
     */
    public static function pay($payWay, $from, $order_id, $terminal)
    {
        $order = [];
        //更新支付方式
        switch ($from) {
            case 'order':
                Order::update(['pay_way' => $payWay], ['id' => $order_id]);
                $order = Order::where('id',$order_id)->findOrEmpty()->toArray();
                break;
            case 'recharge':
                RechargeOrder::update(['pay_way' => $payWay], ['id' => $order_id]);
                $order = RechargeOrder::where('id',$order_id)->findOrEmpty()->toArray();
                break;
        }

        if (empty($order)) {
            self::setError('订单错误');
        }

        if($order['order_amount'] == 0) {
            PayNotifyLogic::handle($from, $order['sn']);
            return ['pay_way'=>$payWay];
        }

        switch ($payWay) {
            case PayEnum::WECHAT_PAY:
                $payService = (new WeChatPayService($terminal, $order['user_id'] ?? null));
                $result = $payService->pay($from, $order);
                break;
            case PayEnum::BALANCE_PAY:
                //余额支付
                $payService = (new BalancePayService());
                $result = $payService->pay($from, $order);
                if (false !== $result) {
                    PayNotifyLogic::handle($from, $order['sn']);
                }
                break;
            default:
                self::$error = '订单异常';
                $result = false;
        }

        //支付成功, 执行支付回调
        if (false === $result && !self::hasError()) {
            self::setError($payService->getError());
        }
        return $result;
    }
}