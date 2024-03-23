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

namespace app\adminapi\logic\staff;


use app\common\logic\BaseLogic;
use app\common\model\staff\Staff;

class StaffLogic extends BaseLogic
{
    /**
     * @notes 添加师傅
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/10 3:52 下午
     */
    public function add($params)
    {
        $goods_ids = ','.implode(',',$params['goods_ids']).',';
        Staff::create([
            'user_id' => $params['user_id'],
//            'sn' => $params['sn'],
            'name' => $params['name'],
            'sex' => $params['sex'],
            'mobile' => $params['mobile'],
            'goods_ids' => $goods_ids,
            'province_id' => $params['province_id'],
            'city_id' => $params['city_id'],
            'district_id' => $params['district_id'],
            'address' => $params['address'] ?? '',
            'longitude' => $params['longitude'] ?? 0,
            'latitude' => $params['latitude'] ?? 0,
            'status' => $params['status'],
            'is_recommend' => $params['is_recommend'],
        ]);

        return true;
    }

    /**
     * @notes 师傅详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/10 4:22 下午
     */
    public function detail($id)
    {
        $result = Staff::where(['id'=>$id])
            ->append(['user','goods','goods_ids_arr','province','city','district'])
            ->findOrEmpty()
            ->toArray();

        $result['goods_ids'] = explode(',',trim($result['goods_ids'],','));

        return $result;
    }

    /**
     * @notes 编辑师傅
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/10 4:27 下午
     */
    public function edit($params)
    {
        $goods_ids = ','.implode(',',$params['goods_ids']).',';
        Staff::update([
            'user_id' => $params['user_id'],
//            'sn' => $params['sn'],
            'name' => $params['name'],
            'sex' => $params['sex'],
            'mobile' => $params['mobile'],
            'goods_ids' => $goods_ids,
            'province_id' => $params['province_id'],
            'city_id' => $params['city_id'],
            'district_id' => $params['district_id'],
            'address' => $params['address'] ?? '',
            'longitude' => $params['longitude'] ?? 0,
            'latitude' => $params['latitude'] ?? 0,
            'status' => $params['status'],
            'is_recommend' => $params['is_recommend'],
        ],['id'=>$params['id']]);

        return true;
    }

    /**
     * @notes 删除师傅
     * @param $id
     * @return bool
     * @author ljj
     * @date 2022/2/10 4:31 下午
     */
    public function del($id)
    {
        return Staff::destroy($id);
    }

    /**
     * @notes 修改师傅状态
     * @param $params
     * @return Staff
     * @author ljj
     * @date 2022/2/10 4:39 下午
     */
    public function status($params)
    {
        return Staff::update(['status'=>$params['status']],['id'=>$params['id']]);
    }
}