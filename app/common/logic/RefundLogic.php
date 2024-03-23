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

namespace app\common\logic;


use app\common\enum\OrderRefundEnum;
use app\common\enum\PayEnum;
use app\common\model\order\OrderRefund;
use app\common\model\order\OrderRefundLog;
use app\common\service\WeChatConfigService;
use app\common\service\WeChatPayService;

class RefundLogic extends BaseLogic
{
    protected $refund;
    protected $refund_log;

    /**
     * @notes 退款
     * @param $order
     * @param $refund_amount
     * @param $type
     * @param $operator_id
     * @return bool
     * @throws \Exception
     * @author ljj
     * @date 2022/2/15 5:05 下午
     */
    public function refund($order, $refund_amount,$type,$operator_id)
    {
        if ($refund_amount <= 0) {
            return false;
        }

        //生成退款记录
        $this->log($order,$refund_amount,$type,$operator_id);

        //原路退款
        switch ($order['pay_way']) {
            //微信退款
            case PayEnum::WECHAT_PAY:
                $this->wechatRefund($order,$refund_amount);
                break;
        }

        return true;
    }

    /**
     * @notes 退款记录
     * @param $order
     * @param $refund_amount
     * @param $type
     * @param $operator_id
     * @author ljj
     * @date 2022/2/15 3:49 下午
     */
    public function log($order,$refund_amount,$type,$operator_id)
    {
        $refund = OrderRefund::create([
            'sn' => generate_sn(new OrderRefund(), 'sn'),
            'order_id' => $order['id'],
            'user_id' => $order['user_id'],
            'order_amount' => $order['order_amount'],
            'refund_amount' => $refund_amount,
            'order_terminal' => $order['order_terminal'],
            'transaction_id' => $order['transaction_id'],
            'type' => $type,
        ]);

        //退款日志
        $refund_log = OrderRefundLog::create([
            'sn' => generate_sn(new OrderRefundLog(), 'sn'),
            'refund_id' => $refund->id,
            'type' => $type,
            'operator_id' => $operator_id,
        ]);

        $this->refund = $refund;
        $this->refund_log = $refund_log;
    }


    /**
     * @notes 微信退款
     * @param $order
     * @param $refund_amount
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @author ljj
     * @date 2022/2/15 5:06 下午
     */
    public function wechatRefund($order,$refund_amount)
    {
        //微信配置信息
        $wechatConfig = WeChatConfigService::getWechatConfigByTerminal($order['order_terminal']);
        if (!file_exists($wechatConfig['cert_path']) || !file_exists($wechatConfig['key_path'])) {
            throw new \Exception('微信证书不存在,请联系管理员!');
        }

        //发起退款
        $result = (new WeChatPayService($order['order_terminal']))->refund([
            'transaction_id' => $order['transaction_id'],
            'refund_sn' => $this->refund_log->sn,
            'total_fee' => $refund_amount * 100,//订单金额,单位为分
            'refund_fee' => intval($refund_amount * 100),//退款金额
        ]);


        if ($result['return_code'] == 'FAIL' || $result['result_code'] == 'FAIL') {
            if ($result['err_code'] == 'SYSTEMERROR' || $result['err_code'] == 'BIZERR_NEED_RETRY') {
                return true;
            }

            //更新退款日志记录
            OrderRefundLog::update([
                'wechat_refund_id' => $result['refund_id'] ?? 0,
                'refund_status' => OrderRefundEnum::STATUS_FAIL,
                'refund_msg' => json_encode($result, JSON_UNESCAPED_UNICODE),
            ], ['id'=>$this->refund_log->id]);

            //更新订单退款状态
            OrderRefund::update([
                'refund_status' => OrderRefundEnum::STATUS_FAIL,
            ], ['id'=>$this->refund->id]);
        }

        return true;
    }
}