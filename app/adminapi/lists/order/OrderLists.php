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

namespace app\adminapi\lists\order;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\OrderEnum;
use app\common\lists\ListsExtendInterface;
use app\common\model\order\Order;

class OrderLists extends BaseAdminDataLists implements ListsExtendInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2022/2/10 6:14 下午
     */
    public function where()
    {
        $where = [];
        $params = $this->params;
        if (isset($params['order_info']) && $params['order_info'] != '') {
            $where[] = ['o.sn','like','%'.$params['order_info'].'%'];
        }
        if (isset($params['user_info']) && $params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname|u.account','like','%'.$params['user_info'].'%'];
        }
        if (isset($params['goods_info']) && $params['goods_info'] != '') {
            $where[] = ['g.name','like','%'.$params['goods_info'].'%'];
        }
        if (isset($params['pay_status']) && $params['pay_status'] != '') {
            $where[] = ['o.pay_status','=',$params['pay_status']];
        }
        if (isset($params['start_time']) && $params['start_time'] != '') {
            $where[] = ['o.create_time','>=',strtotime($params['start_time'])];
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $where[] = ['o.create_time','<=',strtotime($params['end_time'])];
        }
        if (isset($params['staff_info']) && $params['staff_info'] != '') {
            $where[] = ['s.name','like','%'.$params['staff_info'].'%'];
        }
        if (isset($params['is_dispatch']) && $params['is_dispatch'] != '') {
            $where[] = ['o.is_dispatch','=',$params['is_dispatch']];
        }
        if (isset($params['order_status']) && $params['order_status'] != '') {
            switch ($params['order_status']) {
                case 1:
                    $where[] = ['o.order_status','=',0];
                    break;
                case 2:
                    $where[] = ['o.order_status','=',1];
                    break;
                case 3:
                    $where[] = ['o.order_status','=',2];
                    break;
                case 4:
                    $where[] = ['o.order_status','=',3];
                    break;
                case 5:
                    $where[] = ['o.order_status','=',4];
                    break;
            }
        }
        if (isset($params['service_num']) && $params['service_num'] != '') {
            switch ($params['service_num']) {
                case 1:
                    $where[] = ['o.service_num','=',1];
                    break;
                default:
                    $where[] = ['o.service_num','>',1];
            }
        }
        if (isset($params['order_type']) && $params['order_type'] != '') {
            $where[] = ['o.order_type','=',$params['order_type']];
        }

        return $where;
    }

    /**
     * @notes 订单列表
     * @return array
     * @author ljj
     * @date 2022/2/10 6:19 下午
     */
    public function lists(): array
    {
        $where = self::where();

        $lists = (new Order())->alias('o')
            ->leftjoin('user u', 'u.id = o.user_id')
            ->leftjoin('order_goods og', 'og.order_id = o.id')
            ->leftjoin('goods g', 'g.id = og.goods_id')
            ->leftjoin('staff s', 's.id = o.staff_id')
            ->field('o.id,o.sn,o.user_id,o.order_type,o.staff_id,o.order_status,o.pay_status,o.service_num,o.order_amount,o.total_num,o.contact,o.appoint_time_start,o.appoint_time_end,is_dispatch,o.start_time as s_time,o.finish_time as f_time')
            ->with(['order_goods' => function($query){
                $query->field('goods_id,order_id,goods_snap,goods_name,goods_price,unit_name')->append(['goods_image'])->hidden(['goods_snap']);
            },'user' => function($query){
                $query->field('id,sn,nickname,avatar,mobile,account');
            }])
            ->where($where)
            ->order(['o.id'=>'desc'])
            ->append(['appoint_time','appoint_week','door_time','pay_status_desc','order_status_desc','cancel_btn','del_btn','verification_btn','dispatch_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->group('o.id')
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            if ($list['order_status'] == OrderEnum::ORDER_STATUS_SERVICE) {
                $list['cancel_btn'] = 1;
            }
            //计算出完成时间和开始时间的具体服务时长
            $list['service_time'] = 0;
            if(!empty($list['f_time']) && !empty($list['s_time'])) {
                $list['service_time'] = $list['f_time'] - $list['s_time'];
            }

        }

        return $lists;
    }

    /**
     * @notes 订单总数
     * @return int
     * @author ljj
     * @date 2022/2/10 6:19 下午
     */
    public function count(): int
    {
        $where = self::where();

        return (new Order())->alias('o')
            ->leftjoin('user u', 'u.id = o.user_id')
            ->leftjoin('order_goods og', 'og.order_id = o.id')
            ->leftjoin('goods g', 'g.id = og.goods_id')
            ->leftjoin('staff s', 's.id = o.staff_id')
            ->group('o.id')
            ->where($where)
            ->count();
    }

    /**
     * @notes 订单数据统计
     * @return array
     * @author ljj
     * @date 2022/2/15 11:07 上午
     */
    public function extend(): array
    {
        $where = self::where();
        foreach ($where as $key=>$val) {
            if ($val[0] == 'o.order_status') {
                unset($where[$key]);
            }
        }

        $lists = (new Order())->alias('o')
            ->leftjoin('user u', 'u.id = o.user_id')
            ->leftjoin('order_goods og', 'og.order_id = o.id')
            ->leftjoin('goods g', 'g.id = og.goods_id')
            ->leftjoin('staff s', 's.id = o.staff_id')
            ->where($where)
            ->group('o.id')
            ->select()
            ->toArray();

        $data['all_count'] = 0;
        $data['wait_pay_count'] = 0;
        $data['appoint_count'] = 0;
        $data['service_count'] = 0;
        $data['finish_count'] = 0;
        $data['close_count'] = 0;
        foreach ($lists as $val) {
            $data['all_count'] += 1;

            if ($val['order_status'] == 0) {
                $data['wait_pay_count'] += 1;
            }
            if ($val['order_status'] == 1) {
                $data['appoint_count'] += 1;
            }
            if ($val['order_status'] == 2) {
                $data['service_count'] += 1;
            }
            if ($val['order_status'] == 3) {
                $data['finish_count'] += 1;
            }
            if ($val['order_status'] == 4) {
                $data['close_count'] += 1;
            }
        }
        return $data;
    }
}