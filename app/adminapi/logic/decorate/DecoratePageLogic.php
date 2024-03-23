<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------
namespace app\adminapi\logic\decorate;


use app\common\enum\DefaultEnum;
use app\common\logic\BaseLogic;
use app\common\model\decorate\DecoratePage;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsCategory;
use app\common\model\staff\Staff;


/**
 * 装修页面
 * Class DecoratePageLogic
 * @package app\adminapi\logic\theme
 */
class DecoratePageLogic extends BaseLogic
{


    /**
     * @notes 获取详情
     * @param $id
     * @return array
     * @author 段誉
     * @date 2022/9/14 18:41
     */
    public static function getDetail($id)
    {
        $result = DecoratePage::findOrEmpty($id)->toArray();

        //热门服务
        $hot_service = Goods::where(['status'=>DefaultEnum::SHOW])
            ->field('id,name,remarks,image')
            ->order(['order_num'=>'desc','sort'=>'asc','id'=>'desc'])
            ->limit(5)
            ->select()
            ->toArray();

        //师傅推荐
        $staff_where = [];
        if (isset($get['city_id']) && $get['city_id'] != '') {
            $staff_where[] = ['city_id','=',$get['city_id']];
        }
        $recommend_staff = Staff::where(['status'=>DefaultEnum::SHOW,'is_recommend'=>DefaultEnum::SHOW])
            ->field('id,user_id,name,goods_ids')
            ->append(['goods_name','user_image'])
            ->order(['id'=>'desc'])
            ->where($staff_where)
            ->limit(5)
            ->select()
            ->toArray();

        //首页推荐服务分类
        $recommend_goods_category = GoodsCategory::where(['is_show'=>DefaultEnum::SHOW,'is_recommend'=>DefaultEnum::SHOW,'level'=>1])
            ->field('id,name')
            ->order(['sort'=>'desc','id'=>'desc'])
            ->select()
            ->toArray();
        foreach ($recommend_goods_category as &$category) {
            $categoryIds = GoodsCategory::where(['pid'=>$category['id']])->column('id');
            Array_push($categoryIds,$category['id']);
            $category['goods'] = Goods::where(['category_id' => $categoryIds,'status'=>DefaultEnum::SHOW])
                ->field('id,name,unit_id,image,price')
                ->order(['sort'=>'asc','id'=>'desc'])
                ->append(['unit_desc'])
                ->limit(3)
                ->select()->toArray();

            foreach ($category['goods'] as &$goods) {
                $goods['price'] = trim(rtrim(sprintf("%.4f", $goods['price'] ), '0'),'.');
            }
        }

        $result['hot_service'] = $hot_service;
        $result['recommend_staff'] = $recommend_staff;
        $result['recommend_goods_category'] = $recommend_goods_category;

        return $result;
    }


    /**
     * @notes 保存装修配置
     * @param $params
     * @return bool
     * @author 段誉
     * @date 2022/9/15 9:37
     */
    public static function save($params)
    {
        $pageData = DecoratePage::where(['id' => $params['id']])->findOrEmpty();
        if ($pageData->isEmpty()) {
            self::$error = '信息不存在';
            return false;
        }
        DecoratePage::update([
            'id' => $params['id'],
            'type' => $params['type'],
            'data' => $params['data'],
        ]);
        return true;
    }



}