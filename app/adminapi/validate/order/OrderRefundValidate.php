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

namespace app\adminapi\validate\order;


use app\common\enum\OrderRefundEnum;
use app\common\model\order\OrderRefund;
use app\common\validate\BaseValidate;

class OrderRefundValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId'
    ];

    protected $message = [
        'id.require' => '参数缺失',
    ];


    public function sceneReRefund()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 校验重复退款
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/9/9 6:14 下午
     */
    public function checkId($value,$rule,$data)
    {
        $result = OrderRefund::where('id',$value)->findOrEmpty()->toArray();
        if (!$result) {
            return '退款记录不存在';
        }
        if ($result['refund_status'] != OrderRefundEnum::STATUS_FAIL) {
            return '退款失败才可以重新退款';
        }

        return true;
    }
}