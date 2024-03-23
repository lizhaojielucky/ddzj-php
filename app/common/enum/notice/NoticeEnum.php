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
namespace app\common\enum\notice;

/**
 * 通知枚举
 * Class NoticeEnum
 * @package app\common\enum
 */
class NoticeEnum
{
    /**
     * 通知类型
     */
    const SYSTEM = 1;
    const SMS = 2;
    const OA = 3;
    const MNP = 4;


    /**
     * 短信验证码场景
     */
    const LOGIN_CAPTCHA = 101;
    const BIND_MOBILE_CAPTCHA = 102;
    const CHANGE_MOBILE_CAPTCHA = 103;
    const FIND_LOGIN_PASSWORD_CAPTCHA = 104;


    /**
     * 短信业务通知
     */
    const ORDER_PAY_NOTICE = 201;//订单付款通知
    const SYSTEM_CANCEL_ORDER_NOTICE = 202;//系统取消订单通知
    const REFUND_SUCCESS_NOTICE = 203;//退款成功通知
    const STAFF_CONFIRM_ORDER_NOTICE = 204;//师傅确认订单通知
    const ORDER_FINISH_NOTICE = 205;//订单完成通知
    const ORDER_PAY_NOTICE_PLATFORM = 206;//订单付款通知平台
    const ORDER_WAIT_CONFIRM_NOTICE_STAFF = 207;//订单待确认服务通知师傅
    const ORDER_NO_DISPATCH_NOTICE_PLATFORM = 208;//订单未派单通知平台
    const ORDER_DISPATCH_NOTICE_PLATFORM = 209;//订单派单成功通知平台
    const ORDER_CANCEL_DISPATCH_NOTICE_STAFF = 210;//平台取消派单通知师傅


    /**
     * 验证码场景
     */
    const SMS_SCENE = [
        self::LOGIN_CAPTCHA,
        self::BIND_MOBILE_CAPTCHA,
        self::CHANGE_MOBILE_CAPTCHA,
        self::FIND_LOGIN_PASSWORD_CAPTCHA,
    ];


    //通知类型
    const BUSINESS_NOTIFICATION = 1;//业务通知
    const VERIFICATION_CODE = 2;//验证码


    /**
     * @notes 通知类型
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2022/2/17 2:49 下午
     */
    public static function getTypeDesc($value = true)
    {
        $data = [
            self::BUSINESS_NOTIFICATION => '业务通知',
            self::VERIFICATION_CODE => '验证码'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }


    /**
     * @notes 获取场景描述
     * @param $sceneId
     * @param false $flag
     * @return string|string[]
     * @author 段誉
     * @date 2022/3/29 11:33
     */
    public static function getSceneDesc($sceneId, $flag = false)
    {
        $desc = [
            self::LOGIN_CAPTCHA => '登录验证码',
            self::BIND_MOBILE_CAPTCHA => '绑定手机验证码',
            self::CHANGE_MOBILE_CAPTCHA => '变更手机验证码',
            self::FIND_LOGIN_PASSWORD_CAPTCHA => '找回登录密码验证码',
            self::ORDER_PAY_NOTICE => '订单付款通知',
            self::SYSTEM_CANCEL_ORDER_NOTICE => '系统取消订单通知',
            self::REFUND_SUCCESS_NOTICE => '退款成功通知',
            self::STAFF_CONFIRM_ORDER_NOTICE => '师傅确认订单通知',
            self::ORDER_FINISH_NOTICE => '订单完成通知',
            self::ORDER_PAY_NOTICE_PLATFORM => '订单付款通知',
            self::ORDER_WAIT_CONFIRM_NOTICE_STAFF => '订单待确认服务通知',
            self::ORDER_NO_DISPATCH_NOTICE_PLATFORM => '订单未派单通知',
            self::ORDER_DISPATCH_NOTICE_PLATFORM => '订单派单成功通知',
            self::ORDER_CANCEL_DISPATCH_NOTICE_STAFF => '平台取消派单通知',
        ];

        if ($flag) {
            return $desc;
        }

        return $desc[$sceneId] ?? '';
    }


    /**
     * @notes 更具标记获取场景
     * @param $tag
     * @return int|string
     * @author 段誉
     * @date 2022/9/15 15:08
     */
    public static function getSceneByTag($tag)
    {
        $scene = [
            // 手机验证码登录
            'YZMDL' => self::LOGIN_CAPTCHA,
            // 绑定手机号验证码
            'BDSJHM' => self::BIND_MOBILE_CAPTCHA,
            // 变更手机号验证码
            'BGSJHM' => self::CHANGE_MOBILE_CAPTCHA,
            // 找回登录密码
            'ZHDLMM' => self::FIND_LOGIN_PASSWORD_CAPTCHA,
        ];
        return $scene[$tag] ?? '';
    }


    /**
     * @notes 获取场景变量
     * @param $sceneId
     * @param false $flag
     * @return array|string[]
     * @author 段誉
     * @date 2022/3/29 11:33
     */
    public static function getVars($sceneId, $flag = false)
    {
        $desc = [
            self::LOGIN_CAPTCHA => '验证码:code',
            self::BIND_MOBILE_CAPTCHA => '验证码:code',
            self::CHANGE_MOBILE_CAPTCHA => '验证码:code',
            self::FIND_LOGIN_PASSWORD_CAPTCHA => '验证码:code',
            self::ORDER_PAY_NOTICE => '用户昵称:user_name 订单编号:order_sn',
            self::SYSTEM_CANCEL_ORDER_NOTICE => '用户昵称:user_name 订单编号:order_sn',
            self::REFUND_SUCCESS_NOTICE => '用户昵称:nickname 订单编号:order_sn',
            self::STAFF_CONFIRM_ORDER_NOTICE => '用户昵称:nickname 订单编号:order_sn',
            self::ORDER_FINISH_NOTICE => '用户昵称:nickname 订单编号:order_sn',
            self::ORDER_PAY_NOTICE_PLATFORM => '订单编号:order_sn',
            self::ORDER_WAIT_CONFIRM_NOTICE_STAFF => '师傅名称:staff_name',
            self::ORDER_NO_DISPATCH_NOTICE_PLATFORM => '订单编号:order_sn',
            self::ORDER_DISPATCH_NOTICE_PLATFORM => '订单编号:order_sn 师傅名称:staff_name 时间:time',
            self::ORDER_CANCEL_DISPATCH_NOTICE_STAFF => '师傅名称:staff_name',
        ];

        if ($flag) {
            return $desc;
        }

        return isset($desc[$sceneId]) ? ['可选变量 ' . $desc[$sceneId]] : [];
    }


    /**
     * @notes 获取系统通知示例
     * @param $sceneId
     * @param false $flag
     * @return array|string[]
     * @author 段誉
     * @date 2022/3/29 11:33
     */
    public static function getSystemExample($sceneId, $flag = false)
    {
        $desc = [];

        if ($flag) {
            return $desc;
        }

        return isset($desc[$sceneId]) ? [$desc[$sceneId]] : [];
    }


    /**
     * @notes 获取短信通知示例
     * @param $sceneId
     * @param false $flag
     * @return array|string[]
     * @author 段誉
     * @date 2022/3/29 11:33
     */
    public static function getSmsExample($sceneId, $flag = false)
    {
        $desc = [
            self::LOGIN_CAPTCHA => '您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。',
            self::BIND_MOBILE_CAPTCHA => '您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。',
            self::CHANGE_MOBILE_CAPTCHA => '您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。',
            self::FIND_LOGIN_PASSWORD_CAPTCHA => '您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。',
            self::ORDER_PAY_NOTICE => '亲爱的${nickname}，您的订单${order_sn}已支付成功，商家正在快马加鞭为您安排发货。',
            self::SYSTEM_CANCEL_ORDER_NOTICE => '亲爱的${user_name}，您的订单${order_sn}已被系统取消。',
            self::REFUND_SUCCESS_NOTICE => '亲爱的${nickname}，您的订单${order_sn}已退款成功。',
            self::STAFF_CONFIRM_ORDER_NOTICE => '亲爱的${nickname}，您的订单${order_sn}已被师傅确认服务。',
            self::ORDER_FINISH_NOTICE => '亲爱的${nickname}，您的订单${order_sn}已完成。',
            self::ORDER_PAY_NOTICE_PLATFORM => '亲爱的商家，您有新的订单，订单号${order_sn}，请及时处理。',
            self::ORDER_WAIT_CONFIRM_NOTICE_STAFF => '亲爱的${staff_name}，您有新的订单待确认服务，请前往移动商城点击确认！',
            self::ORDER_NO_DISPATCH_NOTICE_PLATFORM => '您好，您有新的订单${order_sn}未派单，请登录后台及时处理！',
            self::ORDER_DISPATCH_NOTICE_PLATFORM => '您好，您有新的订单${order_sn}派单成功，将由${staff_name}于${time}提供上门服务',
            self::ORDER_CANCEL_DISPATCH_NOTICE_STAFF => '亲爱的${staff_name}，系统已为您取消派单，您无需操作！',
        ];

        if ($flag) {
            return $desc;
        }

        return isset($desc[$sceneId]) ? ['示例：' . $desc[$sceneId]] : [];
    }


    /**
     * @notes 获取公众号模板消息示例
     * @param $sceneId
     * @param false $flag
     * @return array|string[]|\string[][]
     * @author 段誉
     * @date 2022/3/29 11:33
     */
    public static function getOaExample($sceneId, $flag = false)
    {
        $desc = [];

        if ($flag) {
            return $desc;
        }

        return $desc[$sceneId] ?? [];
    }


    /**
     * @notes 获取小程序订阅消息示例
     * @param $sceneId
     * @param false $flag
     * @return array|mixed
     * @author 段誉
     * @date 2022/3/29 11:33
     */
    public static function getMnpExample($sceneId, $flag = false)
    {
        $desc = [];

        if ($flag) {
            return $desc;
        }

        return $desc[$sceneId] ?? [];
    }


    /**
     * @notes 提示
     * @param $type
     * @param $sceneId
     * @return array|string|string[]|\string[][]
     * @author 段誉
     * @date 2022/3/29 11:33
     */
    public static function getOperationTips($type, $sceneId)
    {
        // 场景变量
        $vars = self::getVars($sceneId);
        // 其他提示
        $other = [];
        // 示例
        switch ($type) {
            case self::SYSTEM:
                $example = self::getSystemExample($sceneId);
                break;
            case self::SMS:
                $other[] = '生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板 3、若是腾讯云模板变量名须换成变量名出现顺序对应的数字(例：您好{nickname},您的订单{order_sn}已发货! 须改为 您好{1},您的订单{2}已发货!)';
                $example = self::getSmsExample($sceneId);
                break;
            case self::OA:
                $other[] = '配置路径：公众号后台 > 广告与服务 > 模板消息';
                $other[] = '推荐行业：主营行业：IT科技/互联网|电子商务 副营行业：消费品/消费品';
                $example = self::getOaExample($sceneId);
                break;
            case self::MNP:
                $other[] = '配置路径：小程序后台 > 功能 > 订阅消息';
                $example = self::getMnpExample($sceneId);
                break;
        }
        $tips = array_merge($vars, $example, $other);

        return $tips;
    }
}