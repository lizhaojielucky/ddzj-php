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


use app\common\enum\OrderLogEnum;
use app\common\model\order\OrderLog;

class OrderLogLogic extends BaseLogic
{
    /**
     * @notes 订单日志
     * @param int $type 类型:1-系统;2-后台;3-用户;
     * @param int $channel 渠道编号。变动方式
     * @param int $order_id 订单id
     * @param int $operator_id 操作人id
     * @param string $content 日志内容
     * @author ljj
     * @date 2022/2/11 3:37 下午
     */
    public function record(int $type,int $channel,int $order_id,int $operator_id = 0,string $content = '')
    {
        OrderLog::create([
            'type'          => $type,
            'channel'       => $channel,
            'order_id'      => $order_id,
            'operator_id'   => $operator_id,
            'content'       => $content ?: OrderLogEnum::getRecordDesc($channel),
            'create_time'   => time(),
            'update_time'   => time(),
        ]);
    }
}