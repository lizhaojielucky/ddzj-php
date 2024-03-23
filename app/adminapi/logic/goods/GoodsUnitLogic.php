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
use app\common\model\goods\GoodsUnit;

class GoodsUnitLogic extends BaseLogic
{
    /**
     * @notes 通用单位列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/9 3:48 下午
     */
    public function commonLists()
    {
        $lists = (new GoodsUnit())->field('id,name')
            ->order(['sort'=>'asc','id'=>'desc'])
            ->select()
            ->toArray();

        return $lists;
    }


    /**
     * @notes 添加服务单位
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/8 11:37 上午
     */
    public function add($params)
    {
        GoodsUnit::create([
            'name' => $params['name'],
            'sort' => $params['sort'] ?? DefaultEnum::SORT,
        ]);

        return true;
    }


    /**
     * @notes 查看服务单位详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/8 11:49 上午
     */
    public function detail($id)
    {
        return GoodsUnit::where('id',$id)->findOrEmpty()->toArray();
    }


    /**
     * @notes 编辑服务单位
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/8 11:58 上午
     */
    public function edit($params)
    {
        GoodsUnit::update([
            'name' => $params['name'],
            'sort' => $params['sort'] ?? DefaultEnum::SORT,
        ],['id'=>$params['id']]);

        return true;
    }


    /**
     * @notes 删除服务单位
     * @param $id
     * @return bool
     * @author ljj
     * @date 2022/2/8 12:09 下午
     */
    public function del($id)
    {
        GoodsUnit::destroy($id);
        return true;
    }
}