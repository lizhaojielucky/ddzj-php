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
use app\common\model\goods\GoodsUnit;
use app\common\validate\BaseValidate;

class GoodsUnitValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId',
        'name' => 'require|checkName',
        'sort' => 'number|max:5',
    ];

    protected $message = [
        'id.require' => '参数错误',
        'name.require' => '请输入单位名称',
        'sort.number' => '排序必须为纯数字',
        'sort.max' => '排序不能大于五位数',
    ];

    public function sceneAdd()
    {
        return $this->only(['name','sort']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','name','sort']);
    }

    public function sceneDel()
    {
        return $this->only(['id'])
            ->append('id','checkDel');
    }

    /**
     * @notes 验证单位名称
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/8 11:30 上午
     */
    public function checkName($value,$rule,$data)
    {
        if (ctype_space($value)) {
            return '师傅名称不能为空';
        }

        $where[] = ['name', '=', $value];
        if (isset($data['id'])) {
            $where[] = ['id', '<>', $data['id']];
        }

        $result = GoodsUnit::where($where)->findOrEmpty();
        if (!$result->isEmpty()) {
            return '单位名称已存在，请重新输入';
        }
        return true;
    }

    /**
     * @notes 验证ID
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/8 11:47 上午
     */
    public function checkId($value,$rule,$data)
    {
        $result = GoodsUnit::where('id',$value)->findOrEmpty();
        if ($result->isEmpty()) {
            return '服务单位不存在';
        }
        return true;
    }

    /**
     * @notes 检验服务单位能否删除
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/8 4:44 下午
     */
    public function checkDel($value,$rule,$data)
    {
        $result = Goods::where(['unit_id'=>$value])->select()->toArray();
        if ($result) {
            return '服务单位已被使用，无法删除';
        }
        return true;
    }
}