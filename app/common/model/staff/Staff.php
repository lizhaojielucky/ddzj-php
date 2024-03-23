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

namespace app\common\model\staff;


use app\common\enum\DefaultEnum;
use app\common\model\BaseModel;
use app\common\model\goods\Goods;
use app\common\model\Region;
use app\common\model\user\User;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

class Staff extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    /**
     * @notes 关联用户模型
     * @return \think\model\relation\HasOne
     * @author ljj
     * @date 2022/2/10 11:31 上午
     */
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id')->field('id,sn,nickname,avatar,mobile,account');
    }

    /**
     * @notes 是否首页推荐
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/2/10 11:38 上午
     */
    public function getRecommendDescAttr($value,$data)
    {
        return DefaultEnum::getRecommendDesc($data['is_recommend']);
    }

    /**
     * @notes 性别
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/2/10 11:41 上午
     */
    public function getSexDescAttr($value,$data)
    {
        return DefaultEnum::getSexDesc($data['sex']);
    }

    /**
     * @notes 服务名称
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2022/2/10 4:07 下午
     */
    public function getGoodsNameAttr($value,$data)
    {
        $goods_ids = explode(',',trim($data['goods_ids'],','));
        $goods_arr = Goods::where(['id'=>$goods_ids])->column('name');
        return implode('、',$goods_arr);
    }

    /**
     * @notes 服务项目
     * @param $value
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/10 4:12 下午
     */
    public function getGoodsAttr($value,$data)
    {
        $goods_ids = explode(',',trim($data['goods_ids'],','));
        return Goods::where(['id'=>$goods_ids])
            ->field('id,name,image,category_id,unit_id,price,status,create_time')
            ->append(['category_desc','unit_desc','status_desc'])
            ->select()
            ->toArray();
    }

    /**
     * @notes 用户头像
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2022/2/23 4:48 下午
     */
    public function getUserImageAttr($value,$data)
    {
        return FileService::getFileUrl(User::where('id',$data['user_id'])->value('avatar'));
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
