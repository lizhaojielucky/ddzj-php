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

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;
use think\facade\Config;

/**
 * 注册逻辑层
 * Class RegisterLogic
 * @package app\api\logic
 */
class RegisterLogic extends BaseLogic
{
    public static function register($params)
    {
        try {
            $defaultAvatar = ConfigService::get('config', 'default_avatar',  FileService::getFileUrl(config('project.default_image.user_avatar')));
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);
            // 创建用户
            $data = [
                'channel' => $params['channel'],
                'sn' => create_user_sn(),
                'nickname' => $params['mobile'],
                'avatar' => $defaultAvatar,
                'mobile' => $params['mobile'],
                'account' => $params['mobile'],
                'password' => $password,
            ];
            $user = User::create($data);

            return true;
        } catch(\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }




}