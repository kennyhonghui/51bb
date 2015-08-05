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


class CategoryModel extends Model{

    Protected $autoCheckFields = false;
    private $int_preg = '/^\d+$/';

    /**
     * @param $parent  - 0:一级栏目
     * @return array
     */
    public function getCategory($parent){
        $d = M('category');
        $data = array();
        preg_match($this->int_preg,$parent, $matches);
        if(empty($matches)) return array("code" => 40002, 'data' => '');

        if( (int)$parent === 0 ) $parent = C('CATEGORY_ID');   //转换ID
        $cats = $d -> where("pid='$parent' AND status=1 AND groups='systemcategory'")->select();
        if( $cats && count($cats)>0 ){
            foreach( $cats as $cat ){
                array_push( $data, array(
                    'cid' => $cat['id'],
                    'name' => $cat['title'],
                ));
            }
            $result = array( "code" => 0, 'data' => $data );
        }elseif( count($cats)<1 ){
            $result= array( "code" => 40001, 'data' => '');
        }else{
            $result = array("code" => 40002, 'data' => '');
        }

        return $result;
    }

    /**
     * @param $cid
     * @return array
     */
    public function categoryInfo($cid){
        $d = M('category');

        preg_match($this->int_preg, $cid, $matchs);
        if(empty($matchs)) return array("code" => 40002, 'data' => '');

        $cat = $d -> where("id=$cid AND status=1")->select();
        if( ! is_null($cat) ){
            $data['cid'] = $cat[0]['id'];
            $data['name'] = $cat[0]['title'];
            $data['parent'] = $cat[0]['pid'];
            $result = array( "code" => 0, 'data' => $data );
        }else{
            $result= array( "code" => 40001, 'data' => '');
        }

        return $result;
    }
}