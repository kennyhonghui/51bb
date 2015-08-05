<?php
/*---------------------------------------
 * 相关公用配置信息文件
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/7/12 15:22
 * ---------------------------------------
 */

return array(
    /** 项目名称 */
    'PROJECT_NAME' => '无忧帮帮',

    /** 网址域名 */
    'SITE_URL' => 'http://localhost/51bb/',
    //'SITE_URL' => 'http://51.281.com.cn/',

    /** 调试模式 */
    'DEBUG_MODE' => FALSE,

    /** 手机验证码相关设定 */
    'SMS' => array(
        //关闭则不发送信息，直接返回验证码，方便开发时调试。
        'SEND_SMS' => FALSE,
        //同一个用户一天最多允许发送多少条
        'ONE_DAY_LIMIT' => 3,
        //发送短信时间间隔，单位为"分钟".
        'TIME_SPAN' => 1,
        //短信验证码过期时间,单位为"分钟".
        'SMS_EXPIRE' => 3,
        //API调用地址
        'URL'      => 'http://v.juhe.cn/sms/send',
        //短信模板
        'SMS_TEMPLATE' => array(
            'common'   => 4716,   //【无忧帮帮】欢迎使用#app#，您的手机验证码是#code#，本条信息无需回复。
            'register' => 4715,   //【无忧帮帮】感谢您注册#app#，您的验证码是#code#，如非本人操作，请忽略本短信
            'password' => 4714,   //【无忧帮帮】您本次找回密码的验证码是#code#，有效期为#minutes#分钟，请尽快验证。
        ),
    ),
    /** 行业分类ID */
    'CATEGORY_ID' => 6,

    /** API默认返回的数据格式 */
    'DEFAULT_AJAX_RETURN' => 'JSON',

    /** 加密密匙 */
    'ENCRYPT_KEY'  => '93035ZQ1kIjapEaso0nkuNwu+gcKWZrutCtOS6at6rV5XPnAr8E',

    /** TOKEN */
    'TOKEN' => '51bbHappyBirthday',

    /** UPDATE & VERSION 版本号控制与APP更新地址 */
    'VERSION_BUILDER' => array(
        'version' => 'BB_VERSION',
        'url'     => 'BB_UPDATE_URL',
    ),

    /** 操作时间间隔,单位为"分钟" */
    'TIME_INTERVAL' => 5,

    /** COOKIE */
    'COOKIE' => array(
        'prefix'   => 'WYBB',
        'user'     => 'USER',
        'password' => 'DUSS',
        'expire' => '10',
    ),

    /** 公告显示数目 */
    'ANNOUNCE_NUM' => 5,

    /** 正则表达式验证 */
    'MATCHES' => array(
        'mobile' => '/^1[3|4|5|8][0-9]\d{4,8}$/',
    ),

    /** 文件上传部分设置 */
    'UPLOAD' => array(
        'maxsize' => '2M',
        'userphotopath' => './Uploads/51bangbang/userphotos/',
        'allowpicexts' => array('jpg', 'gif', 'png', 'jpeg'),
        'thumb' => true,
        'userphotoThumbMaxWidth' => 180,
        'userphotoThumbMaxHeight' => 180,
        'userphotoThumbPath' => './Uploads/51bangbang/userphotos/thumbs/',
        'thumbExt' => 'jpg',
        'thumbRemoveOrigin' => false,
        'autoCheck' => false,
        'uploadReplace' => true,
    ),

    /**
     * 错误代码提醒
     * @token: e949u0vIJmqYZF8dZHHmcx2p+fyAhP5ODtY8wLy2tkEPxfe5nr5O1qGJ7nfzew
     */
    'ERROR_CODE' =>array(
        0 => 'success',
        # 参数
        10000 => '非法链接进入',
        10001 => '参数缺失',
        10002 => '非法操作',
        10003 => '手机号码格式不正确',

        #会员模块
        20000 => '账号信息异常,请重新登录',
        20001 => '生成验证码失败',
        20002 => '验证码已过期',
        20003 => '验证码错误',
        20004 => '用户不存在',
        20005 => '用户已存在',
        20006 => '注册用户失败',
        20007 => '用户不存在或密码错误',
        20008 => '重置密码失败',
        20009 => '两次输入密码不一致',
        //这里差一个20010
        20011 => '信息更新成功',
        20012 => '信息更新失败',
        20014 => '更新头像失败',
        20015 => '反馈信息失败',
        20016 => '反馈信息过于频繁',
        20017 => '用户在线状态',
        20018 => '用户离线状态',
        20018 => '用户离线状态',
        20019 => '注销登录失败',

        #About模块
        30000 => '获取文章列表失败',
        30001 => '获取版本号失败',

        #分类
        40001 => '暂无分类列表',
        40002 => '获取分类失败',
    )
);
