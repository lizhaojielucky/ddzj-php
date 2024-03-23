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
use app\adminapi\lists\goods\GoodsCategoryLists;
use app\adminapi\logic\goods\GoodsCategoryLogic;
use app\adminapi\validate\goods\GoodsCategoryValidate;

class GoodsCategoryController extends BaseAdminController
{
    /**
     * @notes 查看服务分类列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/8 3:52 下午
     */
    public function lists()
    {
        return $this->dataLists(new GoodsCategoryLists());
    }


    /**
     * @notes 通用分类列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/8 6:10 下午
     */
    public function commonLists()
    {
        $is_son = $this->request->get('is_son',0);
        $result = (new GoodsCategoryLogic())->commonLists($is_son);
        return $this->success('获取成功',$result);
    }


    /**
     * @notes 添加服务分类
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/8 5:03 下午
     */
    public function add()
    {
        $params = (new GoodsCategoryValidate())->post()->goCheck('add');
        (new GoodsCategoryLogic())->add($params);
        return $this->success('操作成功', [],1,1);
    }


    /**
     * @notes 查看服务分类详情
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/8 5:21 下午
     */
    public function detail()
    {
        $params = (new GoodsCategoryValidate())->get()->goCheck('detail');
        $result = (new GoodsCategoryLogic())->detail($params['id']);
        return $this->success('获取成功',$result);
    }


    /**
     * @notes 编辑服务分类
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/8 6:26 下午
     */
    public function edit()
    {
        $params = (new GoodsCategoryValidate())->post()->goCheck('edit');
        (new GoodsCategoryLogic())->edit($params);
        return $this->success('操作成功',[],1,1);
    }


    /**
     * @notes 删除服务分类
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/8 6:34 下午
     */
    public function del()
    {
        $params = (new GoodsCategoryValidate())->post()->goCheck('del');
        (new GoodsCategoryLogic())->del($params['id']);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 修改服务分类状态
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/10 10:57 上午
     */
    public function status()
    {
        $params = (new GoodsCategoryValidate())->post()->goCheck('status');
        (new GoodsCategoryLogic())->status($params);
        return $this->success('操作成功',[],1,1);
    }
}