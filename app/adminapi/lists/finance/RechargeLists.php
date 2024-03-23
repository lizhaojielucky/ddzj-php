<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\adminapi\lists\finance;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\model\RechargeOrder;
use app\common\service\FileService;

class RechargeLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2022/12/2 5:30 下午
     */
    public function where()
    {
        $where = [];
        if (isset($this->params['sn']) && $this->params['sn'] != '') {
            $where[] = ['ro.sn','=',$this->params['sn']];
        }
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.mobile|u.nickname|u.account','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['pay_way']) && $this->params['pay_way'] != '') {
            $where[] = ['ro.pay_way','=',$this->params['pay_way']];
        }
        if (isset($this->params['pay_status']) && $this->params['pay_status'] != '') {
            $where[] = ['ro.pay_status','=',$this->params['pay_status']];
        }
        // 开始时间
        if(isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['ro.create_time', '>=', strtotime($this->params['start_time'])];
        }
        // 结束时间
        if(isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['ro.create_time', '<=', strtotime($this->params['end_time'])];
        }

        return $where;
    }

    /**
     * @notes 充值明细列表
     * @return array
     * @author ljj
     * @date 2022/12/2 6:43 下午
     */
    public function lists(): array
    {
        $lists = RechargeOrder::alias('ro')
            ->join('user u', 'u.id = ro.user_id')
            ->field('ro.id,u.avatar,u.nickname,ro.sn,ro.order_amount,ro.pay_way,ro.pay_status,ro.pay_time,ro.create_time')
            ->append(['pay_way_desc','pay_status_desc'])
            ->where(self::where())
            ->limit($this->limitOffset,$this->limitLength)
            ->order('ro.id','desc')
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            $list['avatar'] = empty($list['avatar']) ? '' : FileService::getFileUrl($list['avatar']);
        }

        return $lists;
    }

    /**
     * @notes 充值明细数量
     * @return int
     * @author ljj
     * @date 2022/12/2 6:43 下午
     */
    public function count(): int
    {
        return RechargeOrder::alias('ro')
            ->join('user u', 'u.id = ro.user_id')
            ->where(self::where())
            ->count();
    }

    /**
     * @notes 导出字段
     * @return string[]
     * @author ljj
     * @date 2023/4/12 2:40 下午
     */
    public function setExcelFields(): array
    {
        return [
            // '数据库字段名(支持别名) => 'Excel表字段名'
            'nickname' => '用户昵称',
            'sn' => '充值单号',
            'order_amount' => '充值金额',
            'pay_way_desc' => '支付方式',
            'pay_status_desc' => '支付状态',
            'pay_time' => '支付时间',
            'create_time' => '提交时间',
        ];
    }

    /**
     * @notes 导出表名
     * @return string
     * @author ljj
     * @date 2023/4/12 2:40 下午
     */
    public function setFileName(): string
    {
        return '充值明细列表';
    }
}