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


use app\common\model\goods\Goods;
use app\common\model\order\OrderGoods;
use app\common\validate\BaseValidate;

class GoodsCommentValidate extends BaseValidate
{
    protected $rule = [
        'goods_id' => 'require|checkGoodsId',
        'order_goods_id' => 'require|checkOrderGoodsId',
        'service_comment' => 'require|number|in:1,2,3,4,5',
        'image' => 'array',
    ];

    protected $message = [
        'goods_id.require' => '参数错误',
        'order_goods_id.require' => '参数错误',
        'service_comment.require' => '请给服务评分',
        'service_comment.number' => '服务评分值错误',
        'service_comment.in' => '请给服务评分',
        'image.array' => '图片数据必须为数组结构',
    ];

    public function sceneCommentCategory()
    {
        return $this->only(['goods_id']);
    }

    public function sceneCommentGoodsInfo()
    {
        return $this->only(['order_goods_id']);
    }

    public function sceneAdd()
    {
        return $this->only(['order_goods_id','service_comment','image']);
    }


    /**
     * @notes 检查商品ID
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/18 12:05 下午
     */
    public function checkGoodsId($value,$rule,$data)
    {
        $result = Goods::where('id', $value)->findOrEmpty();
        if ($result->isEmpty()) {
            return '商品不存在';
        }

        return true;
    }

    /**
     * @notes 检验订单商品id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/21 6:06 下午
     */
    public function checkOrderGoodsId($value,$rule,$data)
    {
        $result = OrderGoods::where('id', $value)->findOrEmpty();
        if ($result->isEmpty()) {
            return '订单商品不存在';
        }

        return true;
    }
}