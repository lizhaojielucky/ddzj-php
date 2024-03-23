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

namespace app\api\controller;


use app\api\validate\PasswordValidate;
use app\common\enum\notice\NoticeEnum;
use app\api\logic\UserLogic;
use app\api\validate\UserValidate;

class UserController extends BaseShopController
{
    public array $notNeedLogin = ['center','customerService','resetPasswordCaptcha','resetPassword'];


    /**
     * @notes 用户中心
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/23 5:25 下午
     */
    public function center()
    {
        $result = (new UserLogic())->center($this->userId);
        return $this->success('',$result);
    }

    /**
     * @notes 客服配置
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 2:53 下午
     */
    public function customerService()
    {
        $result = (new UserLogic())->customerService();
        return $this->success('',$result);
    }

    /**
     * @notes 用户收藏列表
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 3:07 下午
     */
    public function collectLists()
    {
        $lists = (new UserLogic())->collectLists($this->userId);
        return $this->success('',$lists);
    }

    /**
     * @notes 用户信息
     * @return \think\response\Json
     * @author ljj
     * @date 2022/3/7 5:53 下午
     */
    public function info()
    {
        $result = UserLogic::info($this->userId);
        return $this->data($result);
    }

    /**
     * @notes 设置用户信息
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 3:44 下午
     */
    public function setInfo()
    {
        $params = (new UserValidate())->post()->goCheck('setInfo', ['id' => $this->userId]);
        (new UserLogic)->setInfo($this->userId, $params);
        return $this->success('操作成功', [],1,1);
    }

    /**
     * @notes 获取微信手机号并绑定
     * @return \think\response\Json
     * @author ljj
     * @date 2022/2/24 4:41 下午
     */
    public function getMobileByMnp()
    {
        $params = (new UserValidate())->post()->goCheck('getMobileByMnp');
        $params['user_id'] = $this->userId;
        $result = UserLogic::getMobileByMnp($params);
        if($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 发送验证码 - 重置登录密码
     * @author Tab
     * @date 2021/8/25 16:33
     */
    public function resetPasswordCaptcha()
    {
        $params = (new UserValidate())->post()->goCheck('resetPasswordCaptcha');
        $code = mt_rand(1000, 9999);
        $result = event('Notice', [
            'scene_id' =>  NoticeEnum::FIND_LOGIN_PASSWORD_CAPTCHA,
            'params' => [
                'user_id' => $this->userId,
                'code' => $code,
                'mobile' => $params['mobile']
            ]
        ]);
        if ($result[0] === true) {
            return $this->success('发送成功');
        }

        return $this->fail($result[0], [], 0, 1);
    }

    /**
     * @notes 重置密码
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/9/16 18:06
     */
    public function resetPassword()
    {
        $params = (new PasswordValidate())->post()->goCheck('resetPassword');
        $result = UserLogic::resetPassword($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }


    /**
     * @notes 设置登录密码
     * @return \think\response\Json
     * @author Tab
     * @date 2021/10/22 18:09
     */
    public function setPassword()
    {
        $params = (new UserValidate())->post()->goCheck('setPassword');
        $params['user_id'] = $this->userId;
        $result = UserLogic::setPassword($params);
        if($result) {
            return $this->success('设置成功',[], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }


    /**
     * @notes 修改密码
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/9/20 19:16
     */
    public function changePassword()
    {
        $params = (new PasswordValidate())->post()->goCheck('changePassword');
        $result = UserLogic::changePassword($params, $this->userId);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }


    /**
     * @notes 判断用户是否设置登录密码
     * @return mixed
     * @author Tab
     * @date 2021/10/22 18:24
     */
    public function hasPassword()
    {
        $result =  UserLogic::hasPassword($this->userId);
        return $this->data([
            'has_password' => $result
        ]);
    }


    /**
     * @notes 发送验证码 - 绑定手机号
     * @author Tab
     * @date 2021/8/25 17:35
     */
    public function bindMobileCaptcha()
    {
        $params = (new UserValidate())->post()->goCheck('bindMobileCaptcha');
        $code = mt_rand(1000, 9999);
        $result = event('Notice', [
            'scene_id' =>  NoticeEnum::BIND_MOBILE_CAPTCHA,
            'params' => [
                'user_id' => $this->userId,
                'code' => $code,
                'mobile' => $params['mobile']
            ]
        ]);
        if ($result[0] === true) {
            return $this->success('发送成功');
        }

        return $this->fail($result[0], [], 0, 1);
    }

    /**
     * @notes 发送验证码 - 变更手机号
     * @author Tab
     * @date 2021/8/25 17:35
     */
    public function changeMobileCaptcha()
    {
        $params = (new UserValidate())->post()->goCheck('changeMobileCaptcha');
        $code = mt_rand(1000, 9999);
        $result = event('Notice', [
            'scene_id' =>  NoticeEnum::CHANGE_MOBILE_CAPTCHA,
            'params' => [
                'user_id' => $this->userId,
                'code' => $code,
                'mobile' => $params['mobile']
            ]
        ]);
        if ($result[0] === true) {
            return $this->success('发送成功');
        }

        return $this->fail($result[0], [], 0, 1);
    }

    /**
     * @notes 绑定手机号
     * @return \think\response\Json
     * @author Tab
     * @date 2021/8/25 17:46
     */
    public function bindMobile()
    {
        $params = (new UserValidate())->post()->goCheck('bindMobile');
        $params['id'] = $this->userId;
        $result = UserLogic::bindMobile($params);
        if($result) {
            return $this->success('绑定成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }

    /**
     * @notes 我的钱包
     * @return \think\response\Json
     * @author ljj
     * @date 2022/12/12 9:35 上午
     */
    public function wallet()
    {
        $result = UserLogic::wallet($this->userId);
        return $this->data($result);
    }
}