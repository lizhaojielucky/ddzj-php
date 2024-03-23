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
use app\adminapi\lists\goods\GoodsLists;
use app\adminapi\logic\goods\GoodsLogic;
use app\adminapi\validate\goods\GoodsValidate;

class GoodsController extends BaseAdminController
{
    /**
     * @notes 查看服务列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/9 11:39 上午
     */
    public function lists()
    {
        return $this->dataLists(new GoodsLists());
    }

    /**
     * @notes 添加服务
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/9 3:29 下午
     */
    public function add()
    {
        $params = (new GoodsValidate())->post()->goCheck('add');
        $result = (new GoodsLogic())->add($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 查看服务详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/9 3:52 下午
     */
    public function detail()
    {
        $params = (new GoodsValidate())->get()->goCheck('detail');
        $result = (new GoodsLogic())->detail($params['id']);
        return $this->success('获取成功',$result);
    }

    /**
     * @notes 编辑服务
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/9 4:06 下午
     */
    public function edit()
    {
        $params = (new GoodsValidate())->post()->goCheck('edit');
        $result = (new GoodsLogic())->edit($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 删除服务
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/9 4:13 下午
     */
    public function del()
    {
        $params = (new GoodsValidate())->post()->goCheck('del');
        $result = (new GoodsLogic())->del($params['ids']);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 修改服务状态
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/9 4:55 下午
     */
    public function status()
    {
        $params = (new GoodsValidate())->post()->goCheck('status');
        (new GoodsLogic)->status($params);
        return $this->success('操作成功',[],1,1);
    }
}