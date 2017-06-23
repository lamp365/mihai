<?php
/**
 *分类接口
 */

namespace wapi\controller;
class category1 extends base{
    //取一级分类接口
    public function get_one()
   {
       //缓存一天，如果有数据则取缓存数据
       if(class_exists('Memcached')){
           $memcache = new \Mcache();
           $return = $memcache->get('CATEGORY_ONE');
           if (!empty($return)) ajaxReturnData(1,'',$return);
       }
       //判断行业数
       $list = getCurrentAct();
       if (empty($list)) ajaxReturnData(0,'暂时没有活动');
       $ac_id = $list['ac_id'];
       $catService = new \service\wapi\categoryService();
       $insNum = $catService->checkInsNum($ac_id);
       
       //获取一级栏目数据
       if ($insNum >= 2){
           $data = $catService->getActInsName($ac_id);
       }else {
          $data = $catService->getP1CatName($ac_id);
       }
       
       if (empty($data)) ajaxReturnData(0,'暂时没有栏目');
       //加入缓存
       if(class_exists('Memcached')){
           $memcache = new \Mcache();
           $memcache->set('CATEGORY_ONE', $return,86400);
       }
       ajaxReturnData(1,'',$return);
   }
   //根据一级分类id取二级分类
    public function get_two(){
        $_GP = $this->request;
        $type = intval($_GP['type']);//类型
        $id = intval($_GP['id']);//栏目id
        if (empty($id) || empty($type)) ajaxReturnData(0,'参数错误');
        $catService = new \service\wapi\categoryService();
        if ($type == 1){
            $data = $catService->getCat1NameByActIns($id);
        }else{
            $data = $catService->getCat2NameByActP1Cat($id);
        }
        
        if (empty($data)) ajaxReturnData(0,'暂时没有栏目');
        ajaxReturnData(1,'',$data);
    }
}