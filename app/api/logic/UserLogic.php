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


use app\common\enum\notice\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\BaseLogic;
use app\common\model\decorate\DecoratePage;
use app\common\model\goods\Goods;
use app\common\model\order\Order;
use app\common\model\staff\Staff;
use app\common\model\user\User;
use app\common\model\user\UserAuth;
use app\common\service\ConfigService;
use app\common\service\sms\SmsDriver;
use app\common\service\WeChatConfigService;
use EasyWeChat\Factory;
use think\facade\Config;

class UserLogic extends BaseLogic
{
    /**
     * @notes 用户中心
     * @param $user_id
     * @return array
     * @author ljj
     * @date 2022/2/23 5:24 下午
     */
    public function center($user_id)
    {
        $user = User::where(['id'=>$user_id])
            ->field('id,nickname,avatar,mobile,sex,create_time,is_new_user')
            ->findOrEmpty()
            ->toArray();

        // 装修配置
        $user['decorate_page'] = DecoratePage::where('id',2)->json(['data'],true)->value('data');

        if (!empty($user)) {
            $user['wait_pay_num'] = Order::where(['user_id'=>$user_id,'order_status'=>OrderEnum::ORDER_STATUS_WAIT_PAY])->count();
            $user['appoint_num'] = Order::where(['user_id'=>$user_id,'order_status'=>OrderEnum::ORDER_STATUS_APPOINT])->count();
            $user['service_num'] = Order::where(['user_id'=>$user_id,'order_status'=>OrderEnum::ORDER_STATUS_SERVICE])->count();
            $user['finish_num'] = Order::where(['user_id'=>$user_id,'order_status'=>OrderEnum::ORDER_STATUS_FINISH])->count();

            //判断用户是否是师傅
            $staff = Staff::where(['user_id'=>$user_id])->findOrEmpty()->toArray();
            $user['is_staff'] = empty($staff) ? 0 : 1;

            //等待师傅确认服务订单
            $user['staff_wait_num'] = 0;
            if ($staff) {
                $user['staff_wait_num'] = Order::where(['staff_id'=>$staff['id'],'order_status'=>OrderEnum::ORDER_STATUS_APPOINT])->count();
            }

            //不是师傅的用户不显示订单服务
            if ($user['is_staff'] == 0) {
                $user['decorate_page'] = array_column($user['decorate_page'],NULL,'name');
                foreach ($user['decorate_page']['my-service']['content']['data'] as $key=>$val) {
                    if ($val['link']['path'] == '/bundle/pages/service_order/index') {
                        unset($user['decorate_page']['my-service']['content']['data'][$key]);
                        break;
                    }
                }
            }
            $user['decorate_page'] = array_values($user['decorate_page']);
        }

        return $user;
    }

    /**
     * @notes 客服配置
     * @return array
     * @author ljj
     * @date 2022/2/24 2:53 下午
     */
    public function customerService()
    {
//        $qrCode = ConfigService::get('customer_service', 'qr_code');
//        $qrCode = empty($qrCode) ? '' : FileService::getFileUrl($qrCode);
//        $config = [
//            'qr_code' => $qrCode,
//            'wechat' => ConfigService::get('customer_service', 'wechat', ''),
//            'phone' => ConfigService::get('customer_service', 'phone', ''),
//            'service_time' => ConfigService::get('customer_service', 'service_time', ''),
//        ];

        // 装修配置
        $decoratePage = DecoratePage::where('id',3)->json(['data'],true)->value('data');

        return $decoratePage;
    }

    /**
     * @notes 用户收藏列表
     * @param $user_id
     * @return mixed
     * @author ljj
     * @date 2022/2/24 3:07 下午
     */
    public function collectLists($user_id)
    {
        $lists = Goods::alias('g')
            ->join('goods_collect gc', 'g.id = gc.goods_id')
            ->field('gc.goods_id,g.image,g.name,g.price,g.unit_id,g.status')
            ->append(['unit_desc'])
            ->where(['gc.user_id'=>$user_id])
            ->order('gc.id','desc')
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 用户信息
     * @param $userId
     * @return array
     * @author ljj
     * @date 2022/3/7 5:52 下午
     */
    public static function info($user_id)
    {
        $user = User::where(['id'=>$user_id])
            ->field('id,sn,sex,account,password,nickname,real_name,avatar,mobile,create_time')
            ->findOrEmpty();
        $user['has_password'] = !empty($user['password']);
        $user['has_auth'] = self::hasWechatAuth($user_id);
        $user['version'] = config('project.version');
        $user->hidden(['password']);
        return $user->toArray();
    }

    /**
     * @notes 设置用户信息
     * @param int $userId
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2022/2/24 3:44 下午
     */
    public static function setInfo(int $userId,array $params):bool
    {
        User::update(['id'=>$userId,$params['field']=>$params['value']]);
        return true;
    }

    /**
     * @notes 获取微信手机号并绑定
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/2/24 4:41 下午
     */
    public static function getMobileByMnp(array $params)
    {
        try {
            $getMnpConfig = WeChatConfigService::getMnpConfig();
            $app = Factory::miniProgram($getMnpConfig);
            $response = $app->phone_number->getUserPhoneNumber($params['code']);

            $phoneNumber = $response['phone_info']['purePhoneNumber'] ?? '';
            if (empty($phoneNumber)) {
                throw new \Exception('获取手机号码失败');
            }

            $user = User::where([
                ['mobile', '=', $phoneNumber],
                ['id', '<>', $params['user_id']]
            ])->findOrEmpty();

            if (!$user->isEmpty()) {
                throw new \Exception('手机号已被其他账号绑定');
            }

            // 绑定手机号
            User::update([
                'id' => $params['user_id'],
                'mobile' => $phoneNumber
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 重置登录密码
     * @param $params
     * @return bool
     * @author 段誉
     * @date 2022/9/16 18:06
     */
    public static function resetPassword(array $params)
    {
        try {
            // 校验验证码
            $smsDriver = new SmsDriver();
            if (!$smsDriver->verify($params['mobile'], $params['code'], NoticeEnum::FIND_LOGIN_PASSWORD_CAPTCHA)) {
                throw new \Exception('验证码错误');
            }

            // 重置密码
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);

            // 更新
            User::where('mobile', $params['mobile'])->update([
                'password' => $password
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 设置登录密码
     * @author Tab
     * @date 2021/10/22 18:10
     */
    public static function setPassword($params)
    {
        try {
            $user = User::findOrEmpty($params['user_id']);
            if ($user->isEmpty()) {
                throw new \Exception('用户不存在');
            }
            if (!empty($user->password)) {
                throw new \Exception('用户已设置登录密码');
            }
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);
            $user->password = $password;
            $user->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 修稿密码
     * @param $params
     * @param $userId
     * @return bool
     * @author 段誉
     * @date 2022/9/20 19:13
     */
    public static function changePassword(array $params, int $userId)
    {
        try {
            $user = User::findOrEmpty($userId);
            if ($user->isEmpty()) {
                throw new \Exception('用户不存在');
            }

            // 密码盐
            $passwordSalt = Config::get('project.unique_identification');

            if (!empty($user['password'])) {
                if (empty($params['old_password'])) {
                    throw new \Exception('请填写旧密码');
                }
                $oldPassword = create_password($params['old_password'], $passwordSalt);
                if ($oldPassword != $user['password']) {
                    throw new \Exception('原密码不正确');
                }
            }

            // 保存密码
            $password = create_password($params['password'], $passwordSalt);
            $user->password = $password;
            $user->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 判断用户是否有设置登录密码
     * @param $userId
     * @author Tab
     * @date 2021/10/22 18:25
     */
    public static function hasPassword($userId)
    {
        $user = User::findOrEmpty($userId);
        return empty($user->password) ? false : true;
    }


    /**
     * @notes 绑定手机号
     * @param $params
     * @return bool
     * @author Tab
     * @date 2021/8/25 17:55
     */
    public static function bindMobile($params)
    {
        try {
            $smsDriver = new SmsDriver();
            $result = $smsDriver->verify($params['mobile'], $params['code']);
            if(!$result) {
                throw new \Exception('验证码错误');
            }
            $user = User::where('mobile', $params['mobile'])->findOrEmpty();
            if(!$user->isEmpty()) {
                throw new \Exception('该手机号已被其他账号绑定');
            }
            unset($params['code']);
            User::update($params);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 是否有微信授权信息
     * @param $userId
     * @return bool
     * @author 段誉
     * @date 2022/9/20 19:36
     */
    public static function hasWechatAuth(int $userId)
    {
        //是否有微信授权登录
        $terminal = [UserTerminalEnum::WECHAT_MMP, UserTerminalEnum::WECHAT_OA];
        $auth = UserAuth::where(['user_id' => $userId])
            ->whereIn('terminal', $terminal)
            ->findOrEmpty();
        return !$auth->isEmpty();
    }

    /**
     * @notes 我的钱包
     * @param int $userId
     * @return array
     * @author ljj
     * @date 2022/12/12 9:35 上午
     */
    public static function wallet(int $userId): array
    {
        $result = User::where(['id' => $userId])
            ->field('id,user_money,user_earnings')
            ->findOrEmpty()
            ->toArray();
        $result['total_money'] = round($result['user_money'] + $result['user_earnings'],2);
        $result['recharge_open'] = ConfigService::get('recharge', 'recharge_open',1);

        return $result;
    }
}