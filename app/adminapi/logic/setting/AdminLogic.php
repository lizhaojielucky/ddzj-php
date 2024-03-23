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

namespace app\adminapi\logic\setting;


use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use think\facade\Config;

class AdminLogic extends BaseLogic
{
    /**
     * @notes 获取个人资料
     * @param $admin_id
     * @return array
     * @author ljj
     * @date 2022/4/18 2:36 下午
     */
    public static function getAdmin($admin_id)
    {
        $admin = Admin::where('id',$admin_id)->field('id,name,avatar,account')->findOrEmpty()->toArray();

        return $admin;
    }

    /**
     * @notes 设置个人资料
     * @param $params
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/4/18 2:58 下午
     */
    public static function setAdmin($params)
    {
        $admin = Admin::find($params['admin_id']);
        $admin->name = $params['name'];
        $admin->avatar = $params['avatar'];
        $admin->account = $params['account'];
        if (isset($params['password']) && $params['password'] != '') {
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['new_password'], $passwordSalt);
            $admin->password = $password;
        }
        $admin->save();

        return true;
    }
}