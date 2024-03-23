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


use app\api\lists\GoodsLists;
use app\api\logic\GoodsLogic;
use app\api\validate\GoodsValidate;

class GoodsController extends BaseShopController
{
    public array $notNeedLogin = ['lists','detail'];


    /**
     * @notes 服务列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/17 5:51 下午
     */
    public function lists()
    {
        return $this->dataLists(new GoodsLists());
    }

    /**
     * @notes 服务详情
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/18 10:40 上午
     */
    public function detail()
    {
        $params = (new GoodsValidate())->get()->goCheck('detail');
        $params['user_id'] = $this->userId;
        $result = (new GoodsLogic())->detail($params);
        return $this->success('获取成功',$result);
    }

    /**
     * @notes 预约上门时间
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/3/11 2:32 下午
     */
    public function appointTime()
    {
        $params = (new GoodsValidate())->get()->goCheck('appointTime');
        $result = (new GoodsLogic())->appointTime($params);
        return $this->success('获取成功',$result);
    }

    /**
     * @notes 收藏服务
     * @return \think\response\Json
     * @author ljj
     * @date 2022/3/16 4:14 下午
     */
    public function collect()
    {
        $params = (new GoodsValidate())->post()->goCheck('collect');
        $params['user_id'] = $this->userId;
        (new GoodsLogic())->collect($params);
        return $this->success('操作成功',[],1,1);
    }
}