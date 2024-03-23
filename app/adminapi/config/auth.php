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


/**
 * 权限
 */
return [
    // 首页
    'index' => [
        //控制台
        'index' => [
            'page_path' => '/workbench',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['workbench/index'],
            ],
        ]
    ],
    // 服务管理
    'service' => [
        //服务列表
        'lists' => [
            'page_path' => '/service/lists',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['goods.goods/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'goods.goods/add',
                    'goods.goods/edit',
                    'goods.goods/detail',
                    'goods.goods/del',
                ],
            ],
        ],
        //服务分类
        'category' => [
            'page_path' => '/service/category',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['goods.goods_category/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'goods.goods_category/add',
                    'goods.goods_category/edit',
                    'goods.goods_category/detail',
                    'goods.goods_category/del',
                ],
            ],
        ],
        //服务单位
        'unit' => [
            'page_path' => '/service/unit',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['goods.goods_unit/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'goods.goods_unit/add',
                    'goods.goods_unit/edit',
                    'goods.goods_unit/detail',
                    'goods.goods_unit/del',
                ],
            ],
        ],
        //服务评价
        'comment' => [
            'page_path' => '/service/evaluate',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['goods.goods_comment/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'goods.goods_comment/detail',
                    'goods.goods_comment/del',
                    'goods.goods_comment/reply',
                ],
            ],
        ],
    ],
    // 用户管理
    'user' => [
        // 用户列表
        'lists' => [
            'page_path' => '/user/index',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['user.user/lists'],
            ],
        ],
        // 用户详情
        'lists' => [
            'page_path' => '/user/detail',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['user.user/detail'],
            ],
        ]
    ],
    // 师傅管理
    'staff' => [
        // 师傅列表
        'lists' => [
            'page_path' => '/master_worker/index',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['staff.staff/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'staff.staff/add',
                    'staff.staff/edit',
                    'staff.staff/detail',
                    'staff.staff/del',
                ],
            ],
        ]
    ],
    // 订单管理
    'order' => [
        // 订单列表
        'lists' => [
            'page_path' => '/order/index',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['order.order/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'order.order/del',
                    'order.order/remark',
                    'order.order/cancel',
                    'order.order/detail',
                    'order.order/verification',
                ],
            ]
        ],
        // 预约时间段
        'subscribe' => [
            'page_path' => '/order/subscribe',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['order.order_time/lists', 'order.order_time/getTime'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'order.order_time/add',
                    'order.order_time/edit',
                    'order.order_time/detail',
                    'order.order_time/del',
                ],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'order.order_time/setTime'
                ],
            ],
        ]
    ],
    // 装修管理
    'decorate' => [
        // 首页装修
        'index' => [
            'page_path' => '/decorate/index',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['decorate.menu/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'decorate.menu/status',
                    'decorate.menu/del',
                    'decorate.menu/edit',
                    'decorate.menu/detail',
                    'decorate.menu/shopPage',
                    'goods.goods_category/lists'
                ],
            ]
        ],
        // 底部导航
        'navigation' => [
            'page_path' => '/decorate/tabbar',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['decorate.navigation/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'decorate.navigation/detail',
                    'decorate.navigation/edit'
                ],
            ]
        ],
        // 广告管理
        'ad' => [
            'page_path' => '/decorate/advertise',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['ad.ad_position/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'ad.ad/status',
                    'ad.ad/del',
                    'ad.ad/add',
                    'ad.ad/edit',
                    'ad.ad/detail'
                ],
            ]
        ]
    ],
    // 应用管理 消息通知
    'notice' => [
        // 通知设置
        'notice_setting' => [
            'page_path' => '/notification/index',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['notice.notice/settingLists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'notice.notice/detail',
                    'notice.notice/set',
                ],
            ]
        ],
        // 短信通知
        'massage_setting' => [
            'page_path' => '/sms/index',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['notice.sms_config/getConfig'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'notice.sms_config/detail',
                    'notice.sms_config/setConfig',
                ],
            ]
        ],
    ],
    //渠道
    'channel'   => [
        //微信公众号设置
        'oasetting'   => [
            'page_path'     => '/channel/mp_wechat/index',
            'view'      => [
                'button_auth'   => ['view'],
                'action_auth'   => ['channel.official_account_setting/getConfig'],
            ],
            'save'      => [
                'button_auth'   => ['auth_all'],
                'action_auth'   => ['channel.official_account_setting/setConfig'],
            ],
        ],
        //微信菜单设置
        'oamenu'   => [
            'page_path'     => '/channel/mp_wechat/menu',
            'view'      => [
                'button_auth'   => ['view'],
                'action_auth'   => ['channel.official_account_menu/detail'],
            ],
            'save'      => [
                'button_auth'   => ['auth_all'],
                'action_auth'   => [
                    'channel.official_account_menu/saveAndPublish',
                    'channel.official_account_menu/save'
                ],
            ],
        ],
        //关注回复
        'oafollowreply'   => [
            'page_path'     => ['channel/mp_wechat/reply/follow_reply','/channel/mp_wechat/reply/keyword_reply','/channel/mp_wechat/reply/default_reply'],
            'view'      => [
                'button_auth'   => ['view'],
                'action_auth'   => ['channel.official_account_reply/lists'],
            ],
            'manage'        => [
                'button_auth'   => ['auth_all'],
                'action_auth'   => [
                    'channel.official_account_reply/add',
                    'channel.official_account_reply/edit',
                    'channel.official_account_reply/status',
                    'channel.official_account_reply/detail',
                    'channel.official_account_reply/del',
                ]
            ],
        ],
        //小程序设置
        'mpsetting'   => [
            'page_path'     => '/channel/wechat_app',
            'view'      => [
                'button_auth'   => ['view'],
                'action_auth'   => ['channel.mnp_settings/getConfig'],
            ],
            'save'      => [
                'button_auth'   => ['auth_all'],
                'action_auth'   => ['channel.mnp_settings/setConfig'],
            ],
        ],
        //APP设置
        'appsetting'   => [
            'page_path'     => '/channel/app_store',
            'view'      => [
                'button_auth'   => ['view'],
                'action_auth'   => ['channel.app_setting/getConfig'],
            ],
            'save'      => [
                'button_auth'   => ['auth_all'],
                'action_auth'   => ['channel.app_setting/setConfig'],
            ],
        ],
        //H5设置
        'h5setting'   => [
            'page_path'     => '/channel/h5_store',
            'view'      => [
                'button_auth'   => ['view'],
                'action_auth'   => ['channel.h5_setting/getConfig'],
            ],
            'save'      => [
                'button_auth'   => ['auth_all'],
                'action_auth'   => ['channel.h5_setting/setConfig'],
            ],
        ],
        //pc商城-渠道设置
        'wechat_platform'   => [
            'page_path'     => '/wechat/wechat_platform',
            'view'      => [
                'button_auth'   => ['view'],
                'action_auth'   => ['channel.open_setting/getConfig'],
            ],
            'save'      => [
                'button_auth'   => ['auth_all'],
                'action_auth'   => ['channel.open_setting/setConfig'],
            ],
        ],

    ],
    // 权限管理
    'auth' => [
        //管理员
        'permissions' => [
            'page_path' => '/permission/admin',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['auth.admin/lists', 'auth.role/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'auth.admin/add',
                    'auth.admin/edit',
                    'auth.admin/detail',
                    'auth.admin/del',
                ],
            ],
        ],
        //角色
        'role' => [
            'page_path' => '/permission/role',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['auth.role/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'auth.role/add',
                    'auth.role/edit',
                    'auth.role/detail',
                    'auth.role/del',
                    'config/getMenu',
                ],
            ],
        ],
    ],
    // 系统设置
    'setting' => [
        // 网站信息
        'website' => [
            'page_path' => '/setting/website/information',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.web.websetting/getwebsite'],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => ['setting.web.websetting/setwebsite'],
            ],
        ],
        //备案信息
        'record' => [
            'page_path' => '/setting/website/filing',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.web.websetting/getcopyright'],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => ['setting.web.websetting/setcopyright'],
            ],
        ],
        // 协议/政策
        'protocol' => [
            'page_path' => '/setting/website/protocol',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.web.web_setting/getAgreement'],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => ['setting.web.web_setting/setAgreement'],
            ],
        ],
        // 交易设置
        'order' => [
            'page_path' => '/setting/order',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.transaction_settings/getConfig'],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => ['setting.transaction_settings/setConfig'],
            ],
        ],
        // 客服设置
        'service' => [
            'page_path' => '/setting/service',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.customer_service/getConfig'],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => ['setting.customer_service/setConfig'],
            ],
        ],
        // 支付配置
        'pay_config' => [
            'page_path' => '/setting/payment_config',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.pay.pay_config/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'setting.pay.pay_config/detail',
                    'setting.pay.pay_config/edit'
                ],
            ],
        ],
        // 支付方式
        'pay_way' => [
            'page_path' => '/setting/payment_way',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.pay.pay_way/getPayWay'],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => ['setting.pay.pay_way/setPayWay'],
            ],
        ],
        // 用户设置
        'user' => [
            'page_path' => '/setting/user',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.user.user/getConfig'],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => ['setting.user.user/setConfig'],
            ],
        ],
        // 登录注册
        'register' => [
            'page_path' => '/setting/user/login',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.user.user/getRegisterConfig'],
            ],
            'save' => [
                'button_auth' => ['auth_all'],
                'action_auth' => ['setting.user.user/setRegisterConfig'],
            ],
        ],
        //系统环境
        'environment' => [
            'page_path' => '/setting/system/environment',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['setting.system.system/info'],
            ],
        ],
        // 定时任务
        'task' => [
            'page_path' => '/setting/task',
            'view' => [
                'button_auth' => ['view'],
                'action_auth' => ['crontab.crontab/lists'],
            ],
            'manage' => [
                'button_auth' => ['auth_all'],
                'action_auth' => [
                    'crontab.crontab/add',
                    'crontab.crontab/detail',
                    'crontab.crontab/edit',
                    'crontab.crontab/del',
                    'crontab.crontab/expression'
                ],
            ],
        ],
    ],

];