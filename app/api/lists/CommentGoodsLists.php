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

namespace app\api\lists;


use app\common\enum\YesNoEnum;
use app\common\lists\ListsExtendInterface;
use app\common\model\order\OrderGoods;
use app\common\service\FileService;

class CommentGoodsLists extends BaseShopDataLists implements ListsExtendInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2022/2/18 2:25 下午
     */
    public function setSearch()
    {
        $where = [];
        $where[] = ['o.user_id', '=', $this->userId];
        $where[] = ['o.order_status', '=', 3];
        $where[] = ['og.is_comment','=',$this->params['type'] ?? 0];
        return $where;
    }

    /**
     * @notes 评价商品列表
     * @return array
     * @author ljj
     * @date 2022/2/21 5:59 下午
     */
    public function lists(): array
    {
        $lists = OrderGoods::alias('og')
            ->join('order o', 'o.id = og.order_id')
            ->field('og.id,og.goods_id,og.goods_name,og.goods_price,og.is_comment,og.unit_name,og.goods_snap')
            ->append(['goods_comment'])
            ->where($this->setSearch())
            ->limit($this->limitOffset, $this->limitLength)
            ->order('og.id','desc')
            ->group('og.id')
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            //处理商品图片路径
            $goods_image = json_decode($list['goods_snap'],true)['image'];
            $list['goods_image'] = $goods_image ? FileService::getFileUrl($goods_image) : '';
            unset($list['goods_snap']);
        }

        return $lists;
    }

    /**
     * @notes 评价商品总数
     * @return int
     * @author ljj
     * @date 2022/2/21 5:59 下午
     */
    public function count(): int
    {
        return OrderGoods::alias('og')
            ->join('order o', 'o.id = og.order_id')
            ->where($this->setSearch())
            ->group('og.id')
            ->select()
            ->count();
    }

    /**
     * @notes 评价商品数据统计
     * @return array
     * @author ljj
     * @date 2022/2/21 6:00 下午
     */
    public function extend()
    {
        $waitWhere = [
            ['o.user_id', '=', $this->userId],
            ['o.order_status', '=', 3],
            ['og.is_comment', '=', YesNoEnum::NO],
        ];
        $wait = OrderGoods::alias('og')
            ->leftJoin('order o', 'o.id = og.order_id')
            ->where($waitWhere)
            ->count();
        $finishWhere = [
            ['o.user_id', '=', $this->userId],
            ['o.order_status', '=', 3],
            ['og.is_comment', '=', YesNoEnum::YES],
        ];
        $finish = OrderGoods::alias('og')
            ->leftJoin('order o', 'o.id = og.order_id')
            ->Join('goods_comment go', 'og.id = go.order_goods_id')
            ->where($finishWhere)
            ->whereNull('go.delete_time')
            ->count();
        return [
            'wait' => $wait,
            'finish' => $finish
        ];
    }
}