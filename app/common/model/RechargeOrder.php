<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\common\model;

use app\common\enum\PayEnum;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;


class RechargeOrder extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    /**
     * @notes 支付方式
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/12/2 6:25 下午
     */
    public function getPayWayDescAttr($value,$data)
    {
        return PayEnum::getPayTypeDesc($data['pay_way']);
    }

    /**
     * @notes 支付状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/12/2 6:26 下午
     */
    public function getPayStatusDescAttr($value,$data)
    {
        return PayEnum::getPayStatusDesc($data['pay_status']);
    }

    /**
     * @notes 支付时间
     * @param $value
     * @param $data
     * @return false|string
     * @author ljj
     * @date 2022/12/2 6:47 下午
     */
    public function getPayTimeAttr($value,$data)
    {
        return empty($value) ? '' : date('Y-m-d H:i:s',$value);
    }
}