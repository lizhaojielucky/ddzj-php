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

namespace app\common\model\user;


use app\common\model\BaseModel;
use app\common\model\Region;
use think\model\concern\SoftDelete;

class UserAddress extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    /**
     * @notes 获取用户地址
     * @param $user_id
     * @param int $address_id
     * @return array
     * @author ljj
     * @date 2022/2/24 5:55 下午
     */
    public static function getUserAddress($user_id,$address_id = 0)
    {
        $model = new self;
        if ($address_id) {
            $result = $model->where(['id' => $address_id])->append(['province','city','district'])->findOrEmpty()->toArray();
        }else {
            $result = $model->where(['user_id' => $user_id])
                ->append(['province','city','district'])
                ->where('is_default', 1)
                ->findOrEmpty()
                ->toArray();
        }

        return $result;
    }

    /**
     * @notes 省
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2022/4/6 6:57 下午
     */
    public function getProvinceAttr($value,$data)
    {
        return Region::where(['id'=>$data['province_id']])->value('name');
    }

    /**
     * @notes 市
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2022/4/6 7:02 下午
     */
    public function getCityAttr($value,$data)
    {
        return Region::where(['id'=>$data['city_id']])->value('name');
    }

    /**
     * @notes 区
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2022/4/6 7:02 下午
     */
    public function getDistrictAttr($value,$data)
    {
        return Region::where(['id'=>$data['district_id']])->value('name');
    }
}