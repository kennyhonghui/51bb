<?php
/*--------------------------------------
 * 行业分类系统模型 - bb_category
 *
 * @Project 无忧帮帮
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

            //提交反馈时间间隔限制
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
}