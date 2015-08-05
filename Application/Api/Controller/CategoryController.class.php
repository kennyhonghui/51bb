<?php
/*---------------------------------------
 * 系统分类
 * 行业分类的API接口
 *
 * @Project 无忧帮帮
 * @Author  HHH
 * @Date    2015/7/18 13:16
 * --------------------------------------
 */

namespace Api\Controller;

class CategoryController extends ApiController
{

    /**
     * <pre>
     * 接    口：/category/getlist - 获取行业分类信息
     * 传    入：parent:int
     * 参数说明： parent: 父栏目id - 传入 0 表示列出所有的一级分类信息
     * 返    回：json: example-{"status": int, "msg":string, "data": json}
     * </pre>
     */
    public function getlist(){
        $parent = $this->post('parent');
        $result = D('Category') -> getCategory($parent);
        $this -> respons($result['code'], $result['data']);
    }


}