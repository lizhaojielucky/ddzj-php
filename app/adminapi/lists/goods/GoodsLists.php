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

namespace app\adminapi\lists\goods;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExtendInterface;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsCategory;

class GoodsLists extends BaseAdminDataLists implements ListsExtendInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/11/24 11:00
     */
    public function where()
    {
        $where = [];
        if (isset($this->params['name']) && $this->params['name'] != '') {
            $where[] = ['name','like','%'.$this->params['name'].'%'];
        }
        if (isset($this->params['status']) && $this->params['status'] != '') {
            switch ($this->params['status']) {
                case 1://销售中
                    $where[] = ['status','=',1];
                    break;
                case 2://仓库中
                    $where[] = ['status','=',0];
                    break;
            }
        }
        if (isset($this->params['second_id']) && $this->params['second_id']) {
            $where[] = ['category_id','=',$this->params['second_id']];
        }elseif (isset($this->params['first_id']) && $this->params['first_id']) {
            $category_lists = GoodsCategory::select()->toArray();
            $category_arr = [];
            foreach ($category_lists as $item) {
                $category_arr[$item['pid']][] = $item['id'];
            }
            $ids_arr = $category_arr[$this->params['first_id']] ?? '';
            $ids = $this->params['first_id'];
            if ($ids_arr) {
                $ids = implode(',',$ids_arr).','.$this->params['first_id'];
            }

            $where[] = ['category_id','in',$ids];
        }

        return $where;
    }

    /**
     * @notes 服务列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/9 11:23 上午
     */
    public function lists(): array
    {
        $where = self::where();

        $lists = (new Goods())->field('id,name,category_id,unit_id,image,price,status,sort,good_num,order_num,create_time')
            ->order(['sort'=>'desc','id'=>'desc'])
            ->where($where)
            ->append(['category_desc','unit_desc','status_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 服务数量
     * @return int
     * @author ljj
     * @date 2022/2/9 11:24 上午
     */
    public function count(): int
    {
        $where = self::where();
        return (new Goods())->where($where)->count();
    }

    /**
     * @notes 服务数据统计
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/21 5:16 下午
     */
    public function extend(): array
    {
        $where = [];
        if (isset($this->params['name']) && $this->params['name'] != '') {
            $where[] = ['name','like','%'.$this->params['name'].'%'];
        }

        $lists = (new Goods())->where($where)
            ->select()
            ->toArray();

        $data['all_count'] = 0;
        $data['SHELVE'] = 0;
        $data['UNSHELVE'] = 0;
        foreach ($lists as $val) {
            $data['all_count'] += 1;

            if ($val['status'] == 1) {
                $data['SHELVE'] += 1;
            }
            if ($val['status'] == 0) {
                $data['UNSHELVE'] += 1;
            }
        }
        return $data;
    }
}