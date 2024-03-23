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
use app\common\model\decorate\DecorateTabbar;
use app\common\model\decorate\Navigation;
use app\common\service\ConfigService;
use app\common\service\FileService;

class ConfigLogic extends BaseLogic
{
    /**
     * @notes 基础配置信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/23 10:30 上午
     */
    public function config()
    {
        $config = [
            // 登录方式
            'login_way'                 => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 注册强制绑定手机
            'coerce_mobile'             => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
            // 政策协议
            'login_agreement'           => ConfigService::get('login', 'login_agreement', config('project.login.login_agreement')),
            // 第三方登录 开关
            'third_auth'                => ConfigService::get('login', 'third_auth', config('project.login.third_auth')),
            // 微信授权登录
            'wechat_auth'               => ConfigService::get('login', 'wechat_auth', config('project.login.wechat_auth')),
            // qq授权登录
            'qq_auth'                   => ConfigService::get('login', 'qq_auth', config('project.login.qq_auth')),
            //版权信息
            'info'                      => ConfigService::get('copyright', 'info'),
            //ICP备案号
            'icp_number'                => ConfigService::get('copyright', 'icp_number'),
            //ICP备案链接
            'icp_link'                  => ConfigService::get('copyright', 'icp_link'),
            //底部导航
            'navigation_menu'           => DecorateTabbar::getTabbarLists(),
            // 导航颜色
            'style'                     => ConfigService::get('tabbar', 'style', '{}'),
            //地图key
            'tencent_map_key'           => ConfigService::get('map','tencent_map_key',''),
            //网站名称
            'web_name'                  => ConfigService::get('website', 'name'),
            //网站logo
            'web_logo'                  => FileService::getFileUrl(ConfigService::get('website', 'web_logo')),
            //商城名称
            'shop_name'                 => ConfigService::get('website', 'shop_name'),
            //商城logo
            'shop_logo'                 => FileService::getFileUrl(ConfigService::get('website', 'shop_logo')),
            //版本号
            'version'                   => request()->header('version'),
            //默认头像
            'default_avatar'            => ConfigService::get('config', 'default_avatar',  FileService::getFileUrl(config('project.default_image.user_avatar'))),
            //H5设置
            'h5_settings'               => [
                // 渠道状态 0-关闭 1-开启
                'status' => ConfigService::get('web_page', 'status', 1),
                // 关闭后渠道后访问页面 0-空页面 1-自定义链接
                'page_status' => ConfigService::get('web_page', 'page_status', 0),
                // 自定义链接
                'page_url' => ConfigService::get('web_page', 'page_url', ''),
                'url' => request()->domain() . '/mobile'
            ],
            //文件域名
            'domain' => request()->domain().'/',
        ];
        return $config;
    }

    /**
     * @notes 政策协议
     * @return array
     * @author ljj
     * @date 2022/2/23 11:42 上午
     */
    public function agreement()
    {
        $config = [
            'service_title' => ConfigService::get('agreement', 'service_title'),
            'service_content' => ConfigService::get('agreement', 'service_content'),
            'privacy_title' => ConfigService::get('agreement', 'privacy_title'),
            'privacy_content' => ConfigService::get('agreement', 'privacy_content'),
        ];
        return $config;
    }
}