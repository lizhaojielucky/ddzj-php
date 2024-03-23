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

namespace app\common\model;


use think\model\concern\SoftDelete;

class Crontab extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $name = 'dev_crontab';

    /**
     * @notes 类型获取器
     * @param $value
     * @return string
     * @author Tab
     * @date 2021/8/17 11:03
     */
    public function getTypeDescAttr($value)
    {
        $desc = [
            1 => '定时任务',
            2 => '守护进程',
        ];

        return $desc[$value] ?? '';
    }

    /**
     * @notes 状态获取器
     * @param $value
     * @return string
     * @author Tab
     * @date 2021/8/17 11:04
     */
    public function getStatusDescAttr($value)
    {
        $desc = [
            1 => '运行',
            2 => '停止',
            3 => '错误',
        ];

        return $desc[$value] ?? '';
    }

    /**
     * @notes 最后执行时间获取器
     * @param $value
     * @return string
     * @author Tab
     * @date 2021/8/17 14:35
     */
    public function getLastTimeAttr($value)
    {
        return empty($value) ? '' : date('Y-m-d H:i:s', $value);
    }
}