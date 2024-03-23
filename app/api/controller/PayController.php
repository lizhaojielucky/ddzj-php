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

namespace app\api\controller;


use app\common\enum\user\UserTerminalEnum;
use app\common\service\WeChatPayService;
use app\api\logic\PayLogic;
use app\api\validate\PayValidate;

class PayController extends BaseShopController
{
    public array $notNeedLogin = ['notifyMnp','notifyOa'];


    /**
     * @notes 支付方式
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/28 2:56 下午
     */
    public function payWay()
    {
        $params = (new PayValidate())->get()->goCheck('payWay',['user_id'=>$this->userId]);
        $result = PayLogic::payWay($params);
        if ($result === false) {
            return $this->fail(PayLogic::getError());
        }
        return $this->data($result);
    }


    /**
     * @notes 预支付
     * @return \think\response\Json
     * @throws \Exception
     * @author ljj
     * @date 2022/3/1 11:20 上午
     */
    public function prepay()
    {
        $params = (new PayValidate())->post()->goCheck('prepay');
        //支付流程
        $result = PayLogic::pay($params['pay_way'], $params['from'], $params['order_id'], $this->userInfo['terminal']);
        if (false === $result) {
            return $this->fail(PayLogic::getError(), $params);
        }
        return $this->success('', $result);
    }


    /**
     * @notes 小程序支付回调
     * @return \Symfony\Component\HttpFoundation\Response
     * @author 段誉
     * @date 2021/8/13 14:17
     */
    public function notifyMnp()
    {
        return (new WeChatPayService(UserTerminalEnum::WECHAT_MMP))->notify();
    }


    /**
     * @notes 公众号支付回调
     * @return \Symfony\Component\HttpFoundation\Response
     * @author 段誉
     * @date 2021/8/13 14:17
     */
    public function notifyOa()
    {
        return (new WeChatPayService(UserTerminalEnum::WECHAT_OA))->notify();
    }
}