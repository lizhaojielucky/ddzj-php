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

namespace app\common\service;


use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\PayNotifyLogic;
use app\common\logic\RefundLogic;
use app\common\model\order\Order;
use app\common\model\RechargeOrder;
use app\common\model\user\UserAuth;
use EasyWeChat\Factory;
use EasyWeChat\Payment\Application;

class WeChatPayService extends BasePayService
{
    /**
     * 授权信息
     */
    protected $auth;


    /**
     * 微信配置
     */
    protected $config;


    /**
     * easyWeChat实例
     */
    protected $pay;


    /**
     * 当前使用客户端
     */
    protected $terminal;


    /**
     * 初始化微信配置
     * @param $terminal //用户终端
     * @param null $userId //用户id(获取授权openid)
     */
    public function __construct($terminal, $userId = null)
    {
        $this->terminal = $terminal;
        $this->config = WeChatConfigService::getWechatConfigByTerminal($terminal);
        $this->pay = Factory::payment($this->config);
        if ($userId !== null) {
            $this->auth = UserAuth::where(['user_id' => $userId, 'terminal' => $terminal])->findOrEmpty();
        }
    }

    /**
     * @notes 退款
     * @param $data
     * @return array|\EasyWeChat\Kernel\Support\Collection|false|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @author ljj
     * @date 2022/2/15 4:58 下午
     */
    public function refund($data)
    {
        if (!empty($data["transaction_id"])) {
            return $this->pay->refund->byTransactionId(
                $data['transaction_id'],
                $data['refund_sn'],
                $data['total_fee'],
                $data['refund_fee']
            );
        } else {
            return false;
        }
    }

    /**
     * @notes 发起微信支付统一下单
     * @param $from
     * @param $order
     * @return array|false|string
     * @author 段誉
     * @date 2021/8/4 15:05
     */
    public function pay($from, $order)
    {
        try {
            switch ($this->terminal) {
                case UserTerminalEnum::WECHAT_MMP:
                case UserTerminalEnum::WECHAT_OA:
                    $result = $this->jsapiPay($from, $order);
                    break;
                case UserTerminalEnum::H5:
                    $result = $this->mwebPay($from, $order);
                    break;
                default:
                    throw new \Exception('支付方式错误');
            }
            return [
                'config' => $result,
                'pay_way' => PayEnum::WECHAT_PAY
            ];
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes jsapi支付(小程序,公众号)
     * @param $from
     * @param $order
     * @return array|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 段誉
     * @date 2021/8/4 15:05
     */
    public function jsapiPay($from, $order)
    {
        $check_source = [UserTerminalEnum::WECHAT_MMP, UserTerminalEnum::WECHAT_OA];
        if ($this->auth->isEmpty() && in_array($this->terminal, $check_source)) {
            throw new \Exception('获取授权信息失败');
        }
        $result = $this->pay->order->unify($this->getAttributes($from, $order));
        $this->checkResultFail($result);
        return $this->pay->jssdk->bridgeConfig($result['prepay_id'], false);
    }


    /**
     * @notes h5支付 (非微信环境下h5)
     * @param $from
     * @param $order
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 段誉
     * @date 2021/8/4 15:07
     */
    public function mwebPay($from, $order)
    {
        $result = $this->pay->order->unify($this->getAttributes($from, $order));
        $this->checkResultFail($result);
        $redirect_url = request()->domain() . '/mobile/bundle/pages/h5_pay_query/h5_pay_query?pay_way='.PayEnum::WECHAT_PAY;
        $redirect_url = urlencode($redirect_url);
        return $result['mweb_url'] . '&redirect_url=' . $redirect_url;
    }

    /**
     * @notes 验证微信返回数据
     * @param $result
     * @throws \Exception
     * @author 段誉
     * @date 2021/8/4 14:56
     */
    public function checkResultFail($result)
    {
        if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            if (isset($result['return_code']) && $result['return_code'] == 'FAIL') {
                throw new \Exception($result['return_msg']);
            }
            if (isset($result['err_code_des'])) {
                throw new \Exception($result['err_code_des']);
            }
            throw new \Exception('未知原因');
        }
    }


    /**
     * @notes 支付请求参数
     * @param $from
     * @param $order
     * @return array
     * @author 段誉
     * @date 2021/8/4 15:07
     */
    public function getAttributes($from, $order)
    {
        switch ($from) {
            case 'order':
                $attributes = [
                    'trade_type' => 'JSAPI',
                    'body' => '预约服务',
                    'total_fee' => $order['order_amount'] * 100, // 单位：分
                    'openid' => $this->auth['openid'],
                    'attach' => 'order',
                ];
                break;
            case 'recharge':
                $attributes = [
                    'trade_type' => 'JSAPI',
                    'body' => '充值',
                    'total_fee' => $order['order_amount'] * 100, // 单位：分
                    'openid' => $this->auth['openid'],
                    'attach' => 'recharge',
                ];
                break;
        }

        //h5支付类型
        if ($this->terminal == UserTerminalEnum::H5) {
            $attributes['trade_type'] = 'MWEB';
        }

        //修改微信统一下单,订单编号 -> 支付回调时截取前面的单号 18个
        //修改原因:回调时使用了不同的回调地址,导致跨客户端支付时(例如小程序,公众号)可能出现201,商户订单号重复错误
        $suffix = mb_substr(time(), -4);
        $attributes['out_trade_no'] = $order['sn'] . $attributes['trade_type'] . $this->terminal . $suffix;

        return $attributes;
    }


    /**
     * @notes 支付回调
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     * @author 段誉
     * @date 2021/8/13 14:19
     */
    public function notify()
    {
        $app = new Application($this->config);
        $response = $app->handlePaidNotify(function ($message, $fail) {

            if ($message['return_code'] !== 'SUCCESS') {
                return $fail('通信失败');
            }

            // 用户是否支付成功
            if ($message['result_code'] === 'SUCCESS') {
                $extra['transaction_id'] = $message['transaction_id'];
                $attach = $message['attach'];
                $message['out_trade_no'] = mb_substr($message['out_trade_no'], 0, 18);
                switch ($attach) {
                    case 'order':
                        $order = Order::where(['sn' => $message['out_trade_no']])->findOrEmpty();
                        if ($order->isEmpty() || $order['pay_status'] >= PayEnum::ISPAID) {
                            return true;
                        }

                        //特殊情况：用户在前端支付成功的情况下，调用回调接口之前，订单被关闭
                        if ($order['order_status'] == OrderEnum::ORDER_STATUS_CLOSE) {

                            //原路退款
                            $order['transaction_id'] = $extra['transaction_id'];
                            (new RefundLogic())->refund($order,$order['order_amount']);

                            return true;
                        }

                        PayNotifyLogic::handle('order', $message['out_trade_no'], $extra);
                        break;
                    case 'recharge':
                        $order = RechargeOrder::where(['sn' => $message['out_trade_no']])->findOrEmpty();
                        if($order->isEmpty() || $order->pay_status == PayEnum::ISPAID) {
                            return true;
                        }
                        PayNotifyLogic::handle('recharge', $message['out_trade_no'], $extra);
                        break;
                }
            } elseif ($message['result_code'] === 'FAIL') {
                // 用户支付失败

            }
            return true; // 返回处理完成

        });
        return $response->send();
    }
}