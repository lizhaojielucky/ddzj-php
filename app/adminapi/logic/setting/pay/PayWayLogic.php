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

namespace app\adminapi\logic\setting\pay;


use app\common\enum\user\UserTerminalEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\pay\PayWay;

class PayWayLogic extends BaseLogic
{
    /**
     * @notes 获取支付方式
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/15 6:51 下午
     */
    public function getPayWay()
    {
        $pay_way = PayWay::select();
        $pay_way = $pay_way->append(['pay_way_desc','icon'])->toArray();
        if (empty($pay_way)) {
            return [];
        }

        $lists = [];
        for ($i=1;$i<=max(array_column($pay_way,'scene'));$i++) {
            foreach ($pay_way as $val) {
                if ($val['scene'] == $i) {
                    $lists[$i][] = $val;
                }
            }
        }

        return $lists;
    }

    /**
     * @notes 设置支付方式
     * @param $params
     * @return bool|string
     * @throws \Exception
     * @author ljj
     * @date 2022/2/15 7:02 下午
     */
    public function setPayWay($params)
    {
        $pay_way = new PayWay;
        $data = [];
        foreach ($params as $key=>$value) {
            $is_default = array_column($value,'is_default');
            $is_default_num = array_count_values($is_default);
            $status = array_column($value,'status');
            $scene_name = UserTerminalEnum::getTermInalDesc($key);
            if (!in_array(YesNoEnum::YES,$is_default)) {
                return $scene_name.'支付场景缺少默认支付';
            }
            if ($is_default_num[YesNoEnum::YES] > 1) {
                return $scene_name.'支付场景的默认值只能存在一个';
            }
            if (!in_array(YesNoEnum::YES,$status)) {
                return $scene_name.'支付场景至少开启一个支付状态';
            }

            foreach ($value as $val) {
                $result = PayWay::where('id',$val['id'])->findOrEmpty();
                if ($result->isEmpty()) {
                    continue;
                }
                if ($val['is_default'] == YesNoEnum::YES && $val['status'] == YesNoEnum::NO) {
                    return $scene_name.'支付场景的默认支付未开启支付状态';
                }

                $data[] = [
                    'id' => $val['id'],
                    'is_default' => $val['is_default'],
                    'status' => $val['status'],
                ];
            }
        }
        $pay_way->saveAll($data);
        return true;
    }
}