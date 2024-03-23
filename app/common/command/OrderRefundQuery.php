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


use app\common\enum\notice\NoticeEnum;
use app\common\enum\OrderRefundEnum;
use app\common\model\order\OrderRefundLog;
use app\common\service\WeChatConfigService;
use EasyWeChat\Factory;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use think\facade\Log;

class OrderRefundQuery extends Command
{
    protected function configure()
    {
        $this->setName('order_refund_query')
            ->setDescription('订单退款查询');
    }

    protected function execute(Input $input, Output $output)
    {
        $lists = OrderRefundLog::alias('orl')
            ->join('order_refund or', 'or.id = orl.refund_id')
            ->field('orl.sn as refund_log_sn,orl.id as refund_log_id,or.id refund_id,or.order_terminal,or.user_id,or.order_id')
            ->where(['orl.refund_status'=>OrderRefundEnum::STATUS_ING])
            ->select()
            ->toArray();

        if (empty($lists)) {
            return true;
        }

        Db::startTrans();
        try{
            foreach ($lists as $val) {

                //微信配置信息
                $wechatConfig = WeChatConfigService::getWechatConfigByTerminal($val['order_terminal']);
                if (!file_exists($wechatConfig['cert_path']) || !file_exists($wechatConfig['key_path'])) {
                    throw new \Exception('微信证书不存在,请联系管理员!');
                }

                $app = Factory::payment($wechatConfig);
                //通过商户退款单号查询退款状态
                $result = $app->refund->queryByOutRefundNumber($val['refund_log_sn']);

                if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                    $refund_status = OrderRefundEnum::STATUS_ING;

                    if ($result['refund_status_0'] == 'SUCCESS') {
                        $refund_status = OrderRefundEnum::STATUS_SUCCESS;

                        //更新订单退款状态
                        \app\common\model\order\OrderRefund::update([
                            'refund_status' => OrderRefundEnum::STATUS_SUCCESS,
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
                    }
                    if ($result['refund_status_0'] == 'REFUNDCLOSE') {
                        $refund_status = OrderRefundEnum::STATUS_FAIL;

                        //更新订单退款状态
                        \app\common\model\order\OrderRefund::update([
                            'refund_status' => OrderRefundEnum::STATUS_FAIL,
                        ], ['id'=>$val['refund_id']]);
                    }

                    //更新退款日志记录
                    OrderRefundLog::update([
                        'wechat_refund_id' => $result['refund_id_0'],
                        'refund_status' => $refund_status,
                        'refund_msg' => json_encode($result, JSON_UNESCAPED_UNICODE),
                    ], ['id'=>$val['refund_log_id']]);
                } else {
                    if (isset($result['return_code']) && $result['return_code'] == 'FAIL') {
                        throw new \Exception($result['return_msg']);
                    }

                    if (isset($result['err_code_des'])) {
                        throw new \Exception($result['err_code_des']);
                    }
                }
            }

            Db::commit();
            return true;
        } catch(\Exception $e) {
            Db::rollback();
            Log::write('订单退款查询失败,失败原因:' . $e->getMessage());
            return false;
        }
    }
}