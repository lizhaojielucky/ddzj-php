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

namespace app\adminapi\controller\goods;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\goods\GoodsCommentLists;
use app\adminapi\logic\goods\GoodsCommentLogic;
use app\adminapi\validate\goods\GoodsCommentValidate;

class GoodsCommentController extends BaseAdminController
{
    /**
     * @notes 查看服务评价列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/9 6:44 下午
     */
    public function lists()
    {
        return $this->dataLists(new GoodsCommentLists());
    }

    /**
     * @notes 服务评价回复
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/9 7:03 下午
     */
    public function reply()
    {
        $params = (new GoodsCommentValidate())->post()->goCheck('reply');
        (new GoodsCommentLogic())->reply($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 回复详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 9:40 上午
     */
    public function detail()
    {
        $params = (new GoodsCommentValidate())->get()->goCheck('detail');
        $result = (new GoodsCommentLogic())->detail($params['id']);
        return $this->success('获取成功',$result);
    }

    /**
     * @notes 删除服务评价
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 9:42 上午
     */
    public function del()
    {
        $params = (new GoodsCommentValidate())->post()->goCheck('del');
        (new GoodsCommentLogic())->del($params['id']);
        return $this->success('操作成功',[],1,1);
    }
}