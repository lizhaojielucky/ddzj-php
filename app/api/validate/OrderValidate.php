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

namespace app\api\validate;


use app\common\enum\OrderEnum;
use app\common\model\order\Order;
use app\common\validate\BaseValidate;

class OrderValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId',
    ];

    protected $message = [
        'id.require' => '参数错误',
    ];

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneCancel()
    {
        return $this->only(['id'])
            ->append('id','checkCancel');
    }

    public function sceneDel()
    {
        return $this->only(['id'])
            ->append('id','checkDel');
    }


    /**
     * @notes 检验订单id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/28 10:12 上午
     */
    public function checkId($value,$rule,$data)
    {
        $result = Order::where(['id'=>$value])->findOrEmpty();
        if ($result->isEmpty()) {
            return '订单不存在';
        }
        return true;
    }

    /**
     * @notes 检验订单能否取消
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/28 11:28 上午
     */
    public function checkCancel($value,$rule,$data)
    {
        $result = Order::where(['id'=>$value])->findOrEmpty()->toArray();
        if ($result['order_status'] != OrderEnum::ORDER_STATUS_WAIT_PAY && $result['order_status'] != OrderEnum::ORDER_STATUS_APPOINT) {
            return '该订单无法取消';
        }
        return true;
    }

    /**
     * @notes 检验订单能否删除
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/28 11:48 上午
     */
    public function checkDel($value,$rule,$data)
    {
        $result = Order::where(['id'=>$value])->findOrEmpty()->toArray();
        if ($result['order_status'] != OrderEnum::ORDER_STATUS_CLOSE) {
            return '该订单无法删除';
        }
        return true;
    }
}