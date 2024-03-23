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

namespace app\adminapi\logic\goods;


use app\common\enum\DefaultEnum;
use app\common\logic\BaseLogic;
use app\common\model\goods\GoodsCategory;

class GoodsCategoryLogic extends BaseLogic
{
    /**
     * @notes 通用分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/8 6:09 下午
     */
    public function commonLists($is_son)
    {
        $where = [];
        if ($is_son) {
            $where[] = ['level','=',2];
        }
        $lists = (new GoodsCategory())->field('id,name,pid,level')
            ->where(['is_show'=>DefaultEnum::SHOW])
            ->where($where)
            ->order(['sort'=>'desc','id'=>'asc'])
            ->select()
            ->toArray();

        if (!$is_son) {
            $lists = linear_to_tree($lists,'sons');
        }

        return $lists;
    }


    /**
     * @notes 添加服务分类
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/8 5:03 下午
     */
    public function add($params)
    {
        $level = isset($params['pid']) ? (GoodsCategory::where('id',$params['pid'])->value('level') + 1) : 1;
        $is_recommend = $params['is_recommend'] ?? 0;
        if (isset($params['pid']) && $params['pid'] > 0) {
            $is_recommend = GoodsCategory::where('id',$params['pid'])->value('is_recommend');
        }
        GoodsCategory::create([
            'name' => $params['name'],
            'pid' => $params['pid'] ?? 0,
            'level' => $level,
            'image' => $params['image'] ?? '',
            'sort' => $params['sort'] ?? DefaultEnum::SORT,
            'is_show' => $params['is_show'],
            'is_recommend' => $is_recommend,
        ]);

        return true;
    }

    /**
     * @notes 查看服务分类详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/8 5:21 下午
     */
    public function detail($id)
    {
        $result = GoodsCategory::where('id',$id)->findOrEmpty()->toArray();

        $result['pid'] = $result['pid'] ?: '';

        return $result;
    }

    /**
     * @notes 编辑服务分类
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/8 6:25 下午
     */
    public function edit($params)
    {
        $level = isset($params['pid']) ? (GoodsCategory::where('id',$params['pid'])->value('level') + 1) : 1;

        GoodsCategory::update([
            'name' => $params['name'],
            'pid' => $params['pid'] ?? 0,
            'level' => $level,
            'image' => $params['image'] ?? '',
            'sort' => $params['sort'],
            'is_show' => $params['is_show'],
            'is_recommend' => $params['is_recommend'] ?? 0,
        ],['id'=>$params['id']]);

        //更新下级首页推荐
        GoodsCategory::update([
            'is_recommend' => $params['is_recommend'] ?? 0,
        ],['pid'=>$params['id']]);

        return true;
    }

    /**
     * @notes 删除服务分类
     * @param $id
     * @return bool
     * @author ljj
     * @date 2022/2/8 6:34 下午
     */
    public function del($id)
    {
        return GoodsCategory::destroy($id);
    }

    /**
     * @notes 修改服务分类状态
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/10 10:57 上午
     */
    public function status($params)
    {
        GoodsCategory::update(['is_show'=>$params['is_show']],['id'=>$params['id']]);
        return true;
    }
}