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


use app\common\enum\GoodsEnum;
use app\common\model\goods\Goods;
use app\common\model\user\User;
use app\common\validate\BaseValidate;

class PlaceOrderValidate extends BaseValidate
{
    protected $rule = [
        'user_id' => 'require|checkUser',// 下单用户
        'action' => 'require',// 下单动作(结算/下单)
        'goods' => 'require|array|checkGoods',// 下单服务
        'appoint_time_start' => 'require',// 预约上门时间-开始
        'appoint_time_end' => 'require',// 预约上门时间-结束
    ];


    protected $message = [
        'user_id.require' => '参数缺失',
        'action.require' => '下单动作缺失',
        'goods.require' => '缺失下单服务信息',
        'goods.array' => '下单服务信息格式不正确',
        'appoint_time_start.require' => '请选择预约上门开始时间',
        'appoint_time_end.require' => '请选择预约上门结束时间',
    ];

    /**
     * @notes 检测用户是否存在
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/24 4:57 下午
     */
    public static function checkUser($value, $rule, $data)
    {
        $user = User::findOrEmpty($value);
        if ($user->isEmpty()) {
            return '用户不存在';
        }
        return true;
    }

    /**
     * @notes 验证下单服务信息
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/24 5:01 下午
     */
    public function checkGoods($value, $rule, $data)
    {
        if (!isset($value['id']) || !isset($value['goods_num'])) {
            return '下单服务参数缺失';
        }
        $result = Goods::where(['id'=>$value['id']])->findOrEmpty();
        if ($result->isEmpty()) {
            return '下单服务不存在';
        }
        if ($result['status'] == GoodsEnum::UNSHELVE) {
            return '服务已下架';
        }
        return true;
    }
}