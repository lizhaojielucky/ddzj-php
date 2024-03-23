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

namespace app\common\model\user;


use app\common\enum\user\UserEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

class User extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    //关联用户授权模型
    public function userAuth()
    {
        return $this->hasOne(UserAuth::class, 'user_id');

    }

    /**
     * @notes 不全用户头像路径
     * @param $value
     * @return string|null
     * @author ljj
     * @date 2022/2/7 6:45 下午
     */
    public function getAvatarAttr($value)
    {
        return $value ? FileService::getFileUrl($value) : $value;
    }


    /**
     * @notes 获取用户性别
     * @param $value
     * @return string|string[]
     * @author ljj
     * @date 2022/2/7 6:51 下午
     */
    public function getSexDescAttr($value,$data)
    {
        return UserEnum::getSexDesc($data['sex']);
    }


    /**
     * @notes 注册来源
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/2/8 9:30 上午
     */
    public function getSourceDescAttr($value,$data)
    {
        return UserTerminalEnum::getTermInalDesc($data['register_source']);
    }



    /**
     * @notes 生成用户编码
     * @param string $prefix
     * @param int $length
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/16 10:33
     */
    public static function createUserSn($prefix = '', $length = 8)
    {
        $rand_str = '';
        for ($i = 0; $i < $length; $i++) {
            $rand_str .= mt_rand(0, 9);
        }
        $sn = $prefix . $rand_str;
        if (User::where(['sn' => $sn])->find()) {
            return self::createUserSn($prefix, $length);
        }
        return $sn;
    }
}