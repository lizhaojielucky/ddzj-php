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

namespace app\api\validate;


use app\common\enum\GoodsEnum;
use app\common\model\goods\Goods;
use app\common\validate\BaseValidate;

class GoodsValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId',
        'is_collect' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require' => '参数错误',
        'is_collect.require' => '参数缺失',
        'is_collect.in' => '参数值错误',
    ];


    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneCollect()
    {
        return $this->only(['id','is_collect']);
    }

    public function sceneAppointTime()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 检验服务id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/17 5:58 下午
     */
    public function checkId($value,$rule,$data)
    {
        $result = Goods::where('id',$value)->findOrEmpty();
        if ($result->isEmpty()) {
            return '服务不存在';
        }
        if ($result['status'] == GoodsEnum::UNSHELVE) {
            return '服务已下架';
        }
        return true;
    }
}