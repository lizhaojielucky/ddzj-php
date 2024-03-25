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

namespace app\adminapi\validate\order;


use app\common\enum\OrderEnum;
use app\common\model\order\Order;
use app\common\validate\BaseValidate;

class OrderValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId',
        'staff_id' => 'require',
        'appoint_time_start'=>'require',
        'appoint_time_end'=>'require',
        'money' => 'require|float|>=:0.01',
        'service_time'=>'require|array',
        'service_cycle'=>'require|array',
        'start_data'=>'require',

    ];

    protected $message = [
        'id.require' => '参数错误',
        'staff_id.require' => '请选择师傅',
        'appoint_time_start.require' => '请选择开始时间',
        'appoint_time_start.int' => '开始时间格式错误',
        'appoint_time_end.require' => '请选择结束时间',
        'appoint_time_end.int' => '结束时间格式错误',
        'money.require' => '请输入金额',
        'money.float' => '金额格式错误',
        'money.egt' => '金额必须大于0.01',
        'appoint_start_data.require' => '请选择开始时间',
    ];

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneCancel()
    {
        return $this->only(['id'])
            ->append('id','checkCancel');
    }

    public function sceneDel()
    {
        return $this->only(['id'])
            ->append('id','checkDel');
    }

    public function sceneRemark()
    {
        return $this->only(['id']);
    }

    public function sceneRemarkDetail()
    {
        return $this->only(['id']);
    }

    public function sceneVerification()
    {
        return $this->only(['id'])
            ->append('id','checkVerification');
    }

    public function sceneDispatchStaff()
    {
        return $this->only(['id','staff_id'])
            ->append('id','checkDispatchStaff');
    }

    public function sceneEditAppointmentTime()
    {
        return $this->only(['id','appoint_time_start','appoint_time_end'])
            ->append('id','checkEditAppointmentTime');
    }

    public function sceneEditActualPay()
    {
        return $this->only(['id','money'])
            ->append('id','checkActualPay');
    }

    public function sceneCopyOrder()
    {
        return $this->only(['id','service_time','service_cycle','appoint_start_data'])
            ->append('id','checkCopyOrder');
    }

    /**
     * @notes 检验订单id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/11 11:46 上午
     */
    public function checkId($value,$rule,$data)
    {
        $result = Order::where(['id'=>$value])->findOrEmpty();
        if ($result->isEmpty()) {
            return '订单不存在';
        }
        return true;
    }

    /**
     * @notes 检验订单能否取消
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/11 3:08 下午
     */
    public function checkCancel($value,$rule,$data)
    {
        $result = Order::where('id',$value)->findOrEmpty()->toArray();
        if ($result['order_status'] > OrderEnum::ORDER_STATUS_SERVICE) {
            return '订单不允许取消';
        }

//        if ($result['order_type'] == OrderEnum::ORDER_TYPE_SYSTEM){
//            return '系统订单不允许取消';
//        }

        return true;
    }

    /**
     * @notes 检验订单能否删除
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/11 4:19 下午
     */
    public function checkDel($value,$rule,$data)
    {
        $result = Order::where('id',$value)->findOrEmpty()->toArray();
        if ($result['order_status'] != OrderEnum::ORDER_STATUS_CLOSE) {
            return '订单不允许删除';
        }
        return true;
    }

    /**
     * @notes 检验订单能否核销
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/11 5:05 下午
     */
    public function checkVerification($value,$rule,$data)
    {
        $result = Order::where('id',$value)->findOrEmpty()->toArray();
        if ($result['order_status'] != OrderEnum::ORDER_STATUS_SERVICE) {
            return '订单不允许核销';
        }
        return true;
    }

    /**
     * @notes 校验订单指派师傅
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/8/29 5:19 下午
     */
    public function checkDispatchStaff($value,$rule,$data)
    {
        $result = Order::where('id',$value)->findOrEmpty()->toArray();
        if ($result['order_status'] != OrderEnum::ORDER_STATUS_APPOINT) {
            return '订单不允许指派师傅';
        }
        return true;
    }

    public function checkEditAppointmentTime($value,$rule,$data)
    {
        $result = Order::where('id',$value)->findOrEmpty()->toArray();
        if (in_array($result['order_status'],[OrderEnum::ORDER_STATUS_SERVICE,OrderEnum::ORDER_STATUS_FINISH])) {
            return '订单不允许修改预约时间';
        }
        return true;
    }

    public function checkActualPay($value,$rule,$data)
    {
        $result = Order::where('id',$value)->findOrEmpty()->toArray();
        if ($result['order_status'] != OrderEnum::ORDER_STATUS_WAIT_PAY) {
            return '订单不允许修改实际支付金额';
        }
        return true;
    }

    public function checkCopyOrder($value,$rule,$data)
    {
        //判断$data['start_data']是否是时间格式，并且格式是YYYY-MM-DD
        if (!strtotime($data['start_data'])
            || date('Y-m-d',strtotime($data['start_data'])) != $data['start_data']) {
            return '预约开始时间格式错误';
        }
        foreach ($data['service_time'] as $item) {
            if (!strtotime($item) || date('H:i',strtotime($item)) != $item) {
                return '服务时间格式错误';
            }
        }
        foreach ($data['service_cycle'] as $cycle){
            if(!in_array($cycle,[0,1,2,3,4,5,6])){
                return '服务周期格式错误,请填写0-6的数字';
            }
        }

        $result = Order::where('id',$value)->findOrEmpty()->toArray();
        if ($result['is_copy'] != OrderEnum::COPY_YES) {
            return '订单不允许生成子订单,复制状态不正确';
        }
        if ($result['is_dispatch'] != OrderEnum::DISPATCH_YES || $result['order_status']!=OrderEnum::ORDER_STATUS_APPOINT ) {
            return '订单不允许生成子订单,未派单';
        }


        return true;
    }
}
