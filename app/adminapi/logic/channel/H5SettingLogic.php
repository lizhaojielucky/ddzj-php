<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\adminapi\logic\channel;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

/**
 * H5设置逻辑层
 * Class HFiveSettingLogic
 * @package app\adminapi\logic\settings\h5
 */
class H5SettingLogic extends BaseLogic
{
    /**
     * @notes 获取H5设置
     * @return array
     * @author ljj
     * @date 2022/9/23 9:38 上午
     */
    public static function getConfig()
    {
        $config = [
            'status' => ConfigService::get('h5', 'status',1),
            'close_page' => ConfigService::get('h5', 'close_page',1),
            'close_url' => ConfigService::get('h5', 'close_url',''),
        ];

        return $config;
    }

    /**
     * @notes H5设置
     * @param $params
     * @author ljj
     * @date 2022/9/23 10:02 上午
     */
    public static function setConfig($params)
    {
        $params['close_url'] = $params['close_url'] ?? '';
        ConfigService::set('h5', 'status', $params['status']);
        ConfigService::set('h5', 'close_page', $params['close_page']);
        ConfigService::set('h5', 'close_url', $params['close_url']);
        // 恢复原入口
        if(file_exists('./mobile/index_lock.html')) {
            // 存在则原商城入口被修改过，先清除修改后的入口
            unlink('./mobile/index.html');
            // 恢复原入口
            rename('./mobile/index_lock.html', './mobile/index.html');
        }

        // H5商城关闭 且 显示空白页
        if($params['status'] == 0 && $params['close_page'] == 1) {
            // 变更文件名
            rename('./mobile/index.html', './mobile/index_lock.html');
            // 创建新空白文件
            $newfile = fopen('./mobile/index.html', 'w');
            fclose($newfile);
        }

        // H5商城关闭 且 跳转指定页
        if($params['status'] == 0 && $params['close_page'] == 2 && !empty($params['close_url'])) {
            // 变更文件名
            rename('./mobile/index.html', './mobile/index_lock.html');
            // 创建重定向文件
            $newfile = fopen('./mobile/index.html', 'w');
            $content = '<script>window.location.href = "' . $params['close_url'] . '";</script>';
            fwrite($newfile, $content);
            fclose($newfile);
        }
    }
}