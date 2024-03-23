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

namespace app\adminapi\lists\staff;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\staff\Staff;

class StaffLists extends BaseAdminDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2022/2/10 11:23 上午
     */
    public function where(): array
    {
        $where = [];
        if (isset($this->params['staff_info']) && $this->params['staff_info'] != '') {
            $where[] = ['s.name|s.mobile','like','%'.$this->params['staff_info'].'%'];
        }
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.nickname|u.mobile|u.account','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['s.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['s.create_time','<=',strtotime($this->params['end_time'])];
        }
        if (isset($this->params['region_id']) && $this->params['region_id'] != '') {
            $where[] = ['s.province_id|s.city_id|s.district_id','=',$this->params['region_id']];
        }
        if (isset($this->params['is_recommend']) && $this->params['is_recommend'] != '') {
            $where[] = ['s.is_recommend','=',$this->params['is_recommend']];
        }
        if (isset($this->params['status']) && $this->params['status'] != '') {
            $where[] = ['s.status','=',$this->params['status']];
        }
        return $where;
    }

    /**
     * @notes 师傅列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/10 11:28 上午
     */
    public function lists(): array
    {
        $where = self::where();

        $lists = (new Staff())->alias('s')
            ->join('user u', 'u.id = s.user_id')
            ->field('s.id,s.user_id,s.name,s.sex,s.mobile,s.goods_ids,s.status,s.is_recommend,s.create_time,s.province_id,s.city_id,s.district_id')
            ->order('s.id', 'desc')
            ->where($where)
            ->append(['recommend_desc','sex_desc','goods_name','user','province','city','district'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 师傅总数
     * @return int
     * @author ljj
     * @date 2022/2/10 11:29 上午
     */
    public function count(): int
    {
        return (new Staff())->alias('s')
            ->join('user u', 'u.id = s.user_id')
            ->where(self::where())
            ->count();
    }
}