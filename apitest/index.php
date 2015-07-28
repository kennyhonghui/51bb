<?php
header('Content-Type:text/html;charset=utf-8');

$server = 'http://localhost/51bb/api';
//$server = 'http://51.281.com.cn/?s=api';

$token = 'e949u0vIJmqYZF8dZHHmcx2p+fyAhP5ODtY8wLy2tkEPxfe5nr5O1qGJ7nfzew';

function decodeUnicode($str){
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}

function getRequest($url, $postdata, $data_format) {
    if( strtolower($data_format) == 'json' ){
        $header = "application/json; charset=utf-8;";
        $postdata = json_encode( $postdata );
    } else {
        $header = "application/x-www-form-urlencoded;";
        $postdata = http_build_query( $postdata );
    }

    $header .= "Content-Length: " . strlen($postdata);

    $opts['http'] = array(
        'method' => 'POST',
        'header'  => 'Content-Type: ' . $header,
        'content' => $postdata
    );
	session_write_close();
    $data = @file_get_contents($url, false, stream_context_create($opts));
    return $data;
}
function output( $data, $encode = 1 ){
	if($encode)
		var_dump( json_decode($data) );
	else{
		echo( decodeUnicode($data) );
	}
}

//require_once "postdata.php";
// $postdata = array(
	// 'user' => '15913361892',
	// 'password' => '123456',
	// 'password1' => '123456',
	// 'password2' => '123456',
	// 'code' => '477604',
	// 'token' => $token,
	// 'content' => '我要点外卖',
	
	// 'nickname' => 'kenny',
	// 'birthday' => '1988-06-08',
	// 'sex' => '0',
// ); 

$postdata = array(
	'key' => '0411ba245186e8463d36de23b11b62f9',
	'token' => $token,
	 'nickname' => 'Amy',
	 'birthday' => '1977-06-09',
	 'sex' => '1',
	 'password' => '123456',
	 'user' => '15913361892',
	 'content' => '我要吃肯德基',
); 

/**  用户中心部分 ***/
//$data = getRequest( $server . '/member/getsmscode', $postdata, 'string' );
//$data = getRequest( $server . '/member/forgot', $postdata, 'string' );
//$data = getRequest( $server . '/member/reset', $postdata, 'string' );
//$data = getRequest( $server . '/member/login', $postdata, 'string' );
//$data = getRequest( $server . '/member/register', $postdata, 'string' );
//$data = getRequest( $server . '/member/exist', $postdata, 'string' );
//$data = getRequest( $server . '/member/info', $postdata, 'string' );
//$data = getRequest( $server . '/member/update', $postdata, 'string' );
//$data = getRequest( $server . '/member/logout', $postdata, 'string' );
//$data = getRequest( $server . '/member/status', $postdata, 'string' );


//$data = getRequest( $server . '/category/getlist', $postdata, 'json' );

/***   订单部分  ****/
//$data = getRequest( $server . '/order/create', $postdata, 'json' );

/*****  获取行业分类  ****/
//$data = getRequest( $server . '/category/getlist', $postdata, 'string' );
//$data = getRequest( $server . '/category/getinfo', $postdata, 'string' );

/***  ABOUT模块   ***/
//$data = getRequest( $server . '/about/feedback', $postdata, 'string' );
$data = getRequest( $server . '/about/version', $postdata, 'string' );
//$data = getRequest( $server . '/about/announce', $postdata, 'string' );
//$data = getRequest( $server . '/about/about', $postdata, 'string' );
//$data = getRequest( $server . '/about/declaration', $postdata, 'string' );
output($data, 0);
?>

<!--
<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#upload_file').change(function(){
			if($("#upload_file").val() != '') $("#file_form").submit();
		});
	});
</script>
<p class="acc" onClick="upload_file.click()">个人头像 <span class="pull-right"><img id="personImg" src="images/person.jpg"></span> </p>
<form id="file_form" enctype="multipart/form-data" action="http://51.281.com.cn/?s=api/member/setphoto/" target="exec_target"  method="post">
	<input id="upload_file" name="file" type="file" style="display:none">
	<input id="userid" name="key" type="hidden" value="d161e118d3f4911a213b1b216d6c95c7" style="display:none">
	<input id="token" name="token" type="hidden" value="e949u0vIJmqYZF8dZHHmcx2p+fyAhP5ODtY8wLy2tkEPxfe5nr5O1qGJ7nfzew" style="display:none">
</form>
<iframe id="exec_target" name="exec_target" width=100% height=100%></iframe>
-->