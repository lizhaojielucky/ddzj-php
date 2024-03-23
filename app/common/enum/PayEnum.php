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


class PayEnum
{
    //支付类型
    const WECHAT_PAY    = 1; //微信支付
    const ALI_PAY       = 2; //支付宝支付
    const BALANCE_PAY   = 3; //余额支付


    //支付状态
    const UNPAID = 0; //未支付
    const ISPAID = 1; //已支付


    /**
     * @notes 支付类型
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 11:06 上午
     */
    public static function getPayTypeDesc($value = true)
    {
        $data = [
            self::WECHAT_PAY => '微信支付',
            self::ALI_PAY => '支付宝支付',
            self::BALANCE_PAY => '余额支付',
        ];
        if (true === $value) {
            return $data;
        }
        return $data[$value] ?? '未知';
    }

    /**
     * @notes 支付状态
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 11:07 上午
     */
    public static function getPayStatusDesc($value = true)
    {
        $data = [
            self::UNPAID => '未支付',
            self::ISPAID => '已支付',
        ];
        if (true === $value) {
            return $data;
        }
        return $data[$value];
    }
}