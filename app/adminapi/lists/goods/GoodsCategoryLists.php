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
use app\common\model\goods\GoodsCategory;

class GoodsCategoryLists extends BaseAdminDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/4/12 10:13 上午
     */
    public function where()
    {
        $where = [];
        $category_ids = [];
        if (isset($this->params['name']) && $this->params['name'] != '') {
            $category_lists = GoodsCategory::field('id,pid')->where('name','like','%'.$this->params['name'].'%')->select()->toArray();
            if (empty($category_lists)) {
                return [];
            }
            $category_ids = array_column($category_lists,'id');
            foreach ($category_lists as $val) {
                if ($val['pid'] > 0) {
                    $category_ids[] = $val['pid'];
                }
            }
        }
        if (isset($this->params['is_show']) && $this->params['is_show'] != '') {
            $category_lists = GoodsCategory::field('id,pid')->where('is_show','=',$this->params['is_show'])->select()->toArray();
            if (empty($category_lists)) {
                return [];
            }
            $ids_arr = array_column($category_lists,'id');
            foreach ($category_lists as $val) {
                if ($val['pid'] > 0) {
                    $ids_arr[] = $val['pid'];
                }
            }
            if (!empty($category_ids)) {
                $category_ids = array_intersect($category_ids,$ids_arr);
            } else {
                $category_ids = $ids_arr;
            }
        }
        if (isset($this->params['is_recommend']) && $this->params['is_recommend'] != '') {
            $category_lists = GoodsCategory::field('id,pid')->where('is_recommend','=',$this->params['is_recommend'])->select()->toArray();
            if (empty($category_lists)) {
                return [];
            }
            $ids_arr = array_column($category_lists,'id');
            foreach ($category_lists as $val) {
                if ($val['pid'] > 0) {
                    $ids_arr[] = $val['pid'];
                }
            }
            if (!empty($category_ids)) {
                $category_ids = array_intersect($category_ids,$ids_arr);
            } else {
                $category_ids = $ids_arr;
            }
        }

        if (!empty($category_ids)) {
            $category_ids = array_unique($category_ids);
            $where[] = ['id','in',$category_ids];
        }

        return $where;
    }

    /**
     * @notes 服务分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/8 3:51 下午
     */
    public function lists(): array
    {
        $lists = (new GoodsCategory())->field('id,name,pid,level,image,sort,is_show,is_recommend,create_time')
            ->where($this->where())
            ->order(['sort'=>'desc','id'=>'asc'])
            ->append(['recommend_desc','relevance_num'])
            ->select()
            ->toArray();

        $lists = linear_to_tree($lists,'sons');

        // 分页
        $index = ($this->limitOffset -1) * $this->limitLength;
        $lists = array_slice($lists, $index, $this->limitLength);

        return $lists;
    }

    /**
     * @notes 服务分类数量
     * @return int
     * @author ljj
     * @date 2022/2/8 3:51 下午
     */
    public function count(): int
    {
        return (new GoodsCategory())->where($this->where())->where(['level'=>1])->count();
    }
}