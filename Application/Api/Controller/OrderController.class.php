<?php
/*---------------------------------------
 * 订单系统控制器
 * 用于提供订单系统部分的API接口
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/7/18 13:16
 * --------------------------------------
 */

namespace Api\Controller;

class OrderController extends ApiController{
    /**
     * <pre>
     * 接    口：/order/create - 提交订单信息
     * 传    入：json
     * 参数说明： key: 密匙， lng、lat：经纬度， address:订单地址， payment：支付方式（支付宝是1）， mobile：联系手机号码， deposit：托管订金， max：抢单名额， categoryid：行业分类ID， content：发布需求
     *
     * {"key":"3fb8e418c472db431f45122e93f0e000","token":"e949u0vIJmqYZF8dZHHmcx2p+fyAhP5ODtY8wLy2tkEPxfe5nr5O1qGJ7nfzew","lng":1,"lat":2,"address":"柏景台","categoryid":10,"payment":3,"mobile":"1380000000","deposit":100,"max":5,"content":"我要1000万"}
     *
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function create(){
        $requires = array('key', 'lng', 'lat', 'address', 'categoryid', 'payment', 'mobile', 'deposit', 'max', 'content');
        $params = $this -> post( $requires );
        $result = D('Order') -> createOrder($params);
        $this -> respons($result);
    }
}