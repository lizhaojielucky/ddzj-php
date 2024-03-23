<?php
/**
 * name:菜单、权限名称
 * type：类型：1-菜单；2-权限
 * sons:子级菜单
 * ----auth_key：权限key(必须唯一)
 */
return [
    // 首页-工作台
    [
        'name' => '工作台',
        'type' => 1,
        'sons'    => [
            [
                'name'      => '查看',
                'type'      => 2,
                'auth_key'  => 'index/index.view'
            ]
        ],
    ],
    // 服务管理
    [
        'name' => '服务管理',
        'type' => 1,
        'sons'    => [
            [
                'name' => '服务列表',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'service/lists.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'service/lists.manage'
                    ],
                ],
            ],
            [
                'name' => '服务分类',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'service/category.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'service/category.manage'
                    ],
                ],
            ],
            [
                'name' => '服务单位',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'service/unit.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'service/unit.manage'
                    ],
                ],
            ],
            [
                'name' => '服务评价',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'service/comment.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'service/comment.manage'
                    ],
                ],
            ],
        ],
    ],
    // 用户管理
    [
        'name' => '用户管理',
        'type' => 1,
        'sons'    => [
            [
                'name' => '用户列表',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'user/lists.view'
                    ],
                ],
            ],
            [
                'name' => '用户详情',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'user/detail.view'
                    ],
                ],
            ],
        ],
    ],
    // 师傅管理
    [
        'name' => '师傅管理',
        'type' => 1,
        'sons'    => [
            [
                'name' => '师傅列表',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'user/lists.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'user/lists.manage'
                    ],
                ],
            ],
        ],
    ],
    // 订单管理
    [
        'name' => '订单管理',
        'type' => 1,
        'sons'    => [
            [
                'name' => '订单列表',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'user/lists.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'user/lists.manage'
                    ],
                ],
            ],
            [
                'name' => '预约时间段',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'user/subscribe.view'
                    ],[
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'user/subscribe.save'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'user/subscribe.manage'
                    ],
                ],
            ],
        ],
    ],
    // 装修管理
    [
        'name' => '装修管理',
        'type' => 1,
        'sons'    => [
            [
                'name' => '首页装修',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'decorate/index.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'decorate/index.manage'
                    ],
                ],
            ],
            [
                'name' => '底部导航栏',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'decorate/navigation.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'decorate/navigation.manage'
                    ],
                ],
            ],
            [
                'name' => '广告管理',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'decorate/ad.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'decorate/ad.manage'
                    ],
                ],
            ],
        ],
    ],
    // 应用管理
    [
        'name' => '消息通知',
        'type' => 1,
        'sons'  => [
            [
                'name'      => '通知设置',
                'type'      => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'notice/notice_setting.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'notice/notice_setting.manage'
                    ],
                ],
            ],
            [
                'name'      => '短信设置',
                'type'      => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'notice/massage_setting.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'notice/massage_setting.manage'
                    ],
                ],
            ]
        ],
    ],
    //渠道
    [
        'name'  => '渠道',
        'type'  => 1,
        'sons'=>[
            [
                'name'  => '微信公众号-渠道设置',
                'type'  => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'channel/oasetting.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'channel/oasetting.save'
                    ],
                ],
            ],
            [
                'name'  => '微信公众号-菜单管理',
                'type'  => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'channel/oamenu.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'channel/oamenu.save'
                    ],
                ],
            ],
            [
                'name'  => '微信公众号-关注回复、关键词回复、默认回复',
                'type'  => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'channel/oafollowreply.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'channel/oafollowreply.manage'
                    ],
                ],
            ],
            [
                'name'  => '小程序-设置',
                'type'  => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'channel/mpsetting.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'channel/mpsetting.save'
                    ],

                ],
            ],
            [
                'name'  => 'APP-设置',
                'type'  => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'channel/appsetting.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'channel/appsetting.save'
                    ],

                ],
            ],
            [
                'name'  => 'H5-设置',
                'type'  => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'channel/h5setting.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'channel/h5setting.save'
                    ],

                ],
            ],
            [
                'name'  => '微信开放平台',
                'type'  => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'channel/wechat_platform.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'channel/wechat_platform.save'
                    ],

                ],
            ]
        ]
    ],
    // 权限管理
    [
        'name' => '权限管理',
        'type' => 1,
        'sons' => [
            [
                'name' => '管理员',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'auth/permissions.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'auth/permissions.manage'
                    ],
                ],
            ],
            [
                'name' => '角色',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'auth/role.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'auth/role.manage'
                    ],
                ]
            ],
        ],
    ],
    // 系统设置
    [
        'name' => '系统设置',
        'type' => 1,
        'sons' => [
            [
                'name' => '网站信息',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/website.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/website.save'
                    ],
                ],
            ],
            [
                'name' => '备案信息',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/record.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/record.save'
                    ],
                ],
            ],
            [
                'name' => '政策/协议',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/protocol.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/protocol.save'
                    ],
                ],
            ],
            [
                'name' => '交易设置',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/order.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/order.save'
                    ],
                ],
            ],
            [
                'name' => '客服设置',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/service.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/service.save'
                    ],
                ],
            ],
            [
                'name' => '支付配置',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/pay_config.view'
                    ],
                    [
                        'name'      => '管理',
                        'type'      => 2,
                        'auth_key'  => 'setting/pay_config.manage'
                    ],
                ],
            ],
            [
                'name' => '支付方式',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/pay_way.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/pay_way.save'
                    ],
                ],
            ],
            [
                'name' => '用户设置',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/user.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/user.save'
                    ],
                ],
            ],
            [
                'name' => '登录注册',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/register.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/register.save'
                    ],
                ],
            ],
            [
                'name' => '系统环境',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/environment.view'
                    ],
                ],
            ],
            [
                'name' => '定时任务',
                'type' => 1,
                'sons'  => [
                    [
                        'name'      => '查看',
                        'type'      => 2,
                        'auth_key'  => 'setting/task.view'
                    ],
                    [
                        'name'      => '保存',
                        'type'      => 2,
                        'auth_key'  => 'setting/task.manage'
                    ],
                ],
            ],
        ],
    ]
];



