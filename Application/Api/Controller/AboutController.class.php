<?php
/*---------------------------------------
 * 关于无忧帮帮页面
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/07/23 20:29
 * --------------------------------------
 */

namespace Api\Controller;

class AboutController extends ApiController
{

    /**
     * <pre>
     * 接    口：/about/version - 获取版本信息
     * 返    回：json: example-{"status": int, "msg":string, "data": string}
     * </pre>
     */
    public function version(){
        return $this->respons(0, C('version'));
    }

    /**
     * <pre>
     * 接    口：/about/feedback - 用户反馈信息接口
     * 传    入：key:string
     * 参数说明： 用户密匙
     * 返    回：json: example-{"status": int, "msg":string, "data": empty_string}
     * </pre>
     */
    public function feedback(){
        $key = $this -> post('key');
        $content = $this -> post('content');
        $result =  D('About')->feedback( $key, $content );
        $this -> respons($result);
    }

    /**
     * <pre>
     * 接    口：/about/announce - 公告列表
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function announce(){
        $result = D('About')->announce();
        $this -> respons($result['code'], $result['data']);
    }

    /**
     * <pre>
     * 接    口：/about/about - 关于无忧帮帮
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function about(){
        $result = D('About')->about('ABOUTWUYOUBANGBAGN');
        $this -> respons($result['code'], $result['data']);
    }

    /**
     * <pre>
     * 接    口：/about/declaration - 无忧帮帮声明
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function declaration(){
        $result = D('About')->about('WUYOUBANGBANGDECLARE');
        $this -> respons($result['code'], $result['data']);
    }
}