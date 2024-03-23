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

namespace app\adminapi\logic\setting\pay;


use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\pay\PayConfig;

class PayConfigLogic extends BaseLogic
{
    /**
     * @notes 查看支付配置列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/15 6:03 下午
     */
    public function lists()
    {
        $lists = PayConfig::field('id,name,pay_way,image,sort')
            ->order(['sort'=>'asc','id'=>'desc'])
            ->append(['pay_way_desc'])
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 编辑支付配置
     * @param $params
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/15 6:13 下午
     */
    public function edit($params)
    {
        $pay_config = PayConfig::find($params['id']);
        $config = '';
        if ($pay_config['pay_way'] == PayEnum::WECHAT_PAY) {
            $config = [
                'interface_version' => $params['interface_version'],
                'merchant_type' => $params['merchant_type'],
                'mch_id' => $params['mch_id'],
                'pay_sign_key' => $params['pay_sign_key'],
                'apiclient_cert' => $params['apiclient_cert'],
                'apiclient_key' => $params['apiclient_key'],
            ];
        }
        if ($pay_config['pay_way'] == PayEnum::ALI_PAY) {
            $config = [
                'mode' => $params['mode'] ?? 'normal_mode',
                'merchant_type' => $params['merchant_type'],
                'app_id' => $params['app_id'],
                'private_key' => $params['private_key'],
                'ali_public_key' => $params['ali_public_key'],
            ];
        }
        $pay_config->name = $params['name'];
        $pay_config->image = $params['image'];
        $pay_config->sort = $params['sort'];
        $pay_config->config = $config ? json_encode($config) : '';
        return $pay_config->save();
    }

    /**
     * @notes 支付配置详情
     * @param $id
     * @return array
     * @author ljj
     * @date 2022/2/15 6:28 下午
     */
    public function detail($id)
    {
        return PayConfig::where('id',$id)->json(['config'])->append(['pay_way_desc'])->findOrEmpty()->toArray();
    }
}