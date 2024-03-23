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
use app\common\validate\BaseValidate;

class GoodsCategoryValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId',
        'name' => 'require|checkName|max:8',
        'pid' => 'checkPid',
        'sort' => 'number|max:5',
        'is_show' => 'require|in:0,1',
        'is_recommend' => 'in:0,1'
    ];

    protected $message = [
        'id.require' => '参数错误',
        'name.require' => '请输入分类名称',
        'name.max' => '分类名称不能超过八个字',
        'sort.number' => '排序必须为纯数字',
        'sort.max' => '排序最大不能超过五位数',
        'is_show.require' => '请选择状态',
        'is_show.in' => '状态取值范围在[0,1]',
        'is_recommend.in' => '首页显示取值范围在[0,1]',
    ];

    public function sceneAdd()
    {
        return $this->only(['name','pid','sort','is_show','is_recommend']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','name','pid','sort','is_show','is_recommend']);
    }

    public function sceneDel()
    {
        return $this->only(['id'])
            ->append('id','checkDel');
    }

    public function sceneStatus()
    {
        return $this->only(['id','is_show']);
    }

    /**
     * @notes 检验ID
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/8 4:32 下午
     */
    public function checkId($value,$rule,$data)
    {
        $result = GoodsCategory::where('id',$value)->findOrEmpty();
        if ($result->isEmpty()) {
            return '服务分类不存在';
        }
        return true;
    }

    /**
     * @notes 检验分类名称
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/8 4:36 下午
     */
    public function checkName($value,$rule,$data)
    {
        $where[] = ['name','=',$value];
        if (isset($data['id'])) {
            $where[] = ['id','<>',$data['id']];
        }
        $result = GoodsCategory::where($where)->findOrEmpty();
        if (!$result->isEmpty()) {
            return '分类名称已存在，请重新输入';
        }
        return true;
    }

    /**
     * @notes 检验父级分类
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/8 4:59 下午
     */
    public function checkPid($value,$rule,$data)
    {
        if (!isset($value)) {
            return true;
        }

        $level = GoodsCategory::where('id',$value)->value('level');
        if (!$level) {
            return '所选父级分类不存在';
        }
//        if ($level > 2) {
//            return '所选父级分类已经是最大分级';
//        }
        if ($level > 1) {
            return '所选父级分类已经是最大分级';
        }

        //编辑
        if (isset($data['id'])) {
            $category_two = GoodsCategory::where('pid',$data['id'])->find();
//            if ($category_two && $level > 1) {
//                return '所选父级分类超过最大分级';
//            }
            if ($category_two) {
                return '所选父级分类超过最大分级';
            }

//            $category_three = $category_two ? GoodsCategory::where('pid',$category_two['id'])->find() : [];
//            if ($category_three) {
//                return '目前分类已达最大分级，不能选择父级分类';
//            }

            if ($value == $data['id']) {
                return '不能选择自己作为父级';
            }
        }

        return true;
    }

    /**
     * @notes 检验分类能否删除
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/8 6:33 下午
     */
    public function checkDel($value,$rule,$data)
    {
        $result = Goods::where(['category_id'=>$value])->select()->toArray();
        if ($result) {
            return '服务分类正在使用中，无法删除';
        }
        $result = GoodsCategory::where(['pid'=>$value])->select()->toArray();
        if ($result) {
            return '该分类存在下级，无法删除';
        }
        return true;
    }
}