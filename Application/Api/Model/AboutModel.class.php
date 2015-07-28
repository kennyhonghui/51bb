<?php
/*--------------------------------------
 * ��ҵ����ϵͳģ�� - bb_category
 *
 * @Project ���ǰ��
 * @Author  HHH
 * @Date    2015/7/20 18:01
 *--------------------------------------
 */
namespace Api\Model;
use Think\Model;


class AboutModel extends Model{
    Protected $autoCheckFields = false;

    public function feedback($key, $content){
        if( trim($content) == ''  )  return 20012;
        $userinfo = D('Member')->certificate( $key );
        if( is_array($userinfo) ){
            $feedbackModel = M('51_feedback');
            $uid = $userinfo['uid'];

            //�ύ����ʱ��������
            $lastfeedbackinfo = $feedbackModel -> field('create_time') -> where("uid='$uid'") -> order('create_time desc') -> limit(1) -> select();

            if( ! is_null($lastfeedbackinfo) ){
                $lasttimestamp = (int)$lastfeedbackinfo[0]['create_time'];
                $timeinterval = C('TIME_INTERVAL') * 60 ;
                $nowtimestamp = time();

                if( $nowtimestamp - $lasttimestamp <= $timeinterval ) return 20016;
            }

            $data['uid'] = $uid;
            $data['content'] = htmlspecialchars($content);
            $data['create_time'] = time();
            $data['status'] = 0;
            $result = $feedbackModel -> create($data);
            if( $result ){
                $feedbackModel -> add();
                return 0;
            }else{
                return 20015;
            }
        }else{
            return $userinfo;
        }
    }

    /**
     * ��ȡ�����б�
     * @return array
     */
    public function announce(){
        $limit = C('ANNOUNCE_NUM');  //��ʾ��������
        $model = M('document');
        $result = $model ->join('join bb_category as C on bb_document.category_id=C.id')
                         ->field('bb_document.id,bb_document.title')
                         ->where('C.name="announce" and bb_document.category_id=C.id and bb_document.status=1')
                         ->order('bb_document.id desc')
                         ->limit($limit)
                         ->select();
        if( is_array($result) ){
            return array(
                'code' => 0,
                'data'   => $result
            );
        }
        return array(
            'code' => 30000,
            'data'   => ''
        );
    }

    /**
     * ��ȡ�������ǰ�����������
     * @return array
     */
    public function about($document_name){
        //ABOUTWUYOUBANGBAGN - �������ǰ��
        //WUYOUBANGBANGDECLARE - ���ǰ������
        $model = M('document');
        $result = $model ->join('join `bb_document_article` as A on bb_document.id=A.id')
            ->field('bb_document.title,A.content')
            ->where('bb_document.status=1 and bb_document.name="'.$document_name.'"')
            ->order('bb_document.id desc')
            ->limit(1)
            ->select();
        if( is_array($result) ){
            return array(
                'code' => 0,
                'data'   => $result
            );
        }
        return array(
            'code' => 30000,
            'data'   => ''
        );
    }

    /**
     * ��ȡ�汾��Ϣ���Լ����µ�ַ
     * @return array
     */
    public function version(){
        $version_info = C('VERSION_BUILDER');
        $config  = M('config');

        $version = $version_info['version'];
        $url     = $version_info['url'];

        $result = $config -> field('value') -> where("name='$version' or name='$url'")->order('sort asc')->select();
        if( is_array($result) && count($result) == 2 ){
            return array('code'=>0, 'data' => array(
                'version' => $result[0]['value'],
                'url' => $result[1]['value'],
                'request' => $result[1]['value'] . '?version=' . $result[0]['value'],
            ));
        }
        return array('code' => 30001, 'data' => '');
    }

}