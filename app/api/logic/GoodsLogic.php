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
use app\common\model\goods\GoodsCollect;
use app\common\model\order\OrderTime;
use app\common\model\staff\Staff;
use app\common\service\ConfigService;

class GoodsLogic extends BaseLogic
{
    /**
     * @notes 服务详情
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/18 10:40 上午
     */
    public function detail($params)
    {
        $result = Goods::field('id,name,remarks,category_id,unit_id,image,price,scribing_price,content,order_num')
            ->append(['unit_desc'])
            ->with(['goods_image','goods_comment' => function($query){
                $query->order(['id'=>'desc'])->limit(1);
            }])
            ->where(['id'=>$params['id']])
            ->findOrEmpty()
            ->toArray();

        //是否收藏
        $collect = GoodsCollect::where(['user_id'=>$params['user_id'],'goods_id'=>$params['id']])->findOrEmpty();
        $result['is_collect'] = !$collect->isEmpty() ? 1 : 0;

        //服务师傅
        $city_id = (isset($params['city_id']) && $params['city_id'] != '') ? $params['city_id'] : 0;
        $result['goods_staff'] = Staff::where("find_in_set({$params['id']},goods_ids)")
            ->where(['status'=>DefaultEnum::SHOW,'city_id'=> $city_id])
            ->field('id,name,user_id')
            ->append(['user'])
            ->select()
            ->toArray();

//        //上门时间
//        $result['appoint_time'] = [
//            'order_time' => ConfigService::get('order_time','time',7),
//            'appoint_time' => OrderTime::order(['sort'=>'asc','id'=>'desc'])->field('start_time,end_time')->select()->toArray(),
//        ];

        //修改商品图片格式
        $data = [];
        foreach ($result['goods_image'] as $goods_image) {
            $data[] = [
                'id' => $goods_image['id'],
                'goods_id' => $goods_image['goods_id'],
                'image' => $goods_image['uri'],
            ];
        }
        unset($result['goods_image']);
        $result['goods_image']['data'] = $data;

        return $result;
    }

    /**
     * @notes 预约上门时间
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/3/11 2:32 下午
     */
    public function appointTime($params)
    {
        $goodId = $params['id'];
        return [
            'order_time' => ConfigService::get('order_time','time',7),
            'appoint_time' => OrderTime::whereRaw("FIND_IN_SET({$goodId}, goods) > 0")->order(['sort'=>'desc','id'=>'desc'])->field('start_time,end_time')->select()->toArray(),
        ];
    }

    /**
     * @notes 收藏服务
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/3/16 4:14 下午
     */
    public function collect($params)
    {
        if($params['is_collect']){
            $goods_collect = GoodsCollect::where(['goods_id'=>$params['id'],'user_id'=>$params['user_id']])->findOrEmpty();
            if(!$goods_collect->isEmpty()){
                return true;
            }

            $goods_collect->goods_id = $params['id'];
            $goods_collect->user_id  = $params['user_id'];
            $goods_collect->save();
        }else {
            GoodsCollect::where(['goods_id'=>$params['id'],'user_id'=>$params['user_id']])->delete();
        }

        return true;
    }
}