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


use app\common\validate\BaseValidate;

class UserAddressValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'contact' => 'require',
        'mobile' => 'require|mobile',
        'province_id' => 'require',
        'city_id' => 'require',
        'district_id' => 'require',
        'is_default' => 'in:0,1',
        'longitude'=>'require',
        'latitude'=>'require',
    ];


    protected $message = [
        'id.require' => '参数错误',
        'contact.require' => '请输入联系人',
        'mobile.require' => '请输入手机号码',
        'mobile.mobile' => '手机号码格式不正确',
        'province_id.require' => '请选择省',
        'city_id.require' => '请选择市',
        'district_id.require' => '请选择区',
        'is_default.in' => '设为默认项的取值范围是[0,1]',
        'longitude.require' => '经度不能为空',
        'latitude.require' => '纬度不能为空',
    ];


    public function sceneAdd()
    {
        return $this->only(['contact','mobile','province_id','city_id','district_id','is_default','longitude','latitude']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','contact','mobile','province_id','city_id','district_id','is_default','longitude','latitude']);
    }
}