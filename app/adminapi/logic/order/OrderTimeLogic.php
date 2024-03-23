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

namespace app\adminapi\logic\order;


use app\common\enum\DefaultEnum;
use app\common\logic\BaseLogic;
use app\common\model\order\OrderTime;
use app\common\service\ConfigService;

class OrderTimeLogic extends BaseLogic
{
    /**
     * @notes 设置可预约天数
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/11 6:08 下午
     */
    public function setTime($params)
    {
        ConfigService::set('order_time','time',$params['time'] ?? 7);
        return true;
    }

    /**
     * @notes 获取可预约天数
     * @return array
     * @author ljj
     * @date 2022/2/11 6:13 下午
     */
    public function getTime()
    {
        return ['time'=>ConfigService::get('order_time','time',7)];
    }

    /**
     * @notes 添加预约时间段
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/11 6:25 下午
     */
    public function add($params)
    {
        $goods = "";
        if(isset($params['goods']) && is_array($params['goods'])){
            $goods = implode(',',$params['goods']);
        }
        OrderTime::create([
            'start_time' => $params['start_time'],
            'end_time' => $params['end_time'],
            'sort' => $params['sort'] ?? DefaultEnum::SORT,
            'goods' => $goods,
        ]);

        return true;
    }

    /**
     * @notes 查看时间段详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/11 6:39 下午
     */
    public function detail($id)
    {
        return OrderTime::where('id',$id)->append(['time_desc'])->findOrEmpty()->toArray();
    }

    /**
     * @notes 编辑时间段
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/11 6:41 下午
     */
    public function edit($params)
    {
        $goods = "";
        if(isset($params['goods']) && is_array($params['goods'])){
            $goods = implode(',',$params['goods']);
        }
        OrderTime::update([
            'start_time' => $params['start_time'],
            'end_time' => $params['end_time'],
            'sort' => $params['sort'] ?: DefaultEnum::SORT,
            'goods' => $goods,
        ],['id'=>$params['id']]);

        return true;
    }

    /**
     * @notes 删除时间段
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/11 6:45 下午
     */
    public function del($params)
    {
        OrderTime::destroy($params['ids']);
        return true;
    }

    /**
     * @notes 修改排序
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/11/28 18:20
     */
    public function sort($params)
    {
        OrderTime::update([
            'sort' => $params['sort'],
        ],['id'=>$params['id']]);

        return true;
    }
}