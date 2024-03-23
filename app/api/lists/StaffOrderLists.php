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

namespace app\api\lists;


use app\common\enum\OrderEnum;
use app\common\model\order\Order;
use app\common\model\staff\Staff;

class StaffOrderLists extends BaseShopDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2022/3/1 2:46 下午
     */
    public function where()
    {
        $where = [];
        $staff_id = Staff::where('user_id',$this->userId)->value('id');
        $where[] = ['staff_id','=',$staff_id];
        $where[] = ['service_num','=',1];
        if (isset($this->params['start_times']) && isset($this->params['end_times']) && !empty($this->params['start_times']) && !empty($this->params['end_times'])) {
            if (is_numeric($this->params['start_times']) && is_numeric($this->params['end_times'])) {
                $startTimestamp = $this->params['start_times'];
                $endTimestamp = $this->params['end_times'];
            } else {
                $startTimestamp = strtotime($this->params['start_times']);
                $endTimestamp = strtotime($this->params['end_times']);
            }
            $where[] = ['appoint_time_start', '>=', $startTimestamp];
            $where[] = ['appoint_time_start', '<=', $endTimestamp];
        }
        if (isset($this->params['order_status']) && $this->params['order_status'] != '') {
            switch ($this->params['order_status']) {
                case 1:
                    $where[] = ['order_status','=',OrderEnum::ORDER_STATUS_APPOINT];
                    break;
                case 2:
                    $where[] = ['order_status','=',OrderEnum::ORDER_STATUS_SERVICE];
                    break;
                case 3:
                    $where[] = ['order_status','=',OrderEnum::ORDER_STATUS_FINISH];
                    break;
                case 4:
                    $where[] = ['order_status','=',OrderEnum::ORDER_STATUS_CLOSE];
                    break;
            }
        }

        return $where;
    }

    /**
     * @notes 订单服务列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/3/1 3:01 下午
     */
    public function lists(): array
    {
        $lists = Order::field('id,sn,order_status,pay_status,order_amount,appoint_time_start,appoint_time_end,lng,lat,address,mobile,start_time as s_time,finish_time as f_time')
            ->order('id','asc')
            ->append(['appoint_time','appoint_week','order_status_desc','confirm_service_btn','verification_btn'])
            ->with(['order_goods' => function($query){
                $query->field('order_id,goods_snap,goods_name')->append(['goods_image'])->hidden(['goods_snap']);
            }])
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        foreach ($lists as &$list) {
            $list['service_time'] = 0;
            if(!empty($list['f_time']) && !empty($list['s_time'])) {
                $list['service_time'] = $list['f_time'] - $list['s_time'];
            }

        }
        return $lists;
    }

    /**
     * @notes 订单服务数量
     * @return int
     * @author ljj
     * @date 2022/3/1 3:12 下午
     */
    public function count(): int
    {
        return Order::where($this->where())->count();
    }
}