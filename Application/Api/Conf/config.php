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
    /** 调试模式 */
    'DEBUG_MODE' => FALSE,

    /** 短信验证码过期时间,单位为"分钟" */
    'SMS_EXPIRE' => 3,
    /** 发送手机验证码开关，方便调试  */

    'SEND_SMS' =>  TRUE,
    /** API默认返回的数据格式 */

    'DEFAULT_AJAX_RETURN' => 'JSON',
    /** 加密密匙 */

    'ENCRYPT_KEY'  => '93035ZQ1kIjapEaso0nkuNwu+gcKWZrutCtOS6at6rV5XPnAr8E',
    /** TOKEN */

    'TOKEN' => '51bbHappyBirthday',
    /** 版本号 */
    'version' => '1.0',

    /** 操作时间间隔,单位为"分钟" */
    'TIME_INTERVAL' => 5,

    /** COOKIE */
    'COOKIE' => array(
        'prefix'   => 'WYBB',
        'user'     => 'USER',
        'password' => 'DUSS',
        'expire' => '10',
    ),

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

        20011 => '信息更新成功',
        20012 => '信息更新失败',
        20014 => '更新头像失败',
        20015 => '反馈信息失败',
        20016 => '反馈信息过于频繁',
        20017 => '用户在线状态',
        20018 => '用户离线状态',
        20018 => '用户离线状态',
        20019 => '注销登录失败',

        #分类
        40001 => '暂无分类列表',
        40002 => '获取分类失败',

        #About模块

    )
);
