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

namespace app\api\logic;


use app\common\enum\DefaultEnum;
use app\common\logic\BaseLogic;
use app\common\model\goods\Goods;
use app\common\model\staff\Staff;

class StaffLogic extends BaseLogic
{
    /**
     * @notes 师傅详情
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/23 6:34 下午
     */
    public function detail($id)
    {
        $result = Staff::where(['id'=>$id])
            ->field('id,user_id,name,mobile,goods_ids,province_id,city_id,district_id,address,create_time')
            ->append(['user_image','province','city','district'])
            ->findOrEmpty()
            ->toArray();

        if ($result) {
            $goods_ids = explode(',',trim($result['goods_ids'],','));
            $result['goods'] = Goods::where(['id'=>$goods_ids,'status'=>DefaultEnum::SHOW])
                ->field('id,name,unit_id,image,price')
                ->append(['unit_desc'])
                ->select()->toArray();
        }

        return $result;
    }
}