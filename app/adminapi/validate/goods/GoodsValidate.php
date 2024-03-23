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

namespace app\adminapi\validate\goods;


use app\common\model\goods\Goods;
use app\common\model\goods\GoodsCategory;
use app\common\model\goods\GoodsUnit;
use app\common\model\staff\Staff;
use app\common\validate\BaseValidate;

class GoodsValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId',
        'name' => 'require|max:64|checkName',
        'category_id' => 'require|checkCategory',
        'goods_image' => 'require|array|max:10',
        'price' => 'require|float|egt:0',
        'scribing_price' => 'float|egt:0',
        'unit_id' => 'require|checkUnit',
        'status' => 'require|in:0,1',
        'ids' => 'require|array',
    ];

    protected $message = [
        'id.require' => '参数错误',
        'name.require' => '请输入服务名称',
        'name.max' => '服务名称已超过限制字数',
        'category_id.require' => '请选择服务分类',
        'goods_image.require' => '请上传轮播图',
        'goods_image.array' => '轮播图格式不正确',
        'goods_image.max' => '轮播图数量不能大于10张',
        'price.require' => '请输入价格',
        'price.float' => '价格必须为浮点数',
        'price.egt' => '价格必须大于或等于零',
        'scribing_price.float' => '划线价必须为浮点数',
        'scribing_price.egt' => '划线价必须大于或等于零',
        'unit_id.require' => '请选择单位',
        'status.require' => '请选择服务状态',
        'status.in' => '服务状态取值范围在[0,1]',
        'ids.require' => '请选择服务',
        'ids.array' => '参数格式错误',
    ];

    public function sceneAdd()
    {
        return $this->only(['name','category_id','goods_image','price','scribing_price','unit_id','status']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','name','category_id','goods_image','price','scribing_price','unit_id','status']);
    }

    public function sceneDel()
    {
        return $this->only(['ids'])
            ->append('ids','checkDel');
    }

    public function sceneStatus()
    {
        return $this->only(['ids','status']);
    }


    /**
     * @notes 检验服务ID
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/9 12:02 下午
     */
    public function checkId($value,$rule,$data)
    {
        $result = Goods::where(['id'=>$value])->findOrEmpty();
        if ($result->isEmpty()) {
            return '服务不存在';
        }

        return true;
    }

    /**
     * @notes 检验服务名称
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/9 12:04 下午
     */
    public function checkName($value,$rule,$data)
    {
        $where[] = ['name','=',$value];
        if (isset($data['id'])) {
            $where[] = ['id','<>',$data['id']];
        }
        $result = Goods::where($where)->findOrEmpty();
        if (!$result->isEmpty()) {
            return '服务名称已存在，请重新输入';
        }
        return true;
    }

    /**
     * @notes 检验服务分类id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/9 12:06 下午
     */
    public function checkCategory($value,$rule,$data)
    {
        $result = GoodsCategory::where(['id'=>$value])->findOrEmpty();
        if ($result->isEmpty()) {
            return '服务分类不存在，请重新选择';
        }
        return true;
    }

    /**
     * @notes 检验服务单位id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/9 12:06 下午
     */
    public function checkUnit($value,$rule,$data)
    {
        $result = GoodsUnit::where(['id'=>$value])->findOrEmpty();
        if ($result->isEmpty()) {
            return '服务单位不存在，请重新选择';
        }
        return true;
    }

    /**
     * @notes 检验服务能否被删除
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/4/1 3:37 下午
     */
    public function checkDel($value,$rule,$data)
    {
//        $goods = Goods::column('name','id');
//        foreach ($value as $val) {
//            $result = Staff::whereRaw("FIND_IN_SET($val,goods_ids)")->select()->toArray();
//            if ($result) {
//                return '服务：'.$goods[$val].'已被师傅绑定，无法删除';
//            }
//        }
        return true;
    }

}