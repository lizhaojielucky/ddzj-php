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


use app\common\model\order\OrderTime;
use app\common\validate\BaseValidate;

class OrderTimeValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId',
        'time' => 'number|egt:1',
        'start_time' => 'require',
        'end_time' => 'require|checkTime',
        'sort' => 'number|max:5',
        'ids' => 'require|array',
        'goods'=>'array',
    ];

    protected $message = [
        'time.number' => '可提前预约天数必须为纯数字',
        'time.egt' => '可提前预约天数必须大于或等于1',
        'start_time.require' => '请选择开始时间段',
        'end_time.require' => '请选择结束时间段',
        'sort.number' => '排序必须为纯数字',
        'sort.max' => '排序最大不能超过五位数',
        'id.require' => '参数错误',
        'ids.require' => '参数错误',
        'ids.array' => '参数结构错误',
        'goods.array' => '参数结构错误,必须是数组',
    ];

    public function sceneSetTime()
    {
        return $this->only(['time']);
    }

    public function sceneAdd()
    {
        return $this->only(['start_time','end_time','sort']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','start_time','end_time','sort']);
    }

    public function sceneDel()
    {
        return $this->only(['ids']);
    }

    public function sceneSort()
    {
        return $this->only(['id','sort'])
            ->append('sort','require');
    }


    /**
     * @notes 检验时间段
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/11 6:31 下午
     */
    public function checkTime($value,$rule,$data)
    {
        $start_time = strtotime($data['start_time']);
        $end_time = strtotime($data['end_time']);
        if ($end_time <= $start_time) {
            return '结束时间不能小于或等于开始时间';
        }

        $where[] = ['start_time','=',$data['start_time']];
        $where[] = ['end_time','=',$data['end_time']];
        if (isset($data['id'])) {
            $where[] = ['id','<>',$data['id']];
        }
        $result = OrderTime::where($where)->findOrEmpty();
        if (!$result->isEmpty()) {
            return '时间段已存在，请重新设置';
        }

        return true;
    }

    /**
     * @notes 检验时间段id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/11 6:35 下午
     */
    public function checkId($value,$rule,$data)
    {
        $result = OrderTime::where('id',$value)->findOrEmpty();
        if ($result->isEmpty()) {
            return '时间段不存在';
        }
        return true;
    }
}
