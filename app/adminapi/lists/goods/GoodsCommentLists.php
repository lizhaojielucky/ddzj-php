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
use app\common\model\goods\GoodsComment;
use app\common\service\FileService;

class GoodsCommentLists extends BaseAdminDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2022/2/9 6:00 下午
     */
    public function where()
    {
        $where = [];
        $params = $this->params;
        if (isset($params['goods_info']) && $params['goods_info'] != '') {
            $where[] = ['g.name','like','%'.$params['goods_info'].'%'];
        }
        if (isset($params['user_info']) && $params['user_info'] != '') {
            $where[] = ['u.nickname','like','%'.$params['user_info'].'%'];
        }
        if (isset($params['status']) && $params['status'] != '') {
            $where[] = ['gc.status','=',$params['status']];
        }
        if (isset($params['comment_level']) && $params['comment_level'] != '') {
            switch ($params['comment_level']){
                case 'good'://好评
                    $where[]= ['gc.service_comment', '>', 3];
                    break;
                case 'medium'://中评
                    $where[]= ['gc.service_comment', '=', 3];
                    break;
                case 'bad'://差评
                    $where[]= ['gc.service_comment', '<', 3];
                    break;
            }
        }
        if (isset($params['start_time']) && $params['start_time'] != '') {
            $where[] = ['gc.create_time','>=',strtotime($params['start_time'])];
        }
        if (isset($params['end_time']) && $params['end_time'] != '') {
            $where[] = ['gc.create_time','<=',strtotime($params['end_time'])];
        }

        return $where;
    }

    /**
     * @notes 服务评价列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/9 6:00 下午
     */
    public function lists(): array
    {
        $where = self::where();

        $lists = (new GoodsComment())->alias('gc')
            ->join('user u', 'u.id = gc.user_id')
            ->join('goods g', 'g.id = gc.goods_id')
            ->field('gc.id,gc.goods_id,gc.user_id,gc.order_goods_id,gc.service_comment,gc.comment,gc.reply,gc.status,gc.create_time,g.name as goods_name,g.image as goods_image')
            ->with(['user'])
            ->order(['gc.id'=>'desc'])
            ->where($where)
            ->append(['comment_level','status_desc','goods_comment_image'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            $list['goods_image'] = FileService::getFileUrl($list['goods_image']);
        }

        return $lists;
    }

    /**
     * @notes 服务评价总数
     * @return int
     * @author ljj
     * @date 2022/2/9 5:59 下午
     */
    public function count(): int
    {
        $where = self::where();
        return (new GoodsComment())->alias('gc')->join('user u', 'u.id = gc.user_id')->join('goods g', 'g.id = gc.goods_id')->where($where)->count();
    }
}