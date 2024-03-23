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

namespace app\adminapi\logic\order;


use app\common\enum\notice\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\OrderRefundEnum;
use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\logic\OrderLogLogic;
use app\common\logic\RefundLogic;
use app\common\model\order\Order;
use app\common\model\order\OrderGoods;
use app\common\model\staff\Staff;
use think\facade\Db;

class OrderLogic extends BaseLogic
{
    /**
     * @notes 订单详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/11 3:01 下午
     */
    public function detail($id)
    {
        $result = Order::where(['id'=>$id])
            ->field('id,sn,user_id,staff_id,order_type,order_status,pay_status,pay_way,pay_time,service_num,user_remark,order_remarks,verification_code,verification_status,contact,mobile,province_id,city_id,district_id,address,create_time,appoint_time_start,appoint_time_end,start_time,finish_time,is_dispatch,lng,lat')
            ->append(['appoint_time','appoint_week','door_time','pay_status_desc','order_status_desc','pay_way_desc','verification_status_desc','cancel_btn','verification_btn','user','staff','province','city','district','staff_record_confirm_lng','staff_record_confirm_lat','staff_record_confirm_distance','staff_record_verify_lng','staff_record_verify_lat','staff_record_verify_distance'])
            ->with(['order_goods' => function($query){
                $query->field('goods_id,order_id,goods_snap,goods_name,goods_price,unit_name,goods_num,total_pay_price')->append(['goods_image'])->hidden(['goods_snap']);
            },'order_log' => function($query){
                $query->field('id,order_id,type,channel,operator_id,create_time')->append(['channel_desc','operator']);
            }])
            ->findOrEmpty()
            ->toArray();

        if ($result['order_status'] == OrderEnum::ORDER_STATUS_SERVICE) {
            $result['cancel_btn'] = 1;
        }
        return $result;
    }

    /**
     * @notes 取消订单
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2022/2/11 4:10 下午
     */
    public function cancel($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            //更新订单状态
            Order::update([
                'order_status' => OrderEnum::ORDER_STATUS_CLOSE,
                'cancel_time' => time(),
            ],['id'=>$params['id']]);

            //TODO 已支付订单原路退回金额
            $order = Order::where('id',$params['id'])->findOrEmpty()->toArray();
            if($order['pay_status'] == PayEnum::ISPAID) {
                (new RefundLogic())->refund($order,$order['order_amount'],OrderRefundEnum::TYPE_ADMIN,$params['admin_id']);
            }

            //添加订单日志
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_ADMIN,OrderLogEnum::SHOP_CANCEL_ORDER,$params['id'],$params['admin_id']);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 删除订单
     * @param $params
     * @author ljj
     * @date 2022/2/11 4:27 下午
     */
    public function del($params)
    {
        Order::destroy($params['id']);
        return true;
    }

    /**
     * @notes 商家备注
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/11 4:45 下午
     */
    public function remark($params)
    {
        Order::update(['order_remarks'=>$params['remark'] ?? ''],['id'=>$params['id']]);
        return true;
    }

    /**
     * @notes 商家备注详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/11 4:56 下午
     */
    public function remarkDetail($id)
    {
        return Order::where('id',$id)->field('order_remarks')->findOrEmpty()->toArray();
    }

    /**
     * @notes 核销订单
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2022/2/11 5:03 下午
     */
    public function verification($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            //更新订单状态
            Order::update([
                'order_status' => OrderEnum::ORDER_STATUS_FINISH,
                'verification_status' => OrderEnum::VERIFICATION,
                'finish_time' => time(),
            ],['id'=>$params['id']]);

            //添加订单日志
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_ADMIN,OrderLogEnum::SHOP_VERIFICATION,$params['id'],$params['admin_id']);

            $order = Order::where('id',$params['id'])->findOrEmpty()->toArray();

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

    public function editAppointmentTime($params)
    {
        Db::startTrans();
        try {
            Order::update([
                'appoint_time_start' => $params['appoint_time_start'],
                'appoint_time_end' => $params['appoint_time_end'],
            ],['id'=>$params['id']]);

            //添加订单日志
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_ADMIN,OrderLogEnum::SHOP_EDIT_APPOINTMENT_TIME,$params['id'],$params['admin_id']);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public function editActualPay($params)
    {
        Db::startTrans();
        try {
            Order::update([
                'order_amount' => $params['money'],
                'total_amount' => $params['money'],
            ],['id'=>$params['id']]);

            OrderGoods::update([
                'total_pay_price' => $params['money'],
            ],['order_id'=>$params['id']]);

            //添加订单日志
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_ADMIN,OrderLogEnum::SHOP_EDIT_ACTUAL_PAY,$params['id'],$params['admin_id']);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    public function copyOrder($params)
    {
        //复制当前订单
        Db::startTrans();
        try {
            $order = Order::where('id', $params['id'])->findOrEmpty()->toArray();
            $orderGoods = OrderGoods::where('order_id', $params['id'])->findOrEmpty()->toArray();
            $service_time = $params['service_time'];    //[0=>'08:00',1=>'09:00']
            $service_cycle = $params['service_cycle'];  //[0,1,2,3,4,5,6] 星期天到星期六
            $start_date = $params['start_data'];    //Y-m-d 例如：2022-08-29
            // 将开始日期转换为 DateTime 对象，以便更轻松地进行操作
            $current_date = new \DateTime($start_date);
            $service_num = $order['service_num'];
            $sn = $order['sn'];
            // 循环直到达到所需的服务预约次数
            $appoint_count = 0;
            while ($appoint_count < $service_num) {
                $current_weekday = $current_date->format('w'); // 0（星期日）至 6（星期六）
                // 检查当前星期几是否与任何服务周期匹配
                if (in_array($current_weekday, $service_cycle)) {
                    // 获取当前星期几对应的服务时间
                    // 构造预约时间
                    $appointDate = $current_date->format('Y-m-d');
                    $appointStartTime = $appointDate. ' ' .  $service_time[0];
                    $appointEndTime = $appointDate . ' ' .  $service_time[1];
                    unset($order['id']); // 移除原始数据的 ID
                    unset($order['transaction_id']);
                    unset($order['pay_time']);
                    unset($order['order_amount']);
                    unset($order['total_amount']);
                    unset($order['start_time']);
                    unset($order['finish_time']);
                    $order['sn'] = $sn.'-'.($appoint_count+1);
                    $order['verification_code'] = create_number_sn((new Order()), 'verification_code', 6);
                    $order['order_type'] = OrderEnum::ORDER_TYPE_SYSTEM;
                    $order['service_num'] = 1;
                    $order['create_time'] = time();
                    $order['update_time'] = time();
                    $order['appoint_time_start'] = strtotime($appointStartTime);
                    $order['appoint_time_end'] =  strtotime($appointEndTime);
                    $newOrder = Order::create($order);

                    unset($orderGoods['id']);
                    unset($orderGoods['total_pay_price']);
                    $orderGoods['create_time'] = time();
                    $orderGoods['update_time'] = time();
                    $orderGoods['order_id'] = $newOrder->id;
                    OrderGoods::create($orderGoods);

                    $appoint_count++;
                }
                // 转到下一天
                $current_date->modify('+1 day');

            }
            Order::update([
                'order_status'=>OrderEnum::ORDER_STATUS_FINISH,
                'is_copy'=>OrderEnum::COPY_FINISH,
            ], ['id'=>$params['id']]);
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_ADMIN,OrderLogEnum::SHOP_GENERATE_ORDER,$params['id'],$params['admin_id']);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 指派师傅
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2022/8/29 5:26 下午
     */
    public function dispatchStaff($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            $order = Order::where('id',$params['id'])->findOrEmpty()->toArray();

            //为订单指派师傅
            Order::update(['staff_id'=>$params['staff_id'],'is_dispatch'=>OrderEnum::DISPATCH_YES],['id'=>$params['id']]);

            //添加订单日志
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_ADMIN,OrderLogEnum::SHOP_DISPATCH_STAFF,$params['id'],$params['admin_id']);

            // 订单待确认服务通知 - 通知师傅
            event('Notice', [
                'scene_id' =>  NoticeEnum::ORDER_WAIT_CONFIRM_NOTICE_STAFF,
                'params' => [
                    'order_id' => $params['id'],
                    'staff_id' => $params['staff_id'],
                ]
            ]);

            if ($order['staff_id'] > 0 && $order['staff_id'] != $params['staff_id']) {
                // 平台取消派单通知 - 通知师傅
                event('Notice', [
                    'scene_id' =>  NoticeEnum::ORDER_CANCEL_DISPATCH_NOTICE_STAFF,
                    'params' => [
                        'order_id' => $params['id'],
                        'staff_id' => $order['staff_id'],
                    ]
                ]);
            }

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 师傅列表
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/8/29 6:10 下午
     */
    public function staffLists($params)
    {
        $where[] = ['status','=',YesNoEnum::YES];
        if(isset($params['name']) && $params['name']) {
            $where[] = ['name','like','%'.$params['name'].'%'];
        }
        if (isset($params['region_id']) && $params['region_id']) {
            $where[] = ['province_id|city_id|district_id','=',$params['region_id']];
        }

        $lists = Staff::where($where)
            ->field('id,name,user_id')
            ->with('user')
            ->order('id desc')
            ->select()
            ->toArray();

        return $lists;
    }
}