<?php
// +----------------------------------------------------------------------
// | LikeShop100%开源免费商用电商系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | Gitee下载：https://gitee.com/likeshop_gitee/likeshop
// | 访问官网：https://www.likemarket.net
// | 访问社区：https://home.likemarket.net
// | 访问手册：http://doc.likemarket.net
// | 微信公众号：好象科技
// | 好象科技开发团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------

// | Author: LikeShopTeam
// +----------------------------------------------------------------------

namespace app\common\logic;


use app\common\enum\AccountLogEnum;
use app\common\enum\notice\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\PayEnum;
use app\common\model\order\Order;
use app\common\model\RechargeOrder;
use app\common\model\user\User;
use app\common\service\ConfigService;
use think\facade\Db;
use think\facade\Log;

/**
 * 支付成功后处理订单状态
 * Class PayNotifyLogic
 * @package app\api\logic
 */
class PayNotifyLogic extends BaseLogic
{
    public static function handle($action, $orderSn, $extra = [])
    {
        Db::startTrans();
        try {
            self::$action($orderSn, $extra);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            Log::write(implode('-', [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ]));
            self::setError($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * @notes 调用回调方法统一处理 更新订单支付状态
     * @param $orderSn
     * @param array $extra
     * @author ljj
     * @date 2022/3/1 11:35 上午
     */
    private static function order($orderSn, $extra = [])
    {
        $order = Order::with(['order_goods'])->where(['sn' => $orderSn])->findOrEmpty();

        //更新订单状态
        Order::update([
            'pay_status' => PayEnum::ISPAID,
            'pay_time' => time(),
            'order_status' => OrderEnum::ORDER_STATUS_APPOINT,
            'transaction_id' => $extra['transaction_id'] ?? ''
        ], ['id' => $order['id']]);

        //添加订单日志
        (new OrderLogLogic())->record(OrderLogEnum::TYPE_USER,OrderLogEnum::USER_PAID_ORDER,$order['id'],$order['user_id']);

        // 订单付款通知 - 通知买家
        event('Notice', [
            'scene_id' =>  NoticeEnum::ORDER_PAY_NOTICE,
            'params' => [
                'user_id' => $order['user_id'],
                'order_id' => $order['id']
            ]
        ]);

        // 订单付款通知 - 通知卖家
        $mobile = ConfigService::get('website', 'mobile');
        if (!empty($mobile)) {
            event('Notice', [
                'scene_id' =>  NoticeEnum::ORDER_PAY_NOTICE_PLATFORM,
                'params' => [
                    'mobile' => $mobile,
                    'order_id' => $order['id']
                ]
            ]);
        }
    }


    /**
     * @notes 充值回调
     * @param $orderSn
     * @param array $extra
     * @author ljj
     * @date 2022/12/26 5:00 下午
     */
    public static function recharge($orderSn, $extra = [])
    {
        $order = RechargeOrder::where('sn', $orderSn)->findOrEmpty()->toArray();

        // 增加用户累计充值金额及用户余额
        User::update([
            'user_money' => ['inc',$order['order_amount']],
            'total_recharge_amount' => ['inc',$order['order_amount']],
        ],['id'=>$order['user_id']]);

        // 记录账户流水
        AccountLogLogic::add($order['user_id'], AccountLogEnum::MONEY,AccountLogEnum::USER_RECHARGE_ADD_MONEY,AccountLogEnum::INC, $order['order_amount'], $order['sn']);

        // 更新充值订单状态
        RechargeOrder::update([
            'transaction_id' => $extra['transaction_id'],
            'pay_status' => PayEnum::ISPAID,
            'pay_time' => time(),
        ],['id'=>$order['id']]);
    }
}