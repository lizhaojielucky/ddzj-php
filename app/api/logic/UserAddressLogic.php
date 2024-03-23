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


use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\user\UserAddress;

class UserAddressLogic extends BaseLogic
{
    /**
     * @notes 地址列表
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/24 10:45 上午
     */
    public function lists($user_id)
    {
        $lists = UserAddress::field('id,contact,mobile,province_id,city_id,district_id,address,is_default')
            ->order(['is_default'=>'desc','id'=>'desc'])
            ->append(['province','city','district'])
            ->where(['user_id'=>$user_id])
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 添加地址
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/24 10:51 上午
     */
    public function add($params)
    {
        if (isset($params['is_default']) && $params['is_default'] == YesNoEnum::YES) {
            UserAddress::where(['user_id' => $params['user_id']])->update(['is_default' => YesNoEnum::NO]);
        } else {
            $isFirst = UserAddress::where(['user_id' => $params['user_id']])->findOrEmpty();
            if ($isFirst->isEmpty()) {
                $params['is_default'] = YesNoEnum::YES;
            }
        }

        UserAddress::create([
            'user_id' => $params['user_id'],
            'contact' => $params['contact'],
            'mobile' => $params['mobile'],
            'province_id' => $params['province_id'],
            'city_id' => $params['city_id'],
            'district_id' => $params['district_id'],
            'address' => $params['address'] ?? '',
            'longitude' => $params['longitude'] ?? 0,
            'latitude' => $params['latitude'] ?? 0,
            'is_default' => $params['is_default'] ?? 0,
        ]);

        return true;
    }

    /**
     * @notes 地址详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/24 11:55 上午
     */
    public function detail($id)
    {
        return UserAddress::where(['id'=>$id])->append(['province','city','district'])->findOrEmpty()->toArray();
    }

    /**
     * @notes 编辑地址
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/24 11:59 上午
     */
    public function edit($params)
    {
        if (isset($params['is_default']) && $params['is_default'] == YesNoEnum::YES) {
            UserAddress::where(['user_id' => $params['user_id']])->update(['is_default' => YesNoEnum::NO]);
        }

        UserAddress::update([
            'contact' => $params['contact'],
            'mobile' => $params['mobile'],
            'province_id' => $params['province_id'],
            'city_id' => $params['city_id'],
            'district_id' => $params['district_id'],
            'address' => $params['address'] ?? '',
            'longitude' => $params['longitude'] ?? 0,
            'latitude' => $params['latitude'] ?? 0,
            'is_default' => $params['is_default'] ?? 0,
        ],['id'=>$params['id'],'user_id'=>$params['user_id']]);

        return true;
    }

    /**
     * @notes 设置默认地址
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/24 12:08 下午
     */
    public function setDefault($params)
    {
        UserAddress::where(['user_id' => $params['user_id']])->update(['is_default' => YesNoEnum::NO]);

        UserAddress::update(['is_default' => YesNoEnum::YES],['id'=>$params['id'],'user_id'=>$params['user_id']]);

        return true;
    }

    /**
     * @notes 删除地址
     * @param $id
     * @return bool
     * @author ljj
     * @date 2022/2/24 2:35 下午
     */
    public function del($id)
    {
        UserAddress::destroy($id);
        return true;
    }
}