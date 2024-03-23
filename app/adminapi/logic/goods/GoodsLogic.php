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

namespace app\adminapi\logic\goods;


use app\common\enum\DefaultEnum;
use app\common\logic\BaseLogic;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsImage;
use app\common\model\staff\Staff;
use think\facade\Db;

class GoodsLogic extends BaseLogic
{
    /**
     * @notes 添加服务
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2022/2/9 3:28 下午
     */
    public function add($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            $goods_image = $params['goods_image'];
            $image = array_shift($params['goods_image']);

            //添加服务信息
            $goods = Goods::create([
                'name' => $params['name'],
                'remarks' => $params['remarks'] ?? '',
                'category_id' => $params['category_id'],
                'unit_id' => $params['unit_id'],
                'image' => $image,
                'price' => $params['price'],
                'scribing_price' => $params['scribing_price'] ?? 0.00,
                'status' => $params['status'],
                'sort' => $params['sort'] ?? DefaultEnum::SORT,
                'content' => $params['content'] ?? '',
                'good_num'  => $params['good_num']??1
            ]);

            //添加服务轮播图信息
            foreach ($goods_image as $image_uri) {
                GoodsImage::create([
                    'goods_id' => $goods->id,
                    'uri' => $image_uri,
                ]);
            }

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 查看服务详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/9 3:51 下午
     */
    public function detail($id)
    {
        $result = Goods::where('id',$id)
            ->field('id,name,remarks,category_id,unit_id,price,scribing_price,good_num,status,sort,content')
            ->append(['category_desc','unit_desc','goods_image'])
            ->findOrEmpty()
            ->toArray();

        $goods_image = [];
        foreach ($result['goods_image'] as &$image) {
            $goods_image[] = $image['uri'];
        }
        $result['goods_image'] = $goods_image;

        return $result;
    }

    /**
     * @notes 编辑服务
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2022/2/9 4:06 下午
     */
    public function edit($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            $goods_image = $params['goods_image'];
            $image = array_shift($params['goods_image']);

            //更新服务信息
            Goods::update([
                'name' => $params['name'],
                'remarks' => $params['remarks'] ?? '',
                'category_id' => $params['category_id'],
                'unit_id' => $params['unit_id'],
                'image' => $image,
                'price' => $params['price'],
                'scribing_price' => $params['scribing_price'] ?? 0.00,
                'status' => $params['status'],
                'sort' => $params['sort'] ?? DefaultEnum::SORT,
                'content' => $params['content'] ?? '',
                'good_num'  => $params['good_num']??1
            ],['id'=>$params['id']]);

            //删除旧的轮播图
            GoodsImage::where('goods_id',$params['id'])->delete();
            //更新服务轮播图信息
            foreach ($goods_image as $image_uri) {
                GoodsImage::create([
                    'goods_id' => $params['id'],
                    'uri' => $image_uri,
                ]);
            }

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 删除服务
     * @param $ids
     * @return bool|string
     * @author ljj
     * @date 2022/2/9 4:13 下午
     */
    public function del($ids)
    {
        // 启动事务
        Db::startTrans();
        try {
            //删除师傅绑定的服务
            foreach ($ids as $id) {
                $staff_lists = Staff::whereRaw("FIND_IN_SET($id,goods_ids)")->select()->toArray();
                foreach ($staff_lists as $list) {
                    $goods_ids = str_replace(','.$id.',',',',$list['goods_ids']);
                    if ($goods_ids == ',') {
                        $goods_ids = '';
                    }
                    Staff::update(['goods_ids'=>$goods_ids],['id'=>$list['id']]);
                }
            }

            //删除服务
            Goods::destroy($ids);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 修改服务状态
     * @param $params
     * @return Goods
     * @author ljj
     * @date 2022/2/9 4:54 下午
     */
    public function status($params)
    {
        return Goods::update(['status'=>$params['status']],['id'=>$params['ids']]);
    }
}