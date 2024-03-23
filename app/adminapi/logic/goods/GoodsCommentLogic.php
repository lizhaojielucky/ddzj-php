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


use app\common\logic\BaseLogic;
use app\common\model\goods\GoodsComment;

class GoodsCommentLogic extends BaseLogic
{
    /**
     * @notes 服务评价回复
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/9 7:02 下午
     */
    public function reply($params)
    {
        GoodsComment::update(['reply'=>$params['remark'] ?? '','status'=>1],['id'=>$params['id']]);
        return true;
    }

    /**
     * @notes 回复详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/10 9:40 上午
     */
    public function detail($id)
    {
        return GoodsComment::where(['id'=>$id])->field('id,reply')->findOrEmpty()->toArray();
    }

    /**
     * @notes 删除服务评价
     * @param $id
     * @return bool
     * @author ljj
     * @date 2022/2/10 9:41 上午
     */
    public function del($id)
    {
        return GoodsComment::destroy($id);
    }
}