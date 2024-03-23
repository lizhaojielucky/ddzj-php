<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\adminapi\logic;


use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\IndexVisit;
use app\common\model\order\Order;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;


/**
 * 工作台
 * Class WorkbenchLogic
 * @package app\adminapi\logic
 */
class WorkbenchLogic extends BaseLogic
{
    /**
     * @notes 工作套
     * @param $adminInfo
     * @return array
     * @author 段誉
     * @date 2021/12/29 15:58
     */
    public static function index()
    {
        return [
            // 版本信息
            'version' => self::versionInfo(),
            // 今日数据
            'today' => self::today(),
            // 常用功能
            'menu' => self::menu(),
            // 近15日访客数
            'visitor' => self::visitor(),
            // 近15日营业额
            'business' => self::business15()
        ];
    }


    /**
     * @notes 常用功能
     * @return array[]
     * @author 段誉
     * @date 2021/12/29 16:40
     */
    public static function menu() : array
    {
        return [
            [
                'name' => '服务列表',
                'image' => FileService::getFileUrl(config('project.default_image.admin_goods_lists')),
                'url' => '/service/lists'
            ],
            [
                'name' => '分类管理',
                'image' => FileService::getFileUrl(config('project.default_image.admin_goods_category')),
                'url' => '/service/category'
            ],
            [
                'name' => '订单列表',
                'image' => FileService::getFileUrl(config('project.default_image.admin_order')),
                'url' => '/order/index'
            ],
            [
                'name' => '师傅列表',
                'image' => FileService::getFileUrl(config('project.default_image.admin_staff')),
                'url' => '/master_worker/index'
            ],
            [
                'name' => '广告列表',
                'image' => FileService::getFileUrl(config('project.default_image.admin_ad')),
                'url' => '/decorate/index'
            ],
            [
                'name' => '首页装修',
                'image' => FileService::getFileUrl(config('project.default_image.admin_index_decorate')),
                'url' => '/decorate/index'
            ],
            [
                'name' => '消息通知',
                'image' => FileService::getFileUrl(config('project.default_image.admin_news_notice')),
                'url' => '/setting/message/notice'
            ],
//            [
//                'name' => '支付配置',
//                'image' => FileService::getFileUrl(config('project.default_image.admin_set_payment')),
//                'url' => '/setting/payment/payment_config'
//            ],
        ];
    }


    /**
     * @notes 版本信息
     * @return array
     * @author 段誉
     * @date 2021/12/29 16:08
     */
    public static function versionInfo() : array
    {
        return [
            'version' => config('project.version'),
            'website' => ConfigService::get('website', 'name'),
        ];
    }


    /**
     * @notes 今日数据
     * @return int[]
     * @author 段誉
     * @date 2021/12/29 16:15
     */
    public static function today() : array
    {
        return [
            'time' => date('Y-m-d H:i:s'),

            // 今日销量
            'today_sales_count' => Order::where('pay_status', YesNoEnum::YES)
                ->whereDay('create_time')
                ->count(),
            // 总销销量
            'total_sales_count' => Order::where('pay_status', YesNoEnum::YES)
                ->count(),

            // 今日销售额
            'today_sales_amount' => Order::where('pay_status', YesNoEnum::YES)
                ->whereDay('create_time')
                ->sum('order_amount'),
            // 总销售额
            'total_sales_amount' => Order::where('pay_status', YesNoEnum::YES)
                ->sum('order_amount'),

            // 今日访问量
            'today_visitor' => count(array_unique(IndexVisit::whereDay('create_time')->column('ip'))),
            // 总访问量
            'total_visitor' => count(array_unique(IndexVisit::column('ip'))),

            // 今日新增用户量
            'today_new_user' => User::whereDay('create_time')->count(),
            // 总用户量
            'total_new_user' => User::count(),
        ];
    }


    /**
     * @notes 文章阅读排名
     * @return array[]
     * @author 段誉
     * @date 2021/12/29 16:40
     */
    public static function article() : array
    {
        return [
            ['name' => '文章1', 'read' => 1000],
            ['name' => '文章2', 'read' => 800],
            ['name' => '文章3', 'read' => 600],
            ['name' => '文章4', 'read' => 400],
        ];
    }


    /**
     * @notes 访问数
     * @return array
     * @author 段誉
     * @date 2021/12/29 16:57
     */
    public static function visitor() : array
    {
        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d') . ' 23:59:59';
        $todayDec15 = $today->add(\DateInterval::createFromDateString('-14day'));
        $todayDec15Str = $todayDec15->format('Y-m-d');

        $field = [
            "FROM_UNIXTIME(create_time,'%Y%m%d') as date",
            "ip"
        ];
        $lists = IndexVisit::field($field)
            ->distinct(true)
            ->whereTime('create_time', 'between', [$todayDec15Str,$todayStr])
            ->select()
            ->toArray();

        // 集合一天的IP
        $temp1 =  [];
        foreach ($lists as $item) {
            $temp1[$item['date']][] = $item['ip'];
        }
        // 统计数量
        $temp2 = [];
        foreach ($temp1 as $k => $v) {
            $temp2[$k] = count($v);
        }

        $userData = [];
        $date = [];
        for($i = 0; $i < 15; $i ++) {
            $today = new \DateTime();
            $targetDay = $today->add(\DateInterval::createFromDateString('-'. $i . 'day'));
            $targetDay = $targetDay->format('Ymd');
            $date[] = $targetDay;
            $userData[] = $temp2[$targetDay] ?? 0;
        }
        return [
            'date' => $date,
            'list' => [
                ['name' => '访客数', 'data' => $userData]
            ]
        ];
    }



    /**
     * @notes 近15天营业额
     * @return array
     * @author Tab
     * @date 2021/9/10 18:06
     */
    public static function business15()
    {
        $today = new \DateTime();
        $todayStr = $today->format('Y-m-d') . ' 23:59:59';
        $todayDec15 = $today->add(\DateInterval::createFromDateString('-14day'));
        $todayDec15Str = $todayDec15->format('Y-m-d');

        $field = [
            "FROM_UNIXTIME(create_time,'%Y%m%d') as date",
            "sum(order_amount) as today_amount"
        ];
        $lists = Order::field($field)
            ->whereTime('create_time', 'between', [$todayDec15Str,$todayStr])
            ->where('pay_status', YesNoEnum::YES)
            ->group('date')
            ->select()
            ->toArray();

        $lists = array_column($lists, 'today_amount', 'date');
        $amountData = [];
        $date = [];

        for($i = 0; $i < 15; $i ++) {
            $today = new \DateTime();
            $targetDay = $today->add(\DateInterval::createFromDateString('-'. $i . 'day'));
            $targetDay = $targetDay->format('Ymd');
            $date[] = $targetDay;
            $amountData[] = $lists[$targetDay] ?? 0;
        }
        return [
            'date' => $date,
            'list' => [
                ['name' => '营业额', 'data' => $amountData]
            ]
        ];
    }

}