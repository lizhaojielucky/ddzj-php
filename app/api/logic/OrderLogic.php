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


use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\OrderRefundEnum;
use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\logic\OrderLogLogic;
use app\common\logic\RefundLogic;
use app\common\model\goods\Goods;
use app\common\model\order\Order;
use app\common\model\order\OrderGoods;
use app\common\model\user\User;
use app\common\model\user\UserAddress;
use think\facade\Db;

class OrderLogic extends BaseLogic
{
    /**
     * @notes 订单结算详情
     * @param $params
     * @return array|false
     * @author ljj
     * @date 2022/2/24 6:19 下午
     */
    public function settlement($params)
    {
        try {
            //获取用户信息
            $user = User::findOrEmpty($params['user_id'])->toArray();

            //设置用户地址
            $user_address = UserAddress::getUserAddress($params['user_id'], $params['address_id'] ?? 0);

            //获取服务信息
            $goods = self::getOrderGoods($params['goods']);

            // 订单金额
            $total_amount = round($goods['price'] * $goods['goods_num'],2);

            //订单应付金额
            $order_amount = $total_amount;

            //订单服务总数量
            $total_num = $goods['goods_num'];

            //订单服务总价
            $total_goods_price = round($goods['price'] * $goods['goods_num'],2);

            $result = [
                'terminal'          => $params['terminal'],
                'total_num'         => $total_num,
                'total_goods_price' => $total_goods_price,
                'total_amount'      => $total_amount,
                'order_amount'      => $order_amount,
                'user_id'           => $user['id'],
                'user_remark'       => $params['user_remark'] ?? '',
                'appoint_time_start'=> strtotime($params['appoint_time_start']),
                'appoint_time_end'  => strtotime($params['appoint_time_end']),
                'address'           => $user_address,
                'goods'             => $goods,
            ];

            return $result;

        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 获取订单服务信息
     * @param $goods
     * @return array
     * @author ljj
     * @date 2022/2/24 6:09 下午
     */
    public function getOrderGoods($goods)
    {
        $result = (new Goods())->field('id,name,unit_id,image,good_num,price')
            ->append(['unit_desc'])
            ->where(['id'=>$goods['id']])
            ->findOrEmpty()
            ->toArray();

        //服务数量
        $result['goods_num'] = $goods['goods_num'];

        return $result;
    }

    /**
     * @notes 提交订单
     * @param $params
     * @return array|false
     * @author ljj
     * @date 2022/2/25 9:40 上午
     */
    public static function submitOrder($params)
    {
        Db::startTrans();
        try {
            //收货地址
            if (empty($params['address'])) {
                throw new \Exception('请选择收货地址');
            }

            //创建订单信息
            $order = self::addOrder($params);

            //下单增加服务预约人数
            Goods::update(['order_num'=>['inc',1]],['id'=>$params['goods']['id']]);

            //订单日志
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_USER,OrderLogEnum::USER_ADD_ORDER,$order['id'],$params['user_id']);

            //提交事务
            Db::commit();
            return ['order_id' => $order['id'], 'type' => 'order'];
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 创建订单信息
     * @param $params
     * @return Order|\think\Model
     * @author ljj
     * @date 2022/2/25 9:40 上午
     */
    public static function addOrder($params)
    {
        //创建订单信息
        $order = Order::create([
            'sn'                    => generate_sn((new Order()), 'sn'),
            'user_id'               => $params['user_id'],
            'order_terminal'        => $params['terminal'],
            'goods_price'           => $params['total_goods_price'],
            'order_amount'          => $params['order_amount'],
            'total_amount'          => $params['total_amount'],
            'total_num'             => $params['total_num'],
            'user_remark'           => $params['user_remark'],
            'verification_code'     => create_number_sn((new Order()), 'verification_code',6),
            'contact'               => $params['address']['contact'],
            'mobile'                => $params['address']['mobile'],
            'province_id'           => $params['address']['province_id'],
            'city_id'               => $params['address']['city_id'],
            'district_id'           => $params['address']['district_id'],
            'address'               => $params['address']['address'],
            'lng'                   => $params['address']['longitude'],
            'lat'                   => $params['address']['latitude'],
            'appoint_time_start'    => $params['appoint_time_start'],
            'appoint_time_end'      => $params['appoint_time_end'],
            'service_num'           => $params['goods']['good_num'],
            'is_copy'               => $params['goods']['good_num'] > 1 ? OrderEnum::COPY_YES : OrderEnum::COPY_NO,
        ]);

        //创建订单服务信息
        OrderGoods::create([
            'order_id'          => $order->id,
            'goods_id'          => $params['goods']['id'],
            'goods_name'        => $params['goods']['name'],
            'unit_name'         => $params['goods']['unit_desc'],
            'goods_num'         => $params['goods']['goods_num'],
            'goods_price'       => $params['goods']['price'],
            'total_price'       => round($params['goods']['price'] * $params['goods']['goods_num'],2),
            'total_pay_price'   => round($params['goods']['price'] * $params['goods']['goods_num'],2),
            'goods_snap'        => json_encode($params['goods']),
        ]);

        return $order;
    }

    /**
     * @notes 订单详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/28 11:23 上午
     */
    public function detail($id)
    {
        $result = Order::where('id',$id)
            ->append(['appoint_time','appoint_week','door_time','order_status_desc','pay_way_desc','pay_btn','cancel_btn','del_btn','comment_btn','contact_btn','province','city','district','staff_record_confirm_lng','staff_record_confirm_lat','staff_record_confirm_distance','staff_record_verify_lng','staff_record_verify_lat','staff_record_verify_distance'])
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
     * @notes 取消订单
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2022/2/28 11:36 上午
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
                (new RefundLogic())->refund($order,$order['order_amount'],OrderRefundEnum::TYPE_USER,$params['user_id']);
            }

            //添加订单日志
            (new OrderLogLogic())->record(OrderLogEnum::TYPE_USER,OrderLogEnum::USER_CANCEL_ORDER,$params['id'],$params['user_id']);

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
     * @param $id
     * @return bool
     * @author ljj
     * @date 2022/2/28 11:50 上午
     */
    public function del($id)
    {
        Order::destroy($id);
        return true;
    }
}