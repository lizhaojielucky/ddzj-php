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


namespace app\common\service;


use app\common\enum\AccountLogEnum;
use app\common\enum\PayEnum;
use app\common\logic\AccountLogLogic;
use app\common\model\user\User;


class BalancePayService extends BasePayService
{

    /**
     * @notes 余额支付
     * @param $from
     * @param $order
     * @return array|false
     * @author ljj
     * @date 2022/12/26 5:28 下午
     */
    public function pay($from, $order)
    {
        try {
            $user = User::findOrEmpty($order['user_id']);
            if ($user->isEmpty() || $user['user_money'] < $order['order_amount']) {
                throw new \Exception('余额不足');
            }

            //扣除余额
            User::update([
                'user_money' => ['dec', $order['order_amount']]
            ], ['id' => $order['user_id']]);

            //余额流水
            AccountLogLogic::add($order['user_id'], AccountLogEnum::MONEY,AccountLogEnum::ORDER_DEC_MONEY,AccountLogEnum::DEC, $order['order_amount'], $order['sn']);

            return [
                'pay_way' => PayEnum::BALANCE_PAY
            ];
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

}