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


use app\common\logic\BaseLogic;
use app\common\model\goods\GoodsComment;
use app\common\model\goods\GoodsCommentImage;
use app\common\model\order\OrderGoods;
use think\facade\Db;

class GoodsCommentLogic extends BaseLogic
{
    /**
     * @notes 服务评价分类
     * @param $parmas
     * @return array
     * @author ljj
     * @date 2022/2/18 2:09 下午
     */
    public function commentCategory($parmas)
    {
        $all_count = GoodsComment::where('goods_id', $parmas['goods_id'])->count();
        $image_count = GoodsComment::alias('gc')->where('goods_id', $parmas['goods_id'])->join('goods_comment_image gci', 'gc.id = gci.comment_id')->group('gci.comment_id')->count();
        $good_count = GoodsComment::where('goods_id', $parmas['goods_id'])->where('service_comment','>',3)->count();
        $medium_count = GoodsComment::where('goods_id', $parmas['goods_id'])->where('service_comment','=',3)->count();
        $bad_count = GoodsComment::where('goods_id', $parmas['goods_id'])->where('service_comment','<',3)->count();

        if($all_count == 0) {
            $percentStr = '100%';
            $star = 5;
        }else {
            $percent = round((($good_count / $all_count) * 100));
            $percentStr = round((($good_count / $all_count) * 100)).'%';
            if ($percent >= 100) {
                $star = 5;
            } else if ($percent >= 80) {
                $star = 4;
            } else if ($percent >= 60) {
                $star = 3;
            } else if ($percent >= 40) {
                $star = 2;
            } else if ($percent >= 20) {
                $star = 1;
            } else {
                $star = 0;
            }
        }

        return ['comment'=>
            [
                [
                    'id'    => 0,
                    'name'  => '全部',
                    'count' => $all_count
                ],
                [
                    'id'    => 1,
                    'name'  => '好评',
                    'count' => $good_count
                ],
                [
                    'id'    => 2,
                    'name'  => '中评',
                    'count' => $medium_count
                ],
                [
                    'id'    => 3,
                    'name'  => '差评',
                    'count' => $bad_count
                ],
                [
                    'id'    => 4,
                    'name'  => '晒图',
                    'count' => $image_count
                ],
            ] ,
            'percent'   => $percentStr,
            'star'   => $star,
        ];
    }

    /**
     * @notes 评价服务信息
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/21 6:12 下午
     */
    public function commentGoodsInfo($params)
    {
        $info = OrderGoods::field('id,goods_id,goods_name,goods_price,goods_num,goods_snap,unit_name')
            ->where('id', '=', $params['order_goods_id'])
            ->append(['goods_image'])
            ->hidden(['goods_snap'])
            ->find()
            ->toArray();

        return $info;
    }

    /**
     * @notes 添加服务评价
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/21 6:23 下午
     */
    public function add($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            //获取订单商品信息
            $order_goods = OrderGoods::find($params['order_goods_id'])->toArray();

            //添加评价数据
            $goods_comment = GoodsComment::create([
                'goods_id' => $order_goods['goods_id'],
                'user_id' => $params['user_id'],
                'order_goods_id' => $params['order_goods_id'],
                'service_comment' => $params['service_comment'],
                'comment' => $params['comment'] ?? '',
            ]);

            //添加评价图片数据
            if (isset($params['image'])) {
                $image_data = [];
                foreach ($params['image'] as $val) {
                    $image_data[] = [
                        'comment_id' => $goods_comment->id,
                        'uri' => $val,
                    ];
                }
                $goods_comment_image = new GoodsCommentImage;
                $goods_comment_image->saveAll($image_data);
            }

            //修改订单商品表评价状态
            OrderGoods::update(['is_comment' => 1], ['id' => $params['order_goods_id']]);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }
}