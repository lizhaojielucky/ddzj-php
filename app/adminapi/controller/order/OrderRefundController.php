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

namespace app\adminapi\controller\order;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\order\OrderRefundLists;
use app\adminapi\lists\order\OrderRefundLogLists;
use app\adminapi\logic\order\OrderRefundLogic;
use app\adminapi\validate\order\OrderRefundValidate;

class OrderRefundController extends BaseAdminController
{
    /**
     * @notes 订单退款列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/9/9 4:53 下午
     */
    public function lists()
    {
        return $this->dataLists(new OrderRefundLists());
    }

    /**
     * @notes 退款日志列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/9/9 5:53 下午
     */
    public function logLists()
    {
        return $this->dataLists(new OrderRefundLogLists());
    }

    /**
     * @notes 重新退款
     * @return \think\response\Json
     * @author ljj
     * @date 2022/9/9 6:19 下午
     */
    public function reRefund()
    {
        $params = (new OrderRefundValidate())->post()->goCheck('reRefund');
        $params['admin_id'] = $this->adminId;
        $result = (new OrderRefundLogic())->reRefund($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功',[],1,1);
    }
}