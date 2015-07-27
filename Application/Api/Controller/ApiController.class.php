<?php
/*---------------------------------------
 * API公用控制器
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/7/12 15:09
 * ---------------------------------------
 */
namespace Api\Controller;
use Think\Controller;

class ApiController extends Controller {
    public function _initialize() {
        if( ! C('DEBUG_MODE') ){
            $token=$this->post('token');
            $authcode= authcode($token, 'DECODE', C('ENCRYPT_KEY') ,0);

            if($authcode != C('TOKEN')){
                $this -> respons( 10000 );
            }
        }
    }

    protected function respons($code, $data = ""){
        $msg= get_error_msg($code);
        $this -> responFunc(array("status"=>$code,"msg"=>$msg,"data"=>$data));
    }

    protected function responFunc(array $respon){
        $this->ajaxReturn($respon);
    }

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}

    /* 统一POST数据接收方法，方便以后安全过滤扩展 **/
    protected function post( $valuenames, $allow_empty = false ){
        // header:application/json
        if( ! empty($GLOBALS['HTTP_RAW_POST_DATA']) )  $postdata = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
        // header:application/x-www-form-urlencoded
        elseif( ! empty($_POST) )                      $postdata = (object)($_POST);
        // else returns 100001
        else                                           $this -> respons(10001);

        if( is_string($valuenames) ) {
            $value = isset($postdata->$valuenames) ? trim($postdata->$valuenames) : '';
            if( '' !== $value ) return $value;
            if( !$allow_empty ) $this -> respons(10001);
        }elseif( is_array($valuenames) ){
            $result = array();
            foreach( $valuenames as $vn ){
                $value = isset($postdata->$vn) ? trim($postdata->$vn) : '';
                if( isset($postdata->$vn) ){
                    $result[$vn] = $value;
                }elseif( !$allow_empty ) $this -> respons(10001);
            }
            return $result;
        }
    }
}
