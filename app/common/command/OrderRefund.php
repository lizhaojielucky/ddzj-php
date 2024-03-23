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

namespace app\common\command;


use app\common\enum\AccountLogEnum;
use app\common\enum\notice\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderRefundEnum;
use app\common\enum\PayEnum;
use app\common\logic\AccountLogLogic;
use app\common\model\order\Order;
use app\common\model\order\OrderRefundLog;
use app\common\model\user\User;
use app\common\service\AliPayService;
use app\common\service\WeChatConfigService;
use app\common\service\WeChatPayService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use think\facade\Log;

class OrderRefund extends Command
{
    protected function configure()
    {
        $this->setName('order_refund')
            ->setDescription('订单退款');
    }

    protected function execute(Input $input, Output $output)
    {
        $lists = OrderRefundLog::alias('orl')
            ->join('order_refund or', 'or.id = orl.refund_id')
            ->join('order o', 'o.id = or.order_id')
            ->field('or.order_terminal,or.transaction_id,orl.sn,or.order_amount,or.refund_amount,orl.refund_id,orl.id as refund_log_id,or.user_id,or.order_id,o.pay_way as refund_way,o.sn as order_sn')
            ->where(['orl.refund_status'=>OrderRefundEnum::STATUS_ING,'o.order_type'=>OrderEnum::ORDER_TYPE_USER])
            ->select()
            ->toArray();

        if (empty($lists)) {
            return true;
        }

        foreach ($lists as $val) {
            Db::startTrans();
            try{
                switch ($val['refund_way']) {
                    //微信退款
                    case PayEnum::WECHAT_PAY:
                        //微信配置信息
                        $wechatConfig = WeChatConfigService::getWechatConfigByTerminal($val['order_terminal']);
                        if (!file_exists($wechatConfig['cert_path']) || !file_exists($wechatConfig['key_path'])) {
                            throw new \Exception('微信证书不存在,请联系管理员!');
                        }

                        //发起退款
                        $result = (new WeChatPayService($val['order_terminal']))->refund([
                            'transaction_id' => $val['transaction_id'],
                            'refund_sn' => $val['sn'],
                            'total_fee' => $val['order_amount'] * 100,//订单金额,单位为分
                            'refund_fee' => intval($val['refund_amount'] * 100),//退款金额
                        ]);

                        if (isset($result['return_code']) && $result['return_code'] == 'FAIL') {
                            throw new \Exception($result['return_msg']);
                        }

                        if (isset($result['err_code_des'])) {
                            throw new \Exception($result['err_code_des']);
                        }

                        //更新退款日志记录
                        OrderRefundLog::update([
                            'wechat_refund_id' => $result['refund_id'] ?? 0,
                            'refund_status' => (isset($result['result_code']) && $result['result_code'] == 'SUCCESS') ? 1 : 2,
                            'refund_msg' => json_encode($result, JSON_UNESCAPED_UNICODE),
                        ], ['id'=>$val['refund_log_id']]);

                        //更新订单退款状态
                        \app\common\model\order\OrderRefund::update([
                            'refund_status' => (isset($result['result_code']) && $result['result_code'] == 'SUCCESS') ? 1 : 2,
                        ], ['id'=>$val['refund_id']]);

                        if (isset($result['result_code']) && $result['result_code'] == 'SUCCESS') {
                            // 订单退款成功 - 通知买家
                            event('Notice', [
                                'scene_id' =>  NoticeEnum::REFUND_SUCCESS_NOTICE,
                                'params' => [
                                    'user_id' => $val['user_id'],
                                    'order_id' => $val['order_id']
                                ]
                            ]);
                        }

                        break;
                    //余额退款
                    case PayEnum::BALANCE_PAY:
                        //退回余额
                        User::update(['user_money'=>['inc', $val['refund_amount']]],['id'=>$val['user_id']]);
                        //流水记录
                        AccountLogLogic::add($val['user_id'], AccountLogEnum::MONEY,AccountLogEnum::CANCEL_ORDER_ADD_MONEY,AccountLogEnum::INC, $val['refund_amount'], $val['order_sn']);

                        //更新订单退款状态
                        \app\common\model\order\OrderRefund::update([
                            'refund_status' => 1,
                        ], ['id'=>$val['refund_id']]);
                        //更新退款日志记录
                        OrderRefundLog::update([
                            'refund_status' => 1,
                        ], ['id'=>$val['refund_log_id']]);

                        // 订单退款成功 - 通知买家
                        event('Notice', [
                            'scene_id' =>  NoticeEnum::REFUND_SUCCESS_NOTICE,
                            'params' => [
                                'user_id' => $val['user_id'],
                                'order_id' => $val['order_id']
                            ]
                        ]);
                        break;
                    //支付宝退款
                    case PayEnum::ALI_PAY:
                        //原路退回到支付宝的情况
                        $result = (new AliPayService())->refund($val['order_sn'], $val['refund_amount'], $val['sn']);
                        $result = (array)$result;

                        //更新退款日志记录
                        OrderRefundLog::update([
                            'refund_status' => ($result['code'] == '10000' && $result['msg'] == 'Success' && $result['fundChange'] == 'Y') ? 1 : 2,
                            'refund_msg' => json_encode($result, JSON_UNESCAPED_UNICODE),
                        ], ['id'=>$val['refund_log_id']]);

                        //更新订单退款状态
                        \app\common\model\order\OrderRefund::update([
                            'refund_status' => ($result['code'] == '10000' && $result['msg'] == 'Success' && $result['fundChange'] == 'Y') ? 1 : 2,
                        ], ['id'=>$val['refund_id']]);

                        if ($result['code'] == '10000' && $result['msg'] == 'Success' && $result['fundChange'] == 'Y') {
                            // 订单退款成功 - 通知买家
                            event('Notice', [
                                'scene_id' =>  NoticeEnum::REFUND_SUCCESS_NOTICE,
                                'params' => [
                                    'user_id' => $val['user_id'],
                                    'order_id' => $val['order_id']
                                ]
                            ]);
                        }
                        break;
                }

                Db::commit();
            } catch(\Exception $e) {
                Db::rollback();
                Log::write('订单退款失败,失败原因:' . $e->getMessage());
            }
        }
    }
}