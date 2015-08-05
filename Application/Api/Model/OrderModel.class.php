<?php
/*--------------------------------------
 * ��Աϵͳģ��
 *
 * @Project ���ǰ��
 * @Author  HHH
 * @Date    2015/7/20 18:01
 *--------------------------------------
 */
namespace Api\Model;
use Think\Model;


class OrderModel extends Model{
    Protected $autoCheckFields = false;

    public function createOrder($options){

        //����ֻ������ʽ
        $chkmobile = checkmobile($options['mobile']);
        if($chkmobile !== 0)   return $chkmobile;

        //var_dump($options);
        $userinfo = D('Member')->certificate( $options['key'] );
        if( !is_array($userinfo) ) return $userinfo;   //20002

        $orderModel = M('51_orders');

        //��䶩������  status - 0: �����У�1���ѽ�����2����ȡ���� 999����ɾ��
        $orderdata['orderid'] = get_order_sn();  //������
        $orderdata['uid'] = $userinfo['uid'];
        $orderdata['status'] = 0;
        $orderdata['options'] = array2string($options);

        print_r( string2array($orderdata['options']) );

        $result = $orderModel -> create( $orderdata );
    }
}