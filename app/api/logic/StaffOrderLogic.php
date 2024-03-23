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

namespace app\api\logic;


use app\common\enum\notice\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\logic\BaseLogic;
use app\common\logic\OrderLogLogic;
use app\common\model\order\Order;
use app\common\model\order\OrderStaffRecord;
use app\common\service\ConfigService;
use think\facade\Db;

class StaffOrderLogic extends BaseLogic
{
    /**
     * @notes 订单服务详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/3/1 3:24 下午
     */
    public function detail($id)
    {
        $result = Order::where('id',$id)
            ->append(['appoint_time','appoint_week','door_time','order_status_desc','pay_way_desc','confirm_service_btn','verification_btn','province','city','district','staff_record_confirm_lng','staff_record_confirm_lat','staff_record_confirm_distance','staff_record_verify_lng','staff_record_verify_lat','staff_record_verify_distance'])
            ->with(['order_goods' => function($query){
                $query->field('order_id,goods_snap,goods_name,goods_price,goods_num,unit_name')->append(['goods_image'])->hidden(['goods_snap']);
            },'staff' => function($query){
                $query->field('id,name,mobile,user_id');
            }])
            ->findOrEmpty()
            ->toArray();

        return $result;
    }


    /**
     * @notes 确认服务
     * @param $id
     * @return bool
     * @author ljj
     * @date 2022/3/1 3:43 下午
     */
    public function confirmService($params)
    {
        Order::update([
            'order_status'=>OrderEnum::ORDER_STATUS_SERVICE,
            'start_time' => time(),
        ],['id'=>$params['id']]);

        $order = Order::where('id',$params['id'])->findOrEmpty()->toArray();
        //添加订单日志
        (new OrderLogLogic())->record(OrderLogEnum::TYPE_USER,OrderLogEnum::USER_CONFIRM_SERVICE,$params['id'],$order['user_id']);

        //保存订单实付位置记录
        OrderStaffRecord::insert([
            'order_id' => $params['id'],
            'staff_id' => $order['staff_id'],
            'distance'=> $this->calculateDistance($order['lat'],$order['lng'],$params['lat'],$params['lng']),
            'lat' => $params['lat'],
            'lng' => $params['lng'],
            'type'=>0,
            'create_time' => time()
        ]);


        // 师傅确认服务通知 - 通知买家
        event('Notice', [
            'scene_id' =>  NoticeEnum::STAFF_CONFIRM_ORDER_NOTICE,
            'params' => [
                'user_id' => $order['user_id'],
                'order_id' => $order['id']
            ]
        ]);

        return true;
    }

    /**
     * @notes 订单核销
     * @param $params
     * @return Order|bool
     * @author ljj
     * @date 2022/3/1 3:59 下午
     */
    public function verification($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            $order = Order::where('verification_code',$params['verification_code'])->findOrEmpty()->toArray();

            //更新订单状态
            Order::update([
                'order_status' => OrderEnum::ORDER_STATUS_FINISH,
                'verification_status' => OrderEnum::VERIFICATION,
                'finish_time' => time(),
            ],['id'=>$order['id']]);

            //添加订单日志
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_USER,OrderLogEnum::USER_VERIFICATION,$order['id'],$params['user_id']);

            //保存订单实付位置记录
            OrderStaffRecord::insert([
                'order_id' => $order['id'],
                'staff_id' => $order['staff_id'],
                'distance'=> $this->calculateDistance($order['lat'],$order['lng'],$params['lat'],$params['lng']),
                'lat' => $params['lat'],
                'lng' => $params['lng'],
                'type'=>1,
                'create_time' => time()
            ]);

            // 订单完成通知 - 通知买家
            event('Notice', [
                'scene_id' =>  NoticeEnum::ORDER_FINISH_NOTICE,
                'params' => [
                    'user_id' => $order['user_id'],
                    'order_id' => $order['id']
                ]
            ]);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    public function calculateDistance($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6371000; // 地球半径，单位为米
        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);

        $a = sin($latDiff/2) * sin($latDiff/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDiff/2) * sin($lngDiff/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c; // 计算距离，单位为米

        return $distance;
    }
}