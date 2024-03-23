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

use app\common\{
    cache\AdminAuthCache,
    service\ConfigService,
    service\FileService
};
use WpOrg\Requests\Requests;

/**
 * 配置类逻辑层
 * Class ConfigLogic
 * @package app\adminapi\logic
 */
class ConfigLogic
{
    /**
     * @notes 获取配置
     * @return array
     * @author 段誉
     * @date 2021/12/31 11:03
     */
    public static function getConfig(): array
    {
        $config = [
            // 文件域名
            'oss_domain' => request()->domain().'/',

            // 网站名称
            'web_name' => ConfigService::get('website', 'name'),
            // 网站图标
            'web_favicon' => FileService::getFileUrl(ConfigService::get('website', 'web_favicon')),
            // 网站logo
            'web_logo' => FileService::getFileUrl(ConfigService::get('website', 'web_logo')),
            // 登录页
            'login_image' => FileService::getFileUrl(ConfigService::get('website', 'login_image')),

            // 版权信息
            'copyright_config' => ConfigService::get('copyright', 'config', []),

            //文档信息开关
            'document_status' => ConfigService::get('website','document_status',1),
        ];
        return $config;
    }



    /**
     * @notes 正版检测
     * @return mixed
     * @author ljj
     * @date 2023/5/16 11:49 上午
     */
    public static function checkLegal()
    {
        $check_domain = config('project.check_domain');
        $product_code = config('project.product_code');
        $domain = $_SERVER['HTTP_HOST'];
        $result = Requests::get($check_domain.'/api/version/productAuth?code='.$product_code.'&domain='.$domain);
        $result = json_decode($result->body,true);
        return $result['data'];
    }

    /**
     * @notes 检测新版本
     * @return mixed
     * @author ljj
     * @date 2023/5/25 7:02 下午
     */
    public static function checkVersion()
    {
        $version = config('project.version');
        $product_code = config('project.product_code');
        $check_domain = config('project.check_domain');
        $result = Requests::get($check_domain.'/api/version/hasNew?code='.$product_code.'&version='.$version);
        $result = json_decode($result->body,true);
        return $result['data'];
    }
}