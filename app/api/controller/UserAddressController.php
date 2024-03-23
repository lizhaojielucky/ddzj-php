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


use app\api\logic\UserAddressLogic;
use app\api\validate\UserAddressValidate;

class UserAddressController extends BaseShopController
{
    /**
     * @notes 地址列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/24 10:45 上午
     */
    public function lists()
    {
        $result = (new UserAddressLogic())->lists($this->userId);
        return $this->success('',$result);
    }

    /**
     * @notes 添加地址
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 10:52 上午
     */
    public function add()
    {
        $params = (new UserAddressValidate())->post()->goCheck('add');
        $params['user_id'] = $this->userId;
        (new UserAddressLogic())->add($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 地址详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 11:56 上午
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $result = (new UserAddressLogic())->detail($id);
        return $this->success('',$result);
    }

    /**
     * @notes 编辑地址
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 11:59 上午
     */
    public function edit()
    {
        $params = (new UserAddressValidate())->post()->goCheck('edit');
        $params['user_id'] = $this->userId;
        (new UserAddressLogic())->edit($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 设置默认地址
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 12:08 下午
     */
    public function setDefault()
    {
        $params['id'] = $this->request->post('id');
        $params['user_id'] = $this->userId;
        (new UserAddressLogic())->setDefault($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 删除地址
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 2:35 下午
     */
    public function del()
    {
        $id = $this->request->post('id');
        (new UserAddressLogic())->del($id);
        return $this->success('操作成功',[],1,1);
    }
}