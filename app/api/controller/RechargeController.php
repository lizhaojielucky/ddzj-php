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

namespace app\api\controller;


use app\api\lists\RechargeLists;
use app\api\logic\RechargeLogic;
use app\api\validate\RechargeValidate;

class RechargeController extends BaseShopController
{
    /**
     * @notes 充值
     * @return \think\response\Json
     * @throws \think\Exception
     * @author ljj
     * @date 2022/12/16 16:03
     */
    public function recharge()
    {
        $params = (new RechargeValidate())->post()->goCheck('recharge',['user_id'=>$this->userId,'terminal'=>$this->userInfo['terminal']]);
        $result = (new RechargeLogic())->recharge($params);
        return $this->success('',$result);
    }

    /**
     * @notes 充值记录列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/6/9 3:13 下午
     */
    public function logLists()
    {
        return $this->dataLists(new RechargeLists());
    }
}