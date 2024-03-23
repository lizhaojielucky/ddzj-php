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

namespace app\adminapi\controller\setting\pay;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\pay\PayConfigLogic;
use app\adminapi\validate\setting\PayConfigValidate;

class PayConfigController extends BaseAdminController
{
    /**
     * @notes 查看支付配置列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/15 6:03 下午
     */
    public function lists()
    {
        $result = (new PayConfigLogic())->lists();
        return $this->success('获取成功',$result);
    }

    /**
     * @notes 编辑支付配置
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/15 6:13 下午
     */
    public function edit()
    {
        $params = (new PayConfigValidate())->post()->goCheck('edit');
        (new PayConfigLogic())->edit($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 支付配置详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/15 6:28 下午
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $result = (new PayConfigLogic())->detail($id);
        return $this->success('获取成功',$result);
    }
}