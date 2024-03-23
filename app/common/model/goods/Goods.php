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

namespace app\common\model\goods;


use app\common\enum\GoodsEnum;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Goods extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


    /**
     * @notes 关联服务轮播图
     * @return \think\model\relation\HasMany
     * @author ljj
     * @date 2022/2/9 3:37 下午
     */
    public function goodsImage()
    {
        return $this->hasMany(GoodsImage::class,'goods_id','id');
    }

    /**
     * @notes 关联服务评价模型
     * @return \think\model\relation\HasMany
     * @author ljj
     * @date 2022/2/17 6:15 下午
     */
    public function goodsComment()
    {
        return $this->hasMany(GoodsComment::class,'goods_id','id')->append(['goods_comment_image','user']);
    }


    /**
     * @notes 获取分类名称
     * @param $value
     * @param $data
     * @return mixed|string
     * @author ljj
     * @date 2022/2/9 11:15 上午
     */
    public function getCategoryDescAttr($value,$data)
    {
        $category_arr = (new GoodsCategory())->column('name,pid','id');
        $category_name = '未知';
        $category_first = $category_arr[$data['category_id']] ?? [];
        if ($category_first) {
            $category_name = $category_first['name'];
            $category_second = $category_arr[$category_first['pid']] ?? [];
            if ($category_second) {
                $category_name = $category_second['name'].'/'.$category_name;
                $category_third = $category_arr[$category_second['pid']] ?? [];
                if ($category_third) {
                    $category_name = $category_third['name'].'/'.$category_name;
                }
            }
        }

        return $category_name;
    }

    /**
     * @notes 获取单位名称
     * @param $value
     * @param $data
     * @return mixed|string
     * @author ljj
     * @date 2022/2/9 11:17 上午
     */
    public function getUnitDescAttr($value,$data)
    {
        return GoodsUnit::where('id',$data['unit_id'])->value('name') ?? '未知';
    }

    /**
     * @notes 获取状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2022/2/9 11:22 上午
     */
    public function getStatusDescAttr($value,$data)
    {
        return GoodsEnum::getShowDesc($data['status']);
    }


    /**
     * @notes 分类搜索器
     * @param $query
     * @param $value
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/17 5:11 下午
     */
    public function searchCategoryIdAttr($query, $value, $data)
    {
        if ($value) {
            $goodsCategory = GoodsCategory::find($value);
            $level = $goodsCategory['level'] ?? '';
            $categoryIds = [];
            switch ($level){
                case 1:
                    $categoryIds = GoodsCategory::where(['pid'=>$value])
                        ->column('id');
                    Array_push($categoryIds,$value);
                    break;
                case 2:
                    $categoryIds = [$value];
                    break;
            }
            $goodsIds = Goods::where(['category_id' => $categoryIds])->column('id');
            $query->where('id', 'in', $goodsIds);
        }
    }

    /**
     * @notes 关键词搜索器
     * @param $query
     * @param $value
     * @param $data
     * @author ljj
     * @date 2022/2/17 5:16 下午
     */
    public function searchKeywordAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('name', 'like', '%'.$value.'%');
        }
    }
}