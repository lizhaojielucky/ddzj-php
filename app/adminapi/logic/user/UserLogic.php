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

namespace app\adminapi\logic\user;


use app\common\enum\AccountLogEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\user\User;
use think\facade\Db;

class UserLogic extends BaseLogic
{
    /**
     * @notes 用户详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/4/21 2:28 下午
     */
    public static function detail($id)
    {
        $result = User::where('id',$id)
            ->field('id,sn,nickname,avatar,mobile,sex,real_name,login_time,channel as register_source,create_time,account,user_money')
            ->append(['sex_desc','source_desc'])
            ->findOrEmpty()
            ->toArray();

        if (!empty($result)) {
            $result['login_time'] = $result['login_time'] ? date('Y-m-d H:i:s',$result['login_time']) : '-';
        }

        return $result;
    }

    /**
     * @notes 修改用户信息
     * @param $params
     * @return bool
     * @author ljj
     * @date 2022/8/10 4:54 下午
     */
    public function editInfo($params):bool
    {
        User::where(['id'=>$params['id']])->update([$params['field']=>$params['value']]);
        return true;
    }

    /**
     * @notes 调整余额
     * @param array $params
     * @return bool|string
     * @author ljj
     * @date 2023/4/12 11:58 上午
     */
    public function adjustUserWallet(array $params)
    {
        Db::startTrans();
        try {
            $user = User::find($params['id']);
            //增加
            if(1 == $params['adjust_action']){
                //调整可用余额
                $user->user_money = $user->user_money + $params['adjust_num'];
                $user->save();
                //流水日志
                AccountLogLogic::add($user->id,AccountLogEnum::MONEY,AccountLogEnum::ADMIN_INC_MONEY,AccountLogEnum::INC,$params['adjust_num']);
            }else{
                $user->user_money = $user->user_money - $params['adjust_num'];
                $user->save();
                //流水日志
                AccountLogLogic::add($user->id,AccountLogEnum::MONEY,AccountLogEnum::ADMIN_DEC_MONEY,AccountLogEnum::DEC,$params['adjust_num']);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}