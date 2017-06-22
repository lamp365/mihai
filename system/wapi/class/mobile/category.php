<?php
/**
 *分类接口
 */

namespace wapi\controller;
class category extends base{
    //取一级分类接口
    public function get_one()
   {
       if(class_exists('Memcached')){
           $memcache = new \Mcache();
           $data = $memcache->get('CATEGORY_ONE');
           if (!empty($data)) ajaxReturnData(1,'',$data);
       }
       $list = getCurrentAct();
       if (empty($list)) ajaxReturnData(1,'暂时没有活动');
       $actAreaModel = new \model\activity_area_model();
       $actDishModel = new \model\activity_dish_model();
       $return = $actDishModel->getAllActivtyDish(array('ac_action_id'=>$list['ac_id'],'ac_dish_status'=>1),"ac_dish_id,ac_in_id,ac_p1_id","ac_dish_id DESC");
       //判断行业数目
       if ($return){
           //如果行业数目大于1，一级栏目是行业名称，否则是一级栏目名称
           foreach ($return as $key=>$val){
               $ins_id[] = $val['ac_in_id'];
               $ac_p1_id[] = $val['ac_p1_id'];
           }
           $ins_id = array_unique($ins_id);
           $num = count($ins_id);
           if ($num > 1){
               //取行业名称
               $industryModel = new \model\industry_model();
               foreach ($ins_id as $key=>$v){
                   $industry = $industryModel->getOneIndustry(array('gc_id'=>$v),'gc_id,gc_name');
                   if (!empty($industry)){
                       $temp['id'] = $v;
                       $temp['name'] = $industry['gc_name'];
                       $temp['type'] = 1;
                       $data[] = $temp;
                   }
               }
           }else{
               //取一级栏目名称
               $ac_p1_id = array_unique($ac_p1_id);
               $shopCategoryModel = new \model\shop_category_model();
               foreach ($ac_p1_id as $key=>$v){
                   $category = $shopCategoryModel->getOneShopCategory(array('id'=>$v),'id,name');
                   if ($category){
                       $temp['id'] = $v;
                       $temp['name'] = $category['name'];
                       $temp['type'] = 2;
                       $data[] = $temp;
                   }
               }
           }
           if(class_exists('Memcached')){
               $memcache = new \Mcache();
               $memcache->set('CATEGORY_ONE', $data,86400);
           }
           ajaxReturnData(1,'',$data);
       }
       
   }
   //根据一级分类id取二级分类
    public function get_two(){
        $_GP = $this->request;
        $type = intval($_GP['type']);//类型
        $id = intval($_GP['id']);//栏目id
        if (empty($id) || empty($type)) ajaxReturnData(0,'参数错误');
        $where['ac_dish_status']=1;
        $actDishModel = new \model\activity_dish_model();
        if ($type == 1){
            $where['ac_in_id'] = $id;
            $flag = 'ac_p1_id';
        }else{
            $where['ac_p1_id'] = $id;
            $flag = 'ac_p2_id';
        }
            $res = $actDishModel->getAllActivtyDish($where,$flag);
            if ($res){
                foreach ($res as $key=>$v){
                    $idarr[] = $v[$flag];
                }
                $idarr = array_unique($idarr);
                //取二级栏目名称
                $shopCategoryModel = new \model\shop_category_model();
                foreach ($idarr as $k=>$v){
                    $category = $shopCategoryModel->getOneShopCategory(array('id'=>$v),'id,name,thumb');
                    if ($category){
                        $temp['id'] = $v;
                        $temp['name'] = $category['name'];
                        $temp['thumb'] = $category['thumb'];
                        $temp['type'] = 2;
                        $data[] = $temp;
                    }
                }
            }
        ajaxReturnData(1,'',$data);
    }
}