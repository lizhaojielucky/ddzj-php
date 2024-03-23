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

namespace app\adminapi\controller\order;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\order\OrderTimeLists;
use app\adminapi\logic\order\OrderTimeLogic;
use app\adminapi\validate\order\OrderTimeValidate;

class OrderTimeController extends BaseAdminController
{
    /**
     * @notes 查看预约时间段列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/11 5:55 下午
     */
    public function lists()
    {
        return $this->dataLists(new OrderTimeLists());
    }

    /**
     * @notes 设置可预约天数
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/11 6:08 下午
     */
    public function setTime()
    {
        $params = (new OrderTimeValidate())->post()->goCheck('setTime');
        (new OrderTimeLogic())->setTime($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 获取可预约天数
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/11 6:13 下午
     */
    public function getTime()
    {
        $result = (new OrderTimeLogic())->getTime();
        return $this->success('获取成功',$result);
    }

    /**
     * @notes 添加预约时间段
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/11 6:25 下午
     */
    public function add()
    {
        $params = (new OrderTimeValidate())->post()->goCheck('add');
        (new OrderTimeLogic())->add($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 查看时间段详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/11 6:40 下午
     */
    public function detail()
    {
        $params = (new OrderTimeValidate())->get()->goCheck('detail');
        $result = (new OrderTimeLogic())->detail($params['id']);
        return $this->success('获取成功',$result);
    }

    /**
     * @notes 编辑时间段
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/11 6:41 下午
     */
    public function edit()
    {
        $params = (new OrderTimeValidate())->post()->goCheck('edit');
        (new OrderTimeLogic())->edit($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 删除时间段
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/11 6:45 下午
     */
    public function del()
    {
        $params = (new OrderTimeValidate())->post()->goCheck('del');
        (new OrderTimeLogic())->del($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 修改排序
     * @return \think\response\Json
     * @author ljj
     * @date 2022/11/28 18:20
     */
    public function sort()
    {
        $params = (new OrderTimeValidate())->post()->goCheck('sort');
        (new OrderTimeLogic())->sort($params);
        return $this->success('操作成功',[],1,1);
    }
}