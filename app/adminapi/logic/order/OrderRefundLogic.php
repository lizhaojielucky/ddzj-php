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

namespace app\adminapi\logic\order;


use app\common\enum\OrderRefundEnum;
use app\common\logic\BaseLogic;
use app\common\model\order\OrderRefund;
use app\common\model\order\OrderRefundLog;
use think\facade\Db;

class OrderRefundLogic extends BaseLogic
{
    /**
     * @notes 重新退款
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2022/9/9 6:18 下午
     */
    public function reRefund($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            //新增退款日志
            OrderRefundLog::create([
                'sn' => generate_sn(new OrderRefundLog(), 'sn'),
                'refund_id' => $params['id'],
                'type' => OrderRefundEnum::TYPE_ADMIN,
                'operator_id' => $params['admin_id'],
            ]);

            //更新退款记录状态
            OrderRefund::update(['refund_status'=>OrderRefundEnum::STATUS_ING],['id'=>$params['id']]);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }
}