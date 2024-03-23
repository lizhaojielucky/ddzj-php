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

namespace app\adminapi\controller\staff;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\staff\StaffLists;
use app\adminapi\logic\staff\StaffLogic;
use app\adminapi\validate\staff\StaffValidate;

class StaffController extends BaseAdminController
{
    /**
     * @notes 查看师傅列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 11:38 上午
     */
    public function lists()
    {
        return $this->dataLists(new StaffLists());
    }

    /**
     * @notes 添加师傅
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 3:52 下午
     */
    public function add()
    {
        $params = (new StaffValidate())->post()->goCheck('add');
        (new StaffLogic())->add($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 师傅详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 4:23 下午
     */
    public function detail()
    {
        $params = (new StaffValidate())->get()->goCheck('detail');
        $result = (new StaffLogic())->detail($params['id']);
        return $this->success('获取成功',$result);
    }

    /**
     * @notes 编辑师傅
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 4:27 下午
     */
    public function edit()
    {
        $params = (new StaffValidate())->post()->goCheck('edit');
        (new StaffLogic())->edit($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 删除师傅
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 4:31 下午
     */
    public function del()
    {
        $params = (new StaffValidate())->post()->goCheck('del');
        (new StaffLogic())->del($params['id']);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 修改师傅状态
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 4:39 下午
     */
    public function status()
    {
        $params = (new StaffValidate())->post()->goCheck('status');
        (new StaffLogic())->status($params);
        return $this->success('操作成功',[],1,1);
    }
}