<?php
/**
 *分类接口
 */

namespace wapi\controller;
class category extends base{
    //取一级分类接口
    public function get_one()
   {
       //缓存一天，如果有数据则取缓存数据
       /* if(class_exists('Memcached')){
           deleteMemCache('CATEGORY_ONE');
           $memcache = new \Mcache();
           $data = $memcache->get('CATEGORY_ONE');
           if (!empty($data)) ajaxReturnData(1,'',$data);
       } */
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
           $memcache->set('CATEGORY_ONE', $data,86400);
       }
       ajaxReturnData(1,'',$data);
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
    //判断是否请求二级分类
    public function is_get_two(){
        $_GP = $this->request;
        $id = intval($_GP['id']);//栏目id
        $type = intval($_GP['type']);//类型
        $datatime = intval($_GP['datatime']);//栏目id
        
        if (empty($id)) ajaxReturnData(0,'参数错误');
        
        if ($type == 1){//表示给的是行业
            $flag = checkInsMemtime($id,$datatime);
        }elseif ($type == 2){//表示给的是一级栏目
            $flag = checkCat1Memtime($id,$datatime);
        }
        
        if ($flag) {
            ajaxReturnData(1,'',array('status'=>1));
        }else{
            ajaxReturnData(1,'',array('status'=>0));
        }
    }
    //根据一级分类id取二级分类
    public function get_two1(){
        $_GP = $this->request;
        $type = isset($_GP['type']) ? intval($_GP['type']) : 1;//类型
        $id = intval($_GP['id']);//栏目id
        
        if (empty($id) || empty($type)) ajaxReturnData(0,'参数错误');
        
        if ($type == 1){//根据行业id活动栏目id数组
            $returntype = 1;
            $catIdArr = getCategory1ByIns($id);
        }else {
            $returntype = 2;
            $catIdArr = getCategory2ByCategory1($id);
        }
        
        if (is_array($catIdArr)){//栏目id存在则取出其栏目信息
            $catInfo = array();
            foreach ($catIdArr['data'] as $v){
                $catInfo[] = getMemberCategoryInfo($v);
            }
            ajaxReturnData(1,'',array('catInfo'=>$catInfo,'datatime'=>$catIdArr['datatime'],'type'=>$returntype));
            ajaxReturnData(1,'',$catInfo);
        }else {//缓存中未取到栏目id，则需要从数据库获取然后重新加入缓存，并且返回
            $actDishModel = new \model\activity_dish_model();
            $getCatInfo = array();
            $datatime = '';
            if ($type == 1){//是行业
                $table = 'activity_dish';
                $where = " ac_in_id = '$id' and ac_dish_status=1 ";
                $sql = "SELECT ac_p1_id from ".table($table)." where ".$where." group by ac_p1_id";
                $getCatId = mysqld_selectall($sql);
                if (is_array($getCatId)){
                    foreach ($getCatId as $v){
                        addCat1byIns($id,$v['ac_p1_id']);//加入到该行业的缓存id
                        $getCatInfo[] = getCatInfoAndMemcache($v['ac_p1_id']);
                    }
                }
                if(class_exists('Memcached')){
                    $memcache = new \Mcache();
                    $key_time = "industry_memcache_category1_".$id."_member_time";//缓存时间
                    $key_time = md5($key_time);
                    $datatime = $memcache->get($key_time);
                }
                ajaxReturnData(1,'',array('catInfo'=>$getCatInfo,'datatime'=>$datatime,'type'=>1));
            }else{//是一级栏目
                $table = 'activity_dish';
                $where = " ac_p1_id = '$id' and ac_dish_status=1 ";
                $sql = "SELECT ac_p2_id from ".table($table)." where ".$where." group by ac_p2_id";
                $getCatId = mysqld_selectall($sql);
                if (is_array($getCatId)){
                    foreach ($getCatId as $v){
                        addCat2bycat1($id,$v['ac_p2_id']);//加入到该行业的缓存id
                        $getCatInfo[] = getCatInfoAndMemcache($v['ac_p2_id']);
                    }
                }
                if(class_exists('Memcached')){
                    $memcache = new \Mcache();
                    $key_time = "category1_memcache_category2_".$id."_member_time";//缓存时间
                    $key_time = md5($key_time);
                    $datatime = $memcache->get($key_time);
                }
                ajaxReturnData(1,'',array('catInfo'=>$getCatInfo,'datatime'=>$datatime,'type'=>2));
            }
        }
    }
}