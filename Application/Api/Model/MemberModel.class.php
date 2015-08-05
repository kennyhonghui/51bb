<?php
/*---------------------------------------
 * 会员系统模型
 * 用于提供会员系统部分的API接口
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/7/12 13:13
 * ---------------------------------------
 */


namespace Api\Model;
use Think\Model;


class MemberModel extends Model{
    Protected $autoCheckFields = false;

    private $usesmscodetodo = array(
        'REGISTER','RESET'
    );

    /**
     * @param $user
     * @return array
     */
    public function getsmscode($user){
        //检测手机号码格式
        $chkmobile = checkmobile($user);
        if($chkmobile !== 0)   return array('code'=>$chkmobile, 'data'=>'');

        $code = $this -> savecode( $user );

        if( $code ){
            if( C('SMS')['SEND_SMS'] ){
                $this -> sendSMS( $user, $code );
                return array('code'=>0, 'data'=>'');
            }else{
                return array('code'=>0, 'data'=>$code);
            }
        }else{
            return array('code'=>20001, 'data'=>'');
        }
    }

    /**
     * 将手机验证码写入数据库，用于校对
     * @param $user
     * @return bool|string
     */
    public function savecode( $user ){
        $smscode = M('51_smscode');
        $fromdb = $smscode -> where("mobile='$user'")->select();
        $sms_config = C('SMS');
        $code = getCode();
        $time = get_datetime();

        if( is_null($fromdb) ){
            $data['mobile'] = $user;
            $data['count'] = 1;
            $data['rest'] = $sms_config['ONE_DAY_LIMIT'];
            $data['datetime'] = $time;
            $data['code'] = $code;
            $result = $smscode -> add($data);
        } else {
            $o = $fromdb[0];
            //检测一天剩余发送的条数
            //$rest = (int)$o['rest'];
            //检测时间间隔

            $data['datetime'] = $time;
            $data['code'] = $code;
            $data['todo'] = '';
            $data['count'] = (int)$o['count'] + 1;
            $data['rest'] = (int)$o['rest'] - 1;
            $result = $smscode -> where("mobile = '$user'") -> save($data);
        }
        return $result ? $code : false;    //return $result ? 0 : 20001;
    }

    /**
     * 注册方法
     *
     * @param $user
     * @param $pwd
     * @param $code
     * @return int
     */
    public function register($user, $pwd, $code){
        //0.检测手机号码格式
        $chkmobile = checkmobile($user);
        if($chkmobile !== 0)   return $chkmobile;

        //1.判断验证码
        $smscode = $this -> verifycode($user, $code, $this->usesmscodetodo[0]);
        if( $smscode !== 0 ){
            return $smscode;
        }
        //2.判断用户是否存在
        $usertable = M('51_user');
        $exist = $usertable -> where("mobile='$user'")->select();
        if( !is_null($exist) ) return 20005;

        $data["mobile"] = $user;
        $data["password"] = userpwd($pwd);
        $data["score"] = 5;
        $data["reg_time"] = time();
        $data["status"] = 1;

        if( $usertable->add($data) ){
            $uid = $usertable -> getLastInsID();
            M('51_smscode')->where("mobile=$user")->save(array('todo'=>''));
            M('51_userdata')->add(array('uid'=>$uid));
            return 0;
        } else {
            return 20006;
        }

    }

    /**
     * 用于发送手机验证码短信
     * @param $user
     * @param $code
     */
    public function sendSMS($user, $code){
        $sms_config = C('SMS');
        $url = $sms_config['URL'];

        $params = "key=4ec08df222fbc6c179e83aa1da3cf631&dtype=json&mobile=$user";
        $params .= "&tpl_id=".$sms_config['SMS_TEMPLATE']['common'];
        $params .= "&tpl_value=". urlencode('#app#='.C('PROJECT_NAME').'&#code#='.$code);
        $request = $url . '?' . $params;

        //var_dump($request);
        @file_get_contents($request);
    }

    /**
     * 验证手机验证码方法
     * @param $user
     * @param $code
     * @param $action
     * @return int
     */
    public function verifycode($user, $code, $action){
        $smscode = M('51_smscode');
        $exist = $smscode -> where("mobile='$user' AND code='$code'")->select();
        if( !is_null($exist) ){
            $expire = (int)C('SMS')['SMS_EXPIRE'] * 60;
            $codedata = $exist[0]['code'];
            $datetime = get_datetime( $exist[0]['datetime']);

            //检查是否过期
            if( time() - $datetime > $expire ){
                return 20002;
            }else{
                if( $codedata == $code ){
                    //验证完之后把验证码设置为失效
                    $update['datetime'] = '0000-00-00 00:00:00';
                    $update['lastaction'] = $action;
                    $update['todo'] = $action;
                    $smscode -> where("mobile='$user' AND code='$code'")->save($update);

                    return 0;
                }
            }
        }
        return 20003;
    }

    /**
     * 登录验证
     * @param $user
     * @param $password
     * @return array
     */
    public function login($user, $password){
        //检测手机号码格式
        $chkmobile = checkmobile($user);
        if($chkmobile !== 0)   return array('code'=>$chkmobile, 'data'=>'');

        $password = userpwd($password);
        $usertable = M('51_user');
        $userinfo = $usertable -> where("mobile = '$user' AND password = '$password'") -> limit(1) -> select();
        if( ! is_null($userinfo) ) {
            //更新最后登录时间
            $update['last_login_time'] = time();
            $update['last_login_ip']   = $_SERVER['REMOTE_ADDR'];
            $update['login_status']    = 1;

            $usertable->where("mobile = '$user' AND password = '$password'")->save($update);
            $data = $userinfo[0];

            //生成一条密匙以用于以后部分操作
            $user = $data['mobile'];
            $uid  = $data['uid'];
            $pwd  = $data['password'];
            $key  = $this ->set_key($uid, $user, $pwd);

            return array('status' => 0, 'data' => array(
                'uid' => $data['uid'],
                'nickname' => $data['nickname'],
                'sex' => $data['sex'],
                'mobile' => $data['mobile'],
                'birthday' => $data['birthday'],
                'userphoto' => $data['userphoto'],
                'key' => $key,
            ));

        } else {
            return array('status' => 20007, 'data' => '');
        }
    }

    /**
     * 重置密码方法
     * @param $user
     * @param $password1
     * @param $password2
     * @return int
     */
    public function reset($user, $password1, $password2){
        if( $password1 !== $password2 ) return 20009;
        $usertable = M('51_user');
        $smscode = M('51_smscode');

        $fromdb = $smscode -> where("mobile='$user'")->select();
        if( !is_null($fromdb) && $fromdb[0]['todo'] === $this -> usesmscodetodo[1] ){
            $fromdb = $usertable -> where("mobile='$user'")->select();
            if( ! is_null($fromdb) ){

                //清除用户密匙 key
                $uid = $fromdb[0]['uid'];
                $this -> clear_key($uid);

                $update['password'] = userpwd($password2);
                $update['login_status']      = '0';
                $result = $usertable -> where("mobile='$user'") -> save($update);
                $smscode -> where("mobile='$user'")->save(array('todo'=>''));
                return $result ? 0 : 20008;
            }
            return 20004;
        }else{
            return 10002;
        }
    }

    /**
     * 获取某用户的在线状态
     * @param $user
     * @return int
     */
    public function status($user){
        $userModel = M('51_user');
        $result = $userModel -> where("`mobile`='$user'")->field('login_status')->limit(1)->select();
        if( !is_null($result) ){
            $userinfo = $result[0];
            $status = (int)$userinfo['login_status'];
            return $status == 1 ? 20017 : 20018;
        }
        return 20004;
    }

    /**
     * 忘记密码页面验证 - 判断手机验证码/判断用户是否存在
     * @param $user
     * @param $code
     * @return int
     */
    public function forgot($user, $code){
        //1.判断验证码
        $smscode = $this -> verifycode($user, $code, $this->usesmscodetodo[1]);
        if( $smscode !== 0 ){
            return $smscode;
        }
        //2.判断用户是否存在
        $usertable = M('51_user');
        $exist = $usertable -> where("mobile='$user'")->count();
        return $exist > 0 ? 0 : 20004;
    }

    /**
     * 检测用户名是否已存在
     * @param $user
     * @return int
     */
    public function exist( $user ){
        $chkmobile = checkmobile($user);
        if( $chkmobile !== 0 )  return $chkmobile;

        $usertable = M('51_user');
        $exist = $usertable -> where("mobile='$user'")->count();
        return $exist > 0 ? 20005 : 20004;
    }

    /**
     * 获取用户信息
     * @param $key
     * @param string $default
     * @return array
     */
    public function info( $key, $default = '' ){
        $userinfo = $this -> certificate($key);

        if( is_array($userinfo) ){

            $uid      = $userinfo['uid'];
            $nickname = $userinfo['nickname'];
            $sex      = $userinfo['sex'];
            $mobile   = $userinfo['mobile'];
            $birthday = $userinfo['birthday'];
            $userphotoid  = (int)$userinfo['userphoto'];

            if( $userphotoid > 0 ){
                //获取用户头像
                $image = M('51_image');
                $userphotoinfo = $image -> where("id=$userphotoid")->limit(1)->select();
                $userphoto = is_array($userphotoinfo) && $uid === $userphotoinfo[0]['uid'] ? $userphotoinfo[0]['url'] : $default;
            } else {
                $userphoto = $default;
            }

            $userphoto = C('SITE_URL').substr($userphoto, 2, strlen($userphoto));
            return array('status' => 0, 'data' => array(
                'uid' => $uid,
                'nickname' => $nickname,
                'sex' => $sex,
                'mobile' => $mobile,
                'birthday' => $birthday,
                'userphoto' => $userphoto,
            ));
        }else {
            return array( 'status' => $userinfo, 'data' => '' );
        }
    }

    /**
     * 更新用户信息
     * @param $key
     * @param $nickname
     * @param $birthday
     * @param $sex
     * @return int
     */
    public function update($key, $nickname, $birthday, $sex){
        $userinfo = $this -> certificate($key);

        if( is_array($userinfo) ){
            $data['nickname'] = $nickname;
            $data['birthday'] = $birthday;
            $data['sex'] = $sex;
            $uid = $userinfo['uid'];

            $usertable = M('51_user');
            $result = $usertable -> where("`uid`='$uid'") -> save($data);
            if( $result ){
                return 0;
            }else{
                return 20012;
            }
        }
    }

    /**
     * 上传用户头像
     * @param $key
     * @return array
     */

    /*public function setphoto($key, $HttpServletRequest){
        return array( 'code' => 0, 'data' => array(
            'HttpServletRequest' => $HttpServletRequest,
            'globals' => $GLOBALS,
        ));
    }*/

    public function setphoto($key, $HttpServletRequest){
        $info = $this ->certificate($key);

        if( is_array($info) ){
            $uid = $info['uid'];
            $photoid = $info['userphoto'];

            $imgModel = M('51_image');
            $userphoto = $imgModel -> where("id='$photoid' and uid='$uid'")->limit(1)->select();
            $currentphoto =  !is_null($userphoto) ? $userphoto[0]['url'] : '';  //当前头像的路径

            import('@.Tools.Upload');
            $uploader = new \Tools\Upload();
            $setting = C('UPLOAD');
            $uploader->maxSize        = size_translate($setting['maxsize']); // 设置附件上传大小
            $uploader->allowExts      = $setting['allowpicexts'];            // 设置附件上传类型
            $uploader->thumb          = false;                                   // 启用缩略图
            $uploader->savePath       = $setting['userphotopath'];           // 设置上传目录
            $uploader->thumbPath      = $setting['userphotoThumbPath'];      // 缩略图上传目录
            $uploader->thumbMaxWidth  = $setting['userphotoThumbMaxWidth'];  // 缩略图最大宽度
            $uploader->thumbMaxHeight = $setting['userphotoThumbMaxHeight']; // 缩略图最大高度
            $uploader->thumbExt       = $setting['thumbExt'];                // 缩略图文件类型
            $uploader->uploadReplace  = $setting['uploadReplace'];           // 是否覆盖同名
            $uploader->saveRule       = date( 'YmdHis', time() ).'_'.$uid;

            if(!$uploader->upload()) {
                return array(
                    'code' => 20014,
                    'data'=> '',
                );
            }else{
                //判断文件是否存在, 存在则删除原来的
                if( '' !== $currentphoto && file_exists($currentphoto))   unlink($currentphoto);
                // 上传成功 获取上传文件信息
                $msg =  $uploader->getUploadFileInfo();
                $fileinfo = $msg[0];

                $data['uid']         = $uid;
                $data['name']        = $fileinfo['name'];
                $data['savename']    = $fileinfo['savename'];
                $data['savepath']    = $fileinfo['savepath'];
                $data['ext']         = $fileinfo['extension'];
                $data['mime']        = $fileinfo['type'];
                $data['size']        = $fileinfo['size'];
                $data['sha1']        = $fileinfo['hash'];
                $data['create_time'] = time();
                $data['url']         = ($setting['userphotopath']).($fileinfo['savename']);


                return array('code'=>$uploader -> getUploadFileInfo(), 'data'=>array(
                    'a'=>$uploader -> getErrorMsg(),
                    'b' =>$uploader -> getUploadFileInfo(),
                ));

                $sql = $imgModel -> where("id='$photoid' and uid='$uid'");
                if( is_array($userphoto) ){
                    $result = $sql -> save($data);
                }else{
                    $result = $sql -> add($data);
                    $lastInsID = $imgModel -> getLastInsID();
                    //将$lastInsID更新到user表
                    M('51_user') -> where("uid=$uid") -> save( array('userphoto'=>$lastInsID) );
                }

                return $result ? array('code' => 0,
                    'data' => array(
                        'uid' => $uid,
                        'url' => C('SITE_URL').substr($data['url'], 2, strlen($data['url'])),
                    )

                ) : array('code'=>20014, 'data'=>'');

            }
        }else {
            return array(
                'code' => $info,
                'data' => ''
            );
        }

    }

    /**
     * 注销登录
     * @param $key
     * @return int
     */
    public function logout($key){
        $info = $this -> certificate($key);
        if(is_array($info)){
            $uid = $info['uid'];
            $userModel = M('51_user');
            $result = $userModel -> where("`uid`='$uid'") -> save(array('login_status'=>0));
            $this -> clear_key($uid);
            return  $result ? 0 : 20019;
        }
        return $info;
    }

    /**
     * 用于验证账号与密码 - 授权大于certificate()
     * @param $user
     * @param $password
     * @param bool $data  - 为true则返回字段数组
     * @return array|bool
     */
    public function identify($user, $password, $key, $data=false){
        $password = userpwd($password);
        $userModel = M('51_user');

        if( $data ){
            $info =  $userModel -> where("mobile='$user' AND password='$password' AND `key`='$key'")->limit(1)->select();
            return !is_null($info) ? $info[0] : false;
        }else{
            $info =  $userModel -> where("mobile='$user' AND password='$password' AND `key`='$key'")->limit(1)->count();
            return (int)$info > 0 ? true : false;
        }
    }

    /**
     * 用于验证key，普通授权
     * @param $key
     * @return int
     */
    public function certificate($key){
        $userModel = M('51_user');
        $sql = $userModel -> join('join `bb_51_userdata` as `D` on `bb_51_user`.`uid`=`D`.`uid`')->where("`D`.`userkey`='$key' AND `bb_51_user`.`login_status`='1'")->limit(1);
        $info =  $sql->select();
        return !is_null($info) ? $info[0] : 20000;
    }

    /**
     * @param $uid
     * @param $user
     * @param $password
     * @return string
     */
    private function generate_key( $uid, $user, $password ){
        $encrypt = authcode($uid.$user.$password, 'ENCODE', C('ENCRYPT_KEY'));
        return userpwd($encrypt);
    }

    /**
     * 清除用户密匙
     * @param $uid
     * @return bool|int
     */
    private function clear_key($uid){
        if( empty($uid) ) return false;
        $data['userkey'] = '';
        $userdataModel = M('51_userdata');
        $result = $userdataModel -> where("uid='$uid'")->save($data);
        return $result ? 1 : 0;
    }

    /**
     * 设置用户密匙
     * @param $uid
     * @param $user
     * @param $password
     * @return bool|int
     */
    private function set_key( $uid, $user, $password ){
        if( empty($uid) ) return false;
        $data['userkey'] = $this -> generate_key( $uid, $user, $password );
        $userdataModel = M('51_userdata');
        $result = $userdataModel -> where("uid='$uid'")->save($data);
        return $result ? $data['userkey'] : 0;
    }
}
