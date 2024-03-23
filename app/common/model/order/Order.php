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

namespace app\common\model\order;


use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\model\BaseModel;
use app\common\model\Region;
use app\common\model\staff\Staff;
use app\common\model\user\User;
use app\common\service\ConfigService;
use think\model\concern\SoftDelete;

class Order extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    /**
     * @notes 关联用户模型
     * @return \think\model\relation\HasOne
     * @author ljj
     * @date 2022/2/10 6:36 下午
     */
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    /**
     * @notes 关联订单服务模型
     * @return \think\model\relation\HasMany
     * @author ljj
     * @date 2022/2/10 6:52 下午
     */
    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class,'order_id','id');
    }

    /**
     * @notes 关联订单日志模型
     * @return \think\model\relation\HasMany
     * @author ljj
     * @date 2022/2/10 6:53 下午
     */
    public function orderLog()
    {
        return $this->hasMany(OrderLog::class,'order_id','id');
    }

    /**
     * @notes 关联师傅模型
     * @return \think\model\relation\HasOne
     * @author ljj
     * @date 2022/2/11 12:11 下午
     */
    public function staff()
    {
        return $this->hasOne(Staff::class,'id','staff_id')
            ->append(['user_image']);
    }

    /**
     * @notes 预约时间
     * @param $value
     * @param $data
     * @return false|string
     * @author ljj
     * @date 2022/2/11 10:11 上午
     */
    public function getAppointTimeAttr($value,$data)
    {
        return date('Y-m-d',$data['appoint_time_start']);
    }

    /**
     * @notes 预约日期
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2022/2/11 10:09 上午
     */
    public function getAppointWeekAttr($value,$data)
    {
        $weekarray = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'];
        return $weekarray[date("w",$data['appoint_time_start'])];
    }

    /**
     * @notes 上门时间
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2022/2/11 10:54 上午
     */
    public function getDoorTimeAttr($value,$data)
    {
        $appoint_time_start = date('H:i',$data['appoint_time_start']);
        $appoint_time_end = date('H:i',$data['appoint_time_end']);
        return $appoint_time_start.'-'.$appoint_time_end;
    }

    /**
     * @notes 支付状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 11:08 上午
     */
    public function getPayStatusDescAttr($value,$data)
    {
        return PayEnum::getPayStatusDesc($data['pay_status']);
    }

    /**
     * @notes 支付方式
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 12:01 下午
     */
    public function getPayWayDescAttr($value,$data)
    {
        return PayEnum::getPayTypeDesc($data['pay_way']);
    }

    /**
     * @notes 订单状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 11:59 上午
     */
    public function getOrderStatusDescAttr($value,$data)
    {
        return OrderEnum::getOrderStatusDesc($data['order_status']);
    }

    /**
     * @notes 核销状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/2/11 12:02 下午
     */
    public function getVerificationStatusDescAttr($value,$data)
    {
        return OrderEnum::getVerificationStatusDesc($data['verification_status']);
    }

    /**
     * @notes 取消按钮
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2022/2/11 12:08 下午
     */
    public function getCancelBtnAttr($value,$data)
    {
        $btn = YesNoEnum::NO;
        //待支付或预约中的订单可以取消
        if ($data['order_status'] == OrderEnum::ORDER_STATUS_WAIT_PAY || $data['order_status'] == OrderEnum::ORDER_STATUS_APPOINT) {
            $btn = YesNoEnum::YES;
        }

        return $btn;
    }

    /**
     * @notes 删除按钮
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2022/2/11 2:57 下午
     */
    public function getDelBtnAttr($value,$data)
    {
        $btn = YesNoEnum::NO;
        //已关闭的订单可以删除
        if ($data['order_status'] == OrderEnum::ORDER_STATUS_CLOSE) {
            $btn = YesNoEnum::YES;
        }

        return $btn;
    }

    /**
     * @notes 核销按钮
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2022/2/11 2:59 下午
     */
    public function getVerificationBtnAttr($value,$data)
    {
        $btn = YesNoEnum::NO;
        //服务中的订单可以核销
        if ($data['order_status'] == OrderEnum::ORDER_STATUS_SERVICE) {
            $btn = YesNoEnum::YES;
        }

        return $btn;
    }

    /**
     * @notes 支付按钮
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2022/2/28 9:48 上午
     */
    public function getPayBtnAttr($value,$data)
    {
        $btn = YesNoEnum::NO;
        if ($data['order_status'] == OrderEnum::ORDER_STATUS_WAIT_PAY && $data['pay_status'] == PayEnum::UNPAID) {
            $btn = YesNoEnum::YES;
        }

        return $btn;
    }

    /**
     * @notes 评价按钮
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2022/2/28 9:52 上午
     */
    public function getCommentBtnAttr($value,$data)
    {
        $btn = YesNoEnum::NO;
        if ($data['order_status'] == OrderEnum::ORDER_STATUS_FINISH) {
            $is_comment = OrderGoods::where(['order_id'=>$data['id']])->value('is_comment');
            if ($is_comment == YesNoEnum::NO) {
                $btn = YesNoEnum::YES;
            }
        }

        return $btn;
    }

    /**
     * @notes 支付时间
     * @param $value
     * @param $data
     * @author ljj
     * @date 2022/2/28 11:02 上午
     */
    public function getPayTimeAttr($value,$data)
    {
        return $value ? date('Y-m-d H:i:s',$value) : '-';
    }

    /**
     * @notes 完成时间
     * @param $value
     * @param $data
     * @author ljj
     * @date 2022/2/28 11:02 上午
     */
    public function getFinishTimeAttr($value,$data)
    {
        return $value ? date('Y-m-d H:i:s',$value) : '-';
    }

    public function getStartTimeAttr($value,$data)
    {
        return $value ? date('Y-m-d H:i:s',$value) : '-';
    }

    /**
     * @notes 确认服务按钮
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2022/3/1 3:07 下午
     */
    public function getConfirmServiceBtnAttr($value,$data)
    {
        $btn = YesNoEnum::NO;
        if ($data['order_status'] == OrderEnum::ORDER_STATUS_APPOINT) {
            $btn = YesNoEnum::YES;
        }

        return $btn;
    }

    /**
     * @notes 未支付订单自动取消时间
     * @param $value
     * @param $data
     * @return float|int|string
     * @author ljj
     * @date 2022/3/15 4:28 下午
     */
    public function getOrderCancelTimeAttr($value, $data)
    {
        $end_time = 0;
        $is_cancel = ConfigService::get('transaction', 'cancel_unpaid_orders',1);
        if ($data['order_status'] == 0 && $data['pay_status'] == 0 && $is_cancel == 1) {
            $order_cancel_time = ConfigService::get('transaction', 'cancel_unpaid_orders_times',30);
            $end_time = $data['create_time'] + $order_cancel_time * 60;
        }
        return $end_time;
    }

    /**
     * @notes 联系师傅按钮
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2022/3/16 3:20 下午
     */
    public function getContactBtnAttr($value,$data)
    {
        $btn = YesNoEnum::NO;
        if ($data['order_status'] == OrderEnum::ORDER_STATUS_SERVICE || $data['order_status'] == OrderEnum::ORDER_STATUS_FINISH) {
            $btn = YesNoEnum::YES;
        }

        return $btn;
    }

    /**
     * @notes 省
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2022/4/6 6:57 下午
     */
    public function getProvinceAttr($value,$data)
    {
        return Region::where(['id'=>$data['province_id']])->value('name');
    }

    /**
     * @notes 市
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2022/4/6 7:02 下午
     */
    public function getCityAttr($value,$data)
    {
        return Region::where(['id'=>$data['city_id']])->value('name');
    }

    /**
     * @notes 区
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2022/4/6 7:02 下午
     */
    public function getDistrictAttr($value,$data)
    {
        return Region::where(['id'=>$data['district_id']])->value('name');
    }


    /**
     * @notes 派单状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/8/29 4:57 下午
     */
    public function getDispatchDescAttr($value,$data)
    {
        return OrderEnum::getDispatchDesc($data['is_dispatch']);
    }

    public function getStaffRecordConfirmLngAttr($value,$data)
    {
        $lng =  OrderStaffRecord::where(['order_id'=>$data['id'],'type'=>0])->value('lng');
        return $lng;
    }

    public function getStaffRecordConfirmLatAttr($value,$data)
    {
        return OrderStaffRecord::where(['order_id'=>$data['id'],'type'=>0])->value('lat');
    }

    public function getStaffRecordConfirmDistanceAttr($value,$data)
    {
        return OrderStaffRecord::where(['order_id'=>$data['id'],'type'=>0])->value('distance');
    }


    public function getStaffRecordVerifyLngAttr($value,$data)
    {
        return OrderStaffRecord::where(['order_id'=>$data['id'],'type'=>1])->value('lng');
    }

    public function getStaffRecordVerifyLatAttr($value,$data)
    {
        return OrderStaffRecord::where(['order_id'=>$data['id'],'type'=>1])->value('lat');
    }

    public function getStaffRecordVerifyDistanceAttr($value,$data)
    {
        return OrderStaffRecord::where(['order_id'=>$data['id'],'type'=>1])->value('distance');
    }

}