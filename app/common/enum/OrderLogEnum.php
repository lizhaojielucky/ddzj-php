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


class OrderLogEnum
{
    //操作人类型
    const TYPE_SYSTEM   = 1;//系统
    const TYPE_ADMIN    = 2;//后台
    const TYPE_USER     = 3;//用户


    //订单动作
    const USER_ADD_ORDER        = 101;//提交订单
    const USER_CANCEL_ORDER     = 102;//取消订单
    const USER_CONFIRM_ORDER    = 103;//确认收货
    const USER_PAID_ORDER       = 104;//支付订单
    const USER_VERIFICATION     = 105;//师傅核销订单

    const USER_CONFIRM_SERVICE   = 106;//师傅确认服务

    const SHOP_CANCEL_ORDER     = 201;//商家取消订单
    const SHOP_ORDER_REMARKS    = 202;//商家备注
    const SHOP_VERIFICATION     = 203;//商家核销订单
    const SHOP_DISPATCH_STAFF   = 204;//商家指派师傅
    const SHOP_EDIT_APPOINTMENT_TIME   = 205;//商家修改预约时间
    const SHOP_EDIT_ACTUAL_PAY = 206;//商家修改实际支付金额
    const SHOP_GENERATE_ORDER    = 207;//商家生成订单


    const SYSTEM_CANCEL_ORDER   = 301;//系统取消超时未付款订单
    const SYSTEM_CONFIRM_ORDER  = 302;//系统核销订单
    const SYSTEM_CANCEL_APPOINT_ORDER   = 303;//系统取消超过预约时间订单



    /**
     * @notes 操作人
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 2:17 下午
     */
    public static function getOperatorDesc($value = true)
    {
        $desc = [
            self::TYPE_SYSTEM   => '系统',
            self::TYPE_ADMIN            => '后台',
            self::TYPE_USER             => '用户',
        ];

        if (true === $value) {
            return $desc;
        }
        return $desc[$value];
    }

    /**
     * @notes 订单日志
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 2:17 下午
     */
    public static function getRecordDesc($value = true)
    {
        $desc = [
            //系统
            self::SYSTEM_CANCEL_ORDER   => '系统取消超时未付款订单',
            self::SYSTEM_CONFIRM_ORDER  => '系统核销订单',
            self::SYSTEM_CANCEL_APPOINT_ORDER  => '系统取消超过预约时间订单',

            //商家
            self::SHOP_CANCEL_ORDER     => '商家取消订单',
            self::SHOP_ORDER_REMARKS    => '商家备注',
            self::SHOP_VERIFICATION     => '商家核销订单',
            self::SHOP_DISPATCH_STAFF   => '商家指派师傅',
            self::SHOP_EDIT_APPOINTMENT_TIME   => '商家修改预约时间',
            self::SHOP_EDIT_ACTUAL_PAY  => '商家修改实际支付金额',
            self::SHOP_GENERATE_ORDER   => '商家生成子订单',

            //会员
            self::USER_ADD_ORDER        => '会员提交订单',
            self::USER_CANCEL_ORDER     => '会员取消订单',
            self::USER_CONFIRM_ORDER    => '会员确认收货',
            self::USER_PAID_ORDER       => '会员支付订单',
            self::USER_VERIFICATION     => '师傅核销订单',
            self::USER_CONFIRM_SERVICE  => '师傅确认服务',
        ];

        if (true === $value) {
            return $desc;
        }
        return $desc[$value];
    }
}