<?php
/*---------------------------------------
 * 公用函数库
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/7/12 14:08
 * ---------------------------------------
 */

/**
 * $string 明文或密文
 * $operation 加密ENCODE或解密DECODE
 * $key 密钥
 * $expiry 密钥有效期
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥
    $ckey_length = 4;

    // 密匙
    // $GLOBALS['discuz_auth_key'] 这里可以根据自己的需要修改
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        // substr($result, 0, 10) == 0 验证数据有效性
        // substr($result, 0, 10) - time() > 0 验证数据有效性
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
        // 验证数据有效性，请看未加密明文的格式
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
/*
 * 获取错误消息
 */
function get_error_msg($code){
    $code = if_numeric($code) ? $code : '';
    # 响应码协议
    $_G_response_code_protocol = C('ERROR_CODE');

    $msg = isset($_G_response_code_protocol[$code])
         ? $_G_response_code_protocol[$code]
         : '未定义错误';

    return $msg;
}

function if_numeric($str, $atleast=0, $atmost=0){
    $str=trim($str);
    if(preg_match('#\D#', $str)){ return false; }

    # 位数约束
    $bits_regexp = '+';
    if(preg_match('#^\d+$#', $atleast.$atmost)){
        if($atmost>$atleast){
            $bits_regexp = '{'.$atleast.','.$atmost.'}';
        } else if($atleast) {
            $bits_regexp = '{'.$atleast.',}';
        }
    }

    if(preg_match('#^\d'.$bits_regexp.'$#', $str)) {
        return true;
    }

    return false;
}

/**
 * 获取可以入库时间格式/返回从库取出的时间转换成时间戳
 * @param string $time_format
 * @return bool|int|string
 */
function get_datetime( $time_format = '' ){
    if( '' === $time_format )
        return date('Y-m-d H:i:s', time());
    else
        return strtotime( $time_format );
}


/**
 * 手机验证码生成器
 * @param int $len
 * @param string $format
 * @return string
 */
function getCode($len=6,$format='NUMBER'){
    switch($format){
        case 'ALL'://生成包含数字和字母的验证码
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; break;
        case 'CHAR'://仅生成包含字母的验证码
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; break;
        case 'NUMBER'://仅生成包含数字的验证码
            $chars='0123456789'; break;
        default :
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; break;
    }
    $string='';
    while(strlen($string)<$len)
        $string.=substr($chars,(mt_rand()%strlen($chars)),1);
    return $string;
}

/**
 * UNICODE转码UTF-8
 * @param $str
 * @return mixed
 */
function decodeUnicode($str){
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}


//-----------------------------------------------------------------------------------
/**
 * 将字符串转换为数组
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data) {
    if ($data == '') return array();
    if (is_array($data)) return $data;
    if (strpos($data, 'array') !== false && strpos($data, 'array') === 0) {
        @eval("\$array = $data;");
        return $array;
    }
    return unserialize($data);
}

/**
 * 将数组转换为字符串
 * @param	array	$data		数组
 * @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1) {
    if($data == '') return '';
    if($isformdata) $data = new_stripslashes($data);
    return serialize($data);
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
    if(!is_array($string)) return stripslashes($string);
    foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
    return $string;
}
//------------------------------------------------------------------------------------

function get_order_sn(){
    //生成24位唯一订单号码，格式：YYYY-MMDD-HHII-SS-NNNN,NNNN-CC，其中：YYYY=年份，MM=月份，DD=日期，HH=24格式小时，II=分，SS=秒，NNNNNNNN=随机数，CC=检查码
    while(true) {
        //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
        $order_id_main = date('YmdHis') . rand(10000000, 99999999);
        //订单号码主体长度
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for ($i = 0; $i < $order_id_len; $i++) {
            $order_id_sum += (int)(substr($order_id_main, $i, 1));
        }
        //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
        $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
        return $order_id;
    }
}

/**
 * 容量转换为字节数
 * @param $size
 * @return bool|int
 */
function size_translate( $size ){
    if( empty($size) ) return false;
    $format = '/^(\d+)([kKmMgG\d])$/';
    preg_match($format, $size, $matches);

    if( empty($matches) ) return false;

    $data = $matches[1];
    $unit = strtoupper($matches[2]);

    switch( $unit ){
        case 'K':
            $data = (int)$data;
            $after = $data * 1024;
            break;
        case 'M':
            $data = (int)$data;
            $after = $data * 1024 * 1024;
            break;
        case 'G':
            $data = (int)$data;
            $after = $data * 1024 * 1024 * 1024;
            break;
        default:
            $after = $size;
            break;
    }
    return (int)$after;
}

/**
 * 1D,1H,1M,1S - 转换为秒数
 * @param $time
 * @return bool|int
 */
function get_seconds( $time ){
    if( empty($time) ) return false;
    $format = '/^(\d+)([DdHhMmSs\d])$/';
    preg_match($format, $time, $matches);

    if( empty($matches) ) return false;

    $data = $matches[1];
    $unit = strtoupper($matches[2]);

    switch( $unit ){
        case 'D':
            $data = (int)$data;
            $after = $data * 86400;
            break;
        case 'H':
            $data = (int)$data;
            $after = $data * 3600;
            break;
        case 'M':
            $data = (int)$data;
            $after = $data * 60;
            break;
        case 'S':
            $after = (int)$data;
            break;
        default:
            $after = $time;
            break;
    }
    return (int)$after;
}

/**
 * 用户密码加密， 暂定使用md5、sha1加密
 * 单独放出来方便以后可以修改
 * @param $pwd
 * @return string
 */
function userpwd( $pwd ){
    return md5( sha1($pwd) );
}