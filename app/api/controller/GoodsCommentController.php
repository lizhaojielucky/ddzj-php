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

namespace app\api\controller;


use app\api\lists\CommentGoodsLists;
use app\api\lists\GoodsCommentLists;
use app\api\logic\GoodsCommentLogic;
use app\api\validate\GoodsCommentValidate;

class GoodsCommentController extends BaseShopController
{
    public array $notNeedLogin = ['lists','commentCategory'];


    /**
     * @notes 服务评价列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/18 11:24 上午
     */
    public function lists()
    {
        return $this->dataLists(new GoodsCommentLists());
    }

    /**
     * @notes 服务评价分类
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/18 2:10 下午
     */
    public function commentCategory()
    {
        $params = (new GoodsCommentValidate())->get()->goCheck('CommentCategory');
        $result = (new GoodsCommentLogic())->commentCategory($params);
        return $this->success('',$result);
    }

    /**
     * @notes 评价商品列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/21 6:00 下午
     */
    public function commentGoodsLists()
    {
        return $this->dataLists(new CommentGoodsLists());
    }

    /**
     * @notes 评价服务信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/21 6:12 下午
     */
    public function commentGoodsInfo()
    {
        $params = (new GoodsCommentValidate())->goCheck('CommentGoodsInfo');
        $result = (new GoodsCommentLogic())->commentGoodsInfo($params);
        return $this->success('',$result);
    }

    /**
     * @notes 添加服务评价
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/21 6:23 下午
     */
    public function add()
    {
        $params = (new GoodsCommentValidate())->post()->goCheck('add');
        $params['user_id'] = $this->userId;
        $result = (new GoodsCommentLogic())->add($params);
        if (false === $result) {
            return $this->fail(GoodsCommentLogic::getError());
        }
        return $this->success('添加成功',[],1,1);
    }
}