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


use app\api\lists\OrderLists;
use app\api\logic\OrderLogic;
use app\api\validate\OrderValidate;
use app\api\validate\PlaceOrderValidate;

class OrderController extends BaseShopController
{
    /**
     * @notes 提交订单
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/25 10:12 上午
     */
    public function placeOrder()
    {
        $data = [
            'terminal' => $this->userInfo['terminal'],
            'user_id'=> $this->userId
        ];
        $params = (new PlaceOrderValidate())->post()->goCheck('', $data);

        //订单结算信息
        $settlement = (new OrderLogic())->settlement($params);
        if (false === $settlement) {
            return $this->fail(OrderLogic::getError());
        }
        //结算信息
        if ($params['action'] == 'settlement') {
            return $this->data($settlement);
        }
        //提交订单
        $result = OrderLogic::submitOrder($settlement);
        if (false === $result) {
            return $this->fail(OrderLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * @notes 订单列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/28 10:01 上午
     */
    public function lists()
    {
        return $this->dataLists(new OrderLists());
    }

    /**
     * @notes 订单详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/28 11:23 上午
     */
    public function detail()
    {
        $params = (new OrderValidate())->get()->goCheck('detail');
        $result = (new OrderLogic())->detail($params['id']);
        return $this->success('',$result);
    }

    /**
     * @notes 取消订单
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/28 11:36 上午
     */
    public function cancel()
    {
        $params = (new OrderValidate())->post()->goCheck('cancel');
        $params['user_id'] = $this->userId;
        $result = (new OrderLogic())->cancel($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 删除订单
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/28 11:50 上午
     */
    public function del()
    {
        $params = (new OrderValidate())->post()->goCheck('del');
        (new OrderLogic())->del($params['id']);
        return $this->success('操作成功',[],1,1);
    }
}