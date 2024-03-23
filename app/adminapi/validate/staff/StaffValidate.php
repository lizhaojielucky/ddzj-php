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

namespace app\adminapi\validate\staff;


use app\common\enum\OrderEnum;
use app\common\model\order\Order;
use app\common\model\staff\Staff;
use app\common\model\user\User;
use app\common\validate\BaseValidate;

class StaffValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkId',
        'user_id' => 'require|checkUser',
//        'sn' => 'require|checkSn',
        'name' => 'require|checkName',
        'sex' => 'require|in:1,2',
        'mobile' => 'require|mobile',
        'goods_ids' => 'require|array',
        'province_id' => 'require|number',
        'city_id' => 'require|number',
        'district_id' => 'require|number',
//        'longitude' => 'require',
//        'latitude' => 'require',
        'status' => 'require|in:0,1',
        'is_recommend' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require' => '参数错误',
        'user_id.require' => '请选择绑定用户',
        'require.require' => '请输入师傅编号',
        'name.require' => '请输入师傅姓名',
        'sex.require' => '请选择性别',
        'sex.in' => '性别取值范围在[1,2]',
        'mobile.require' => '请输入手机号码',
        'mobile.mobile' => '手机号码格式不正确',
        'goods_ids.require' => '请选择服务项目',
        'goods_ids.array' => '服务项目格式不正确',
        'province_id.require' => '请选择省',
        'province_id.number' => '省只能为纯数字',
        'city_id.require' => '请选择市',
        'city_id.number' => '市只能为纯数字',
        'district_id.require' => '请选择区',
        'district_id.number' => '区只能为纯数字',
//        'longitude.require' => '请输入经度',
//        'latitude.require' => '请输入纬度',
        'status.require' => '请选择状态',
        'status.in' => '状态选择范围在[0,1]',
        'is_recommend.require' => '请选择首页推荐',
        'is_recommend.in' => '首页推荐取值范围在[0,1]',
    ];

    public function sceneAdd()
    {
        return $this->only(['user_id','name','sex','mobile','goods_ids','province_id','city_id','district_id','status','is_recommend']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','user_id','name','sex','mobile','goods_ids','province_id','city_id','district_id','status','is_recommend']);
    }

    public function sceneDel()
    {
        return $this->only(['id'])
            ->append('id','checkDel');
    }

    public function sceneStatus()
    {
        return $this->only(['id','status']);
    }

    /**
     * @notes 检验师傅id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/10 3:35 下午
     */
    public function checkId($value,$rule,$data)
    {
        $result = Staff::where(['id'=>$value])->findOrEmpty();
        if ($result->isEmpty()) {
            return '师傅不存在';
        }
        return true;
    }

    /**
     * @notes 检验用户id
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/10 3:29 下午
     */
    public function checkUser($value,$rule,$data)
    {
        $result = User::where(['id'=>$value])->findOrEmpty();
        if ($result->isEmpty()) {
            return '用户不存在，请重新选择';
        }

        $where[] = ['user_id','=',$value];
        if (isset($data['id'])) {
            $where[] = ['id','<>',$data['id']];
        }
        $result = Staff::where($where)->findOrEmpty();
        if (!$result->isEmpty()) {
            return '该用户已被绑定，请重新选择';
        }
        return true;
    }

    /**
     * @notes 检验师傅名称
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/2/10 3:32 下午
     */
    public function checkName($value,$rule,$data)
    {
        if (ctype_space($value)) {
            return '师傅名称不能为空';
        }
        $where[] = ['name','=',$value];
        if (isset($data['id'])) {
            $where[] = ['id','<>',$data['id']];
        }
        $result = Staff::where($where)->findOrEmpty();
        if (!$result->isEmpty()) {
            return '师傅名称已存在，请重新输入';
        }
        return true;
    }

    /**
     * @notes 检验能否删除师傅
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/4/1 3:57 下午
     */
    public function checkDel($value,$rule,$data)
    {
        $result = Order::where(['staff_id'=>$value])->select()->toArray();
        if ($result) {
            return '该师傅已有关联订单，不允许删除';
        }
        return true;
    }


    /**
     * @notes 校验师傅编号
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2022/9/30 3:13 下午
     */
    public function checkSn($value,$rule,$data)
    {
        $where[] = ['sn','=',$value];
        if (isset($data['id'])) {
            $where[] = ['id','<>',$data['id']];
        }
        $result = Staff::where($where)->findOrEmpty();
        if (!$result->isEmpty()) {
            return '师傅编号已存在，请重新输入';
        }
        return true;
    }
}