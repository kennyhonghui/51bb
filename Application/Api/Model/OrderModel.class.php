<?php
/*--------------------------------------
 * 会员系统模型
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/7/20 18:01
 *--------------------------------------
 */
namespace Api\Model;
use Think\Model;


class OrderModel extends Model{
    Protected $autoCheckFields = false;

    public function createOrder($options){

        //检测手机号码格式
        $chkmobile = checkmobile($options['mobile']);
        if($chkmobile !== 0)   return $chkmobile;

        //var_dump($options);
        $userinfo = D('Member')->certificate( $options['key'] );
        if( !is_array($userinfo) ) return $userinfo;   //20002

        $orderModel = M('51_orders');

        //填充订单数据  status - 0: 进行中，1：已结束，2：已取消， 999：已删除
        $orderdata['orderid'] = get_order_sn();  //订单号
        $orderdata['uid'] = $userinfo['uid'];
        $orderdata['status'] = 0;
        $orderdata['options'] = array2string($options);

        print_r( string2array($orderdata['options']) );

        $result = $orderModel -> create( $orderdata );
    }
}