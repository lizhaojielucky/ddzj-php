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

namespace app\common\command;


use app\common\enum\DefaultEnum;
use app\common\enum\notice\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\model\order\Order;
use app\common\model\staff\Staff;
use app\common\service\ConfigService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class DispatchStaff extends Command
{
    protected function configure()
    {
        $this->setName('dispatch_staff')
            ->setDescription('派遣师傅');
    }

    protected function execute(Input $input, Output $output)
    {
        //是否开启系统随机派单
        $is_auth_dispatch = ConfigService::get('transaction', 'is_auth_dispatch',1);
        if ($is_auth_dispatch != 1) {
            return true;
        }

        //待分配师傅订单列表
        $wait_dispatch_lists = Order::alias('o')
            ->join('order_goods og', 'o.id = og.order_id')
            ->field('o.id,o.appoint_time_start,o.city_id,og.goods_id')
            ->where(['order_status'=>OrderEnum::ORDER_STATUS_APPOINT,'staff_id'=>0])
            ->select()
            ->toArray();
        $wait_dispatch_arr = [];
        foreach ($wait_dispatch_lists as $val) {
            $appoint_time = date('Y-m-d',$val['appoint_time_start']);
            $wait_dispatch_arr[$appoint_time][] = $val;
        }
        if (!$wait_dispatch_arr) {
            return false;
        }

        //已分配师傅订单列表
        $dispatched_lists = Order::field('id,appoint_time_start,staff_id')
            ->where(['order_status'=>[OrderEnum::ORDER_STATUS_APPOINT,OrderEnum::ORDER_STATUS_SERVICE]])
            ->where('staff_id','>',0)
            ->select()
            ->toArray();
        $dispatch_arr = [];
        foreach ($dispatched_lists as $val) {
            $appoint_time = date('Y-m-d',$val['appoint_time_start']);
            $dispatch_arr[$appoint_time][] = $val['staff_id'];
        }

        foreach ($wait_dispatch_arr as $key=>$value) {
            //已分配师傅
            $dispatch_staff_ids = $dispatch_arr[$key] ?? [];
            foreach ($value as $val) {
                //可分配师傅列表
                $staff_ids = Staff::where("find_in_set({$val['goods_id']},goods_ids)")
                    ->where(['city_id'=>$val['city_id'],'status'=>DefaultEnum::SHOW])
                    ->column('id');
                $staff_ids = array_values(array_diff($staff_ids,$dispatch_staff_ids));
                if (!$staff_ids) {
                    continue;
                }

                //为订单分配师傅
                Order::update(['staff_id'=>$staff_ids[0],'is_dispatch'=>OrderEnum::DISPATCH_YES],['id'=>$val['id']]);
                array_push($dispatch_staff_ids,$staff_ids[0]);


                // 订单待确认服务通知 - 通知师傅
                event('Notice', [
                    'scene_id' =>  NoticeEnum::ORDER_WAIT_CONFIRM_NOTICE_STAFF,
                    'params' => [
                        'order_id' => $val['id'],
                        'staff_id' => $staff_ids[0]
                    ]
                ]);

                // 订单派单成功通知 - 通知平台
                $mobile = ConfigService::get('website', 'mobile');
                if (!empty($mobile)) {
                    event('Notice', [
                        'scene_id' =>  NoticeEnum::ORDER_DISPATCH_NOTICE_PLATFORM,
                        'params' => [
                            'mobile' => $mobile,
                            'staff_id' => $staff_ids[0],
                            'order_id' => $val['id']
                        ]
                    ]);
                }
            }
        }
    }
}