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

namespace app\common\logic;

use app\common\enum\AccountLogEnum;
use app\common\model\AccountLog;
use app\common\model\user\User;

class AccountLogLogic extends BaseLogic
{
    /**
     * @notes 添加账户流水记录
     * @param $userId //会员ID
     * @param $changeObject //变动对象
     * @param $changeType //变动类型
     * @param $action //变动动作
     * @param $changeAmount //变动数量
     * @param string $associationSn //关联单号
     * @param string $remark //备注
     * @param array $feature //预留字段，方便存更多其它信息
     * @return bool
     * @author ljj
     * @date 2022/10/28 5:15 下午
     */
    public static function add($userId, $changeObject, $changeType, $action, $changeAmount, $associationSn = '', $remark = '', $feature = [])
    {
        $user = User::findOrEmpty($userId);
        if($user->isEmpty()) {
            return false;
        }

        $left_amount = 0;
        switch ($changeObject) {
            case AccountLogEnum::MONEY:
                $left_amount = $user->user_money;
                break;
        }

        $accountLog = new AccountLog();
        $data = [
            'sn' => generate_sn($accountLog, 'sn', 20),
            'user_id' => $userId,
            'change_object' => $changeObject,
            'change_type' => $changeType,
            'action' => $action,
            'left_amount' => $left_amount,
            'change_amount' => $changeAmount,
            'association_sn' => $associationSn,
            'remark' => $remark,
            'feature' => $feature ? json_encode($feature, JSON_UNESCAPED_UNICODE) : '',
        ];
        return $accountLog->save($data);
    }
}