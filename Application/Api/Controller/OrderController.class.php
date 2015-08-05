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
     * 参数说明： user: 用户名称， content：发布需求需求内容，lng、lat：经纬度
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function create(){
        $requires = array( 'user', 'content', 'lng', 'lat' );
        $params = $this -> post( $requires );
        //$result = D('') ->
    }
}