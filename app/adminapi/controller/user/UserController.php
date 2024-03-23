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

namespace app\adminapi\controller\user;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\user\UserLists;
use app\adminapi\logic\user\UserLogic;
use app\adminapi\validate\user\UserValidate;

class UserController extends BaseAdminController
{
    /**
     * @notes 查看用户列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/7 6:53 下午
     */
    public function lists()
    {
        return $this->dataLists(new UserLists());
    }

    /**
     * @notes 用户详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/4/21 2:29 下午
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $result = UserLogic::detail($id);
        return $this->success('',$result);
    }

    /**
     * @notes 修改用户信息
     * @return \think\response\Json
     * @author ljj
     * @date 2022/5/24 10:18 上午
     */
    public function editInfo()
    {
        $params = (new UserValidate())->post()->goCheck('editInfo');
        (new UserLogic)->editInfo($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 调整余额
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/12 11:59 上午
     */
    public function adjustBalance()
    {
        $params = (new UserValidate())->post()->goCheck('adjustBalance');
        $result = (new UserLogic)->adjustUserWallet($params);
        if(true !== $result){
            return $this->fail($result);
        }
        return $this->success('操作成功', [], 1, 1);
    }
}