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

namespace app\common\enum;


class OrderEnum
{
    //订单状态
    const ORDER_STATUS_WAIT_PAY       = 0;  //待支付
    const ORDER_STATUS_APPOINT        = 1;  //预约中
    const ORDER_STATUS_SERVICE        = 2;  //服务中
    const ORDER_STATUS_FINISH         = 3;  //已完成
    const ORDER_STATUS_CLOSE          = 4;  //已关闭

    //核销状态
    const WAIT_VERIFICATION = 0;//待核销
    const VERIFICATION = 1;//已核销

    //派单状态
    const DISPATCH_NO = 0;//未派单
    const DISPATCH_YES = 1;//已派单

    const ORDER_TYPE_USER = 0; //用户
    const ORDER_TYPE_SYSTEM = 1; //系统

    //是否需要复制
    const COPY_NO = 0;//不需要
    const COPY_YES = 1;//需要
    const COPY_FINISH = 2;//已复制


    /**
     * @notes 订单状态
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 11:03 上午
     */
    public static function getOrderStatusDesc($value = true)
    {
        $data = [
            self::ORDER_STATUS_WAIT_PAY => '待支付',
            self::ORDER_STATUS_APPOINT => '预约中',
            self::ORDER_STATUS_SERVICE => '服务中',
            self::ORDER_STATUS_FINISH => '已完成',
            self::ORDER_STATUS_CLOSE => '已关闭',
        ];
        if (true === $value) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * @notes 核销状态
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2021/8/26 4:29 下午
     */
    public static function getVerificationStatusDesc($value = true)
    {
        $data = [
            self::WAIT_VERIFICATION => '待核销',
            self::VERIFICATION => '已核销',
        ];
        if (true === $value) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * @notes 派单状态
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2022/8/29 4:56 下午
     */
    public static function getDispatchDesc($value = true)
    {
        $data = [
            self::DISPATCH_NO => '未派单',
            self::DISPATCH_YES => '已派单',
        ];
        if (true === $value) {
            return $data;
        }
        return $data[$value];
    }
}