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
namespace app\adminapi\logic\setting\user;
use app\common\{service\ConfigService, service\FileService};
/**
 * 设置-用户设置逻辑层
 * Class UserLogic
 * @package app\adminapi\logic\config
 */
class UserLogic
{


    /**
     * @notes 获取用户设置
     * @return array
     * @author cjhao
     * @date 2021/7/27 17:49
     */
    public static function getConfig():array
    {
        $config = [
            //默认头像
            'default_avatar'            => ConfigService::get('config', 'default_avatar',  FileService::getFileUrl(config('project.default_image.user_avatar'))),
        ];
        return $config;
    }

    /**
     * @notes 设置用户设置
     * @param array $postData
     * @return bool
     * @author cjhao
     * @date 2021/7/27 17:58
     */
    public function setConfig(array $params):bool
    {
        ConfigService::set('config', 'default_avatar', $params['default_avatar']);
        return true;
    }

    /**
     * @notes 获取注册配置
     * @return array
     * @author ljj
     * @date 2022/2/17 3:32 下午
     */
    public function getRegisterConfig():array
    {
        $config = [
            // 登录方式
            'login_way' => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 注册强制绑定手机
            'coerce_mobile' => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
            // 政策协议
//            'login_agreement' => ConfigService::get('login', 'login_agreement', config('project.login.login_agreement')),
            // 第三方登录 开关
            'third_auth' => ConfigService::get('login', 'third_auth', config('project.login.third_auth')),
            // 微信授权登录
            'wechat_auth' => ConfigService::get('login', 'wechat_auth', config('project.login.wechat_auth')),
            // qq授权登录
//            'qq_auth' => ConfigService::get('login', 'qq_auth', config('project.login.qq_auth')),
        ];
        return $config;
    }


    /**
     * @notes 设置登录注册
     * @param array $params
     * @return bool
     * @author cjhao
     * @date 2021/9/14 17:20
     */
    public static function setRegisterConfig(array $params):bool
    {
        // 登录方式：1-账号密码登录；2-手机短信验证码登录
        ConfigService::set('login', 'login_way', $params['login_way']);
        // 注册强制绑定手机
        ConfigService::set('login', 'coerce_mobile', $params['coerce_mobile']);
        // 政策协议
//        ConfigService::set('login', 'login_agreement', $params['login_agreement']);
        // 第三方授权登录
        ConfigService::set('login', 'third_auth', $params['third_auth']);
        // 微信授权登录
        ConfigService::set('login', 'wechat_auth', $params['wechat_auth']);
        // qq登录
//        ConfigService::set('login', 'qq_auth', $params['qq_auth']);
        return true;

    }

}