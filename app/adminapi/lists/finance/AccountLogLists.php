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

namespace app\adminapi\lists\finance;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\AccountLogEnum;
use app\common\lists\ListsExcelInterface;
use app\common\model\AccountLog;
use app\common\service\FileService;

class AccountLogLists extends BaseAdminDataLists implements ListsExcelInterface
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
        if (isset($this->params['change_object']) && $this->params['change_object'] != '') {
            $where[] = ['al.change_object','=',$this->params['change_object']];
        }else {
            $where[] = ['al.change_object','=',AccountLogEnum::MONEY];
        }
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.mobile|u.nickname|u.account','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['change_type']) && $this->params['change_type'] != '') {
            $where[] = ['al.change_type','=',$this->params['change_type']];
        }
        // 开始时间
        if(isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['al.create_time', '>=', strtotime($this->params['start_time'])];
        }
        // 结束时间
        if(isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['al.create_time', '<=', strtotime($this->params['end_time'])];
        }
        if (isset($this->params['order_sn']) && $this->params['order_sn'] != '') {
            $where[] = ['al.association_sn','=',$this->params['order_sn']];
        }

        return $where;
    }

    /**
     * @notes 账户流水记录列表
     * @return array
     * @author ljj
     * @date 2022/12/2 5:32 下午
     */
    public function lists(): array
    {
        $lists = AccountLog::alias('al')
            ->join('user u', 'u.id = al.user_id')
            ->field('al.id,u.sn as user_sn,u.avatar,u.nickname,u.mobile,al.change_amount,al.left_amount,al.action,al.change_type,al.association_sn,al.create_time')
            ->append(['change_type_desc'])
            ->where(self::where())
            ->limit($this->limitOffset,$this->limitLength)
            ->order('al.id','desc')
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            $list['avatar'] = empty($list['avatar']) ? '' : FileService::getFileUrl($list['avatar']);
        }

        return $lists;
    }

    /**
     * @notes 账户流水记录数量
     * @return int
     * @author ljj
     * @date 2022/12/2 5:32 下午
     */
    public function count(): int
    {
        return AccountLog::alias('al')
            ->join('user u', 'u.id = al.user_id')
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
            'user_sn' => '用户编号',
            'nickname' => '用户昵称',
            'change_amount' => '变动金额',
            'left_amount' => '剩余金额',
            'change_type_desc' => '变动类型',
            'association_sn' => '来源单号',
            'create_time' => '记录时间',
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
        return '账户流水记录列表';
    }
}