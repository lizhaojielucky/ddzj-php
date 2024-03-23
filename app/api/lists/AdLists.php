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

namespace app\api\lists;


use app\common\enum\AdEnum;
use app\common\enum\MenuEnum;
use app\common\lists\BaseDataLists;
use app\common\model\ad\Ad;
use app\common\model\goods\Goods;

class AdLists extends BaseDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2022/3/25 9:54 上午
     */
    public function where()
    {
        $where[] = ['pid','=',$this->params['pid'] ?? 1];
        $where[] = ['status','=',1];

        return $where;
    }

    /**
     * @notes 广告列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/3/25 9:54 上午
     */
    public function lists(): array
    {
        $where = self::where();

        $lists = Ad::field('id,name,pid,image,link_type,link_address')
            ->where($where)
            ->order(['sort'=>'desc','id'=>'desc'])
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            if ($list['link_type'] == AdEnum::LINK_SHOP) {
                $shop_page = array_column(MenuEnum::SHOP_PAGE,NULL,'index');
                $list['link_address'] = $shop_page[$list['link_address']]['path'];
            }
        }

        return $lists;
    }

    /**
     * @notes 广告数量
     * @return int
     * @author ljj
     * @date 2022/3/25 9:55 上午
     */
    public function count(): int
    {
        $where = self::where();
        return Ad::where($where)->count();
    }
}