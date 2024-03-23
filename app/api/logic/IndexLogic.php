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
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\BaseLogic;
use app\common\model\decorate\DecoratePage;
use app\common\model\goods\Goods;
use app\common\model\goods\GoodsCategory;
use app\common\model\IndexVisit;
use app\common\model\staff\Staff;
use app\common\service\ConfigService;

class IndexLogic extends BaseLogic
{
    /**
     * @notes 首页信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/23 4:48 下午
     */
    public function index($get)
    {
//        //首页菜单
//        $home_menu = Menu::where(['decorate_type'=>MenuEnum::NAVIGATION_HOME,'status'=>DefaultEnum::SHOW])
//            ->field('id,name,image,link_type,link_address')
//            ->order(['sort'=>'asc','id'=>'desc'])
//            ->append(['link'])
//            ->select()
//            ->toArray();
//
//        $shop_page = array_column(MenuEnum::SHOP_PAGE,NULL,'index');
//        foreach ($home_menu as &$menu) {
//            $menu['is_tab'] = 0;
//            if ($menu['link_type'] == 1) {
//                $menu['is_tab'] = $shop_page[$menu['link_address']]['is_tab'];
//            }
//        }

        // 装修配置
        $decoratePage = DecoratePage::where('id',1)->json(['data'],true)->value('data');

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

        return [
            'decorate_age' => $decoratePage,
            'hot_service' => $hot_service,
            'recommend_staff' => $recommend_staff,
            'recommend_goods_category' => $recommend_goods_category,
        ];
    }


    /**
     * @notes 首页访客记录
     * @return bool
     * @author Tab
     * @date 2021/9/11 9:29
     */
    public static function visit()
    {
        try {
            $params = request()->post();
            if (!isset($params['terminal']) || !in_array($params['terminal'], UserTerminalEnum::ALL_TERMINAL)) {
                throw new \Exception('终端参数缺失或有误');
            }
            $ip =  request()->ip();
            // 一个ip一个终端一天只生成一条记录
            $record = IndexVisit::where([
                'ip' => $ip,
                'terminal' => $params['terminal']
            ])->whereDay('create_time')->findOrEmpty();
            if (!$record->isEmpty()) {
                // 增加访客在终端的浏览量
                $record->visit += 1;
                $record->save();
                return true;
            }
            // 生成访客记录
            IndexVisit::create([
                'ip' => $ip,
                'terminal' => $params['terminal'],
                'visit' => 1
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 地址解析（地址转坐标）
     * @param $get
     * @return array|mixed
     * @author ljj
     * @date 2022/10/13 12:06 下午
     * 本接口提供由文字地址到经纬度的转换能力，并同时提供结构化的省市区地址信息。
     */
    public static function geocoder($get)
    {
        $get['key'] = ConfigService::get('map', 'tencent_map_key','');
        if ($get['key'] == '') {
            return ['status'=>1,'message'=>'腾讯地图开发密钥不能为空'];
        }

        $query = http_build_query($get);
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/';
        $result =  json_decode(file_get_contents($url.'?'.$query),true);

        return $result;
    }

    /**
     * @notes 逆地址解析（坐标位置描述）
     * @param $get
     * @return array|mixed
     * @author ljj
     * @date 2022/10/13 2:44 下午
     * 本接口提供由经纬度到文字地址及相关位置信息的转换能力
     */
    public static function geocoderCoordinate($get)
    {
        $get['key'] = ConfigService::get('map', 'tencent_map_key','');
        if ($get['key'] == '') {
            return ['status'=>1,'message'=>'腾讯地图开发密钥不能为空'];
        }

        $query = http_build_query($get);
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/';
        $result =  json_decode(file_get_contents($url.'?'.$query),true);

        return $result;
    }
}