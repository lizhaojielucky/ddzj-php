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


use app\common\model\goods\GoodsComment;

class GoodsCommentLists extends BaseShopDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2022/2/18 11:18 上午
     */
    public function setSearch()
    {
        $where= [];
        $where[] = ['gc.goods_id','=',$this->params['goods_id'] ?? 0];
        if (!isset($this->params['id']) || $this->params['id'] == '') {
            return $where;
        }
        switch ($this->params['id']){
            case 1://好评
                $where[]= ['gc.service_comment','>',3];
                break;
            case 2://中评
                $where[]= ['gc.service_comment','=',3];
                break;
            case 3://差评
                $where[]= ['gc.service_comment','<',3];
                break;
            case 4://晒图
                $where[]= ['gci.uri','not null',''];
                break;
            default:
                break;
        }
        return $where;
    }

    /**
     * @notes 服务评价列表
     * @return array
     * @author ljj
     * @date 2022/2/18 11:18 上午
     */
    public function lists(): array
    {
        $lists = GoodsComment::alias('gc')
            ->leftjoin('goods_comment_image gci', 'gc.id = gci.comment_id')
            ->with(['goods_comment_image','user'])
            ->field('gc.id,gc.goods_id,gc.user_id,gc.service_comment,gc.comment,gc.reply,gc.create_time')
            ->append(['comment_level'])
            ->where($this->setSearch())
            ->limit($this->limitOffset, $this->limitLength)
            ->order('gc.id','desc')
            ->group('gc.id')
            ->select()
            ->toArray();


        return $lists;
    }

    /**
     * @notes 服务评价总数
     * @return int
     * @author ljj
     * @date 2022/2/18 11:23 上午
     */
    public function count(): int
    {
        return GoodsComment::alias('gc')
            ->leftjoin('goods_comment_image gci', 'gc.id = gci.comment_id')
            ->field('gc.id,gc.goods_id,gc.user_id,gc.service_comment,gc.comment,gc.reply')
            ->where($this->setSearch())
            ->count();
    }
}