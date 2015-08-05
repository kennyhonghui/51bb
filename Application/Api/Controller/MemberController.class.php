<?php
/*---------------------------------------
 * 会员系统控制器
 * 用于提供会员系统部分的API接口
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/7/12 15:29
 *
 */

namespace Api\Controller;

class MemberController extends ApiController{
    Protected $autoCheckFields = false;
    /**
     * <pre>
     * 接    口：/member/getsmscode - 获取手机验证码
     * 传    入：user:string
     * 参数说明： user: 用户名称
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function getsmscode(){
        $user = $this -> post('user');
        $status = D('Member')->getsmscode( $user );
        $this -> respons( $status['code'], $status['data'] );
    }

    /**
     * <pre>
     * 接    口：/member/register - 注册新用户接口
     * 传    入：user:string, password:string, code:string
     * 参数说明： user: 用户名称， password：密码， code：手机验证码
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function register(){
        $user = $this -> post('user');
        $pwd = $this -> post('password');
        $code = $this -> post('code');

        $code = D('Member')->register($user, $pwd, $code);
        $this -> respons( $code );
    }

    /**
     * <pre>
     * 接    口：/member/login - 登录接口
     * 传    入：user:string, password:string
     * 参数说明： user: 用户名称， password：密码
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function login(){
        $user = $this -> post('user');
        $pwd = $this -> post('password');
        $result = D('Member')->login($user, $pwd);
        $this -> respons( $result['status'], $result['data'] );
    }

    /**
     * <pre>
     * 接    口：/member/forgot - 忘记密码(包括验证用户是否存在/短信验证码是否通过)
     * 传    入：user:string, code:string
     * 参数说明： user: 用户名称， code：手机验证码
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function forgot(){
        $user = $this -> post('user');
        $code = $this -> post('code');
        $status_code = D('Member')->forgot($user, $code);
        $this -> respons($status_code);
    }

    /**
     * <pre>
     * 接    口：/member/reset - 重置密码(包括验证验证两次输入密码是否通过)
     * 传    入：user:string, password1:string, password2:string
     * 参数说明： user: 用户名称， password1：密码， password2:密码确认
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function reset(){
        $user = $this -> post('user');
        $password1 = $this -> post('password1');
        $password2 = $this -> post('password2');
        $status_code = D('Member') -> reset($user, $password1, $password2);
        $this -> respons($status_code);
    }

    /**
     * <pre>
     * 接    口：/member/exist - 检测用户名是否存在
     * 传    入：user:string
     * 参数说明： user: 用户名称
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function exist(){
        $user = $this -> post('user');
        $status_code = D('Member') -> exist($user);
        $this -> respons($status_code);
    }

    /**
     * <pre>
     * 接    口：/member/info - 获取用户信息
     * 传    入：key:string
     * 参数说明：密匙
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function info(){
        $default = './Uploads/51bangbang/userphotos/default.png';
        $key = $this -> post('key');
        $status_code = D('Member') -> info($key, $default);
        $this -> respons($status_code['status'], $status_code['data']);
    }

    /**
     * <pre>
     * 接    口：/member/update - 更新用户信息
     * 传    入：key:string, nickname:sting|昵称, birthday: string|(1980-01-01), sex: int|0:男|1：女
     * 参数说明：密匙
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function update(){
        $key     = $this -> post('key');
        $nickname = $this -> post('nickname');
        $birthday = $this -> post('birthday');
        $sex      = $this -> post('sex');

        $status_code = D('Member') -> update($key, $nickname, $birthday, $sex);
        $this -> respons($status_code);
    }

    /**
     * <pre>
     * 接    口：/member/setphoto - 更新用户头像
     * 传    入：key
     * 参数说明：密匙
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function setphoto(){
        $key = $this -> post('key');
        $HttpServletRequest = $this -> post('HttpServletRequest');
        $status = D('Member') -> setphoto($key, $HttpServletRequest);
        $this -> respons($status['code'], $status['data']);
    }

    /**
     * <pre>
     * 接    口：/member/logout - 注销登录
     * 传    入：key
     * 参数说明：密匙
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function logout(){
        $key = $this -> post('key');
        $status = D('Member') -> logout($key);
        $this -> respons($status);
    }

    /**
     * <pre>
     * 接    口：/member/status - 获取在线状态
     * 传    入：user
     * 参数说明：用户
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function status(){
        $user = $this -> post('user');
        $user = $this -> post('user');
        $status = D('Member') -> status($user);
        $this -> respons($status);
    }
}
