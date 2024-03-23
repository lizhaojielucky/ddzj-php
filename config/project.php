<?php
return [
    // 系统版本号
    'version' => '1.1.3',

    // 官网
    'website' => [
        'name' => env('project.web_name', '网站名称'), // 网站名称
        'url' => env('project.web_url', 'www.like.cn/'), // 网站地址
        'login_image' => 'resource/image/adminapi/default/web_login_image.png',//登录页图片
        'web_logo' => 'resource/image/adminapi/default/web_logo.png', // 网站logo
        'web_favicon' => 'resource/image/adminapi/default/web_favicon.ico', // 网站图标
        'shop_name' => 'likeshop上门家政', // 商城名称
        'shop_logo' => 'resource/image/adminapi/default/shop_logo.png', // 商城图标
    ],

    // 交易设置
    'transaction' => [
        'verification_services_distance' => 100, // 服务核销距离
    ],

    // 唯一标识，密码盐、路径加密等
    'unique_identification' => env('project.unique_identification', 'likeadmin'),

    // 后台管理员token（登录令牌）配置
    'admin_token' => [
        'expire_duration' => 3600 * 8,//管理后台token过期时长(单位秒）
        'be_expire_duration' => 3600,//管理后台token临时过期前时长，自动续期
    ],

    // 商城用户token（登录令牌）配置
    'user_token' => [
        'expire_duration' => 3600 * 8,//用户token过期时长(单位秒）
        'be_expire_duration' => 3600,//用户token临时过期前时长，自动续期
    ],

    // 列表页
    'lists' => [
        'page_size_max' => 25000,//列表页查询数量限制（列表页每页数量、导出每页数量）
        'page_size' => 25, //默认每页数量
    ],

    // 各种默认图片
    'default_image' => [
        'admin_avatar' => 'resource/image/adminapi/default/avatar.png',
        'admin_ad' => 'resource/image/adminapi/default/ad.png',//广告
        'admin_goods_category' => 'resource/image/adminapi/default/goods_category.png',//服务分类
        'admin_goods_lists' => 'resource/image/adminapi/default/goods_lists.png',//服务列表
        'admin_index_decorate' => 'resource/image/adminapi/default/index_decorate.png',//首页装修
        'admin_order' => 'resource/image/adminapi/default/order.png',//订单列表
        'admin_staff' => 'resource/image/adminapi/default/staff.png',//师傅列表
        'admin_news_notice' => 'resource/image/adminapi/default/news_notice.png',//信息通知
        'admin_set_payment' => 'resource/image/adminapi/default/set_payment.png',//支付配置
        'user_avatar' => 'resource/image/shopapi/default/avatar.png',//默认用户头像
    ],

    // 文件上传限制 (图片)
    'file_image' => [
        'jpg', 'png', 'gif', 'jpeg', 'webp'
    ],

    // 文件上传限制 (视频)
    'file_video' => [
        'wmv', 'avi', 'mpg', 'mpeg', '3gp', 'mov', 'mp4', 'flv', 'f4v', 'rmvb', 'mkv'
    ],

    // 登录设置
    'login' => [
        // 登录方式：1-账号密码登录；2-手机短信验证码登录
        'login_way' => ['1', '2'],
        // 注册强制绑定手机 0-关闭 1-开启
        'coerce_mobile' => 1,
        // 第三方授权登录 0-关闭 1-开启
        'third_auth' => 1,
        // 微信授权登录 0-关闭 1-开启
        'wechat_auth' => 1,
        // qq授权登录 0-关闭 1-开启
        'qq_auth' => 0,
        // 登录政策协议 0-关闭 1-开启
        'login_agreement' => 1,
    ],

    // 后台装修
    'decorate' => [
        // 底部导航栏样式设置
        'tabbar_style' => ['default_color' => '#999999', 'selected_color' => '#4173ff'],
    ],


    // 产品code
    'product_code' => '9f3b8d8b0cec733b1695ca1bb3c65246',
    'check_domain' => 'https://server.likeshop.cn',
];
