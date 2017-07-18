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
       //判断行业数目
       $catService = new \service\wapi\categoryService();
       $_GP = $this->request;
       $openid = checkIsLogin();
       //判断该区域是否有商品
       $jd = isset($_GP['longitude']) ? $_GP['longitude'] : '';//经度
       $wd = isset($_GP['latitude']) ? $_GP['latitude'] : '';//纬度
       $data = $catService->getAllInsName($jd,$wd);
       if (empty($data)) ajaxReturnData('0','暂时没有栏目');
       $industry = array();
       $pcate = array();
       foreach ($data as $v){
           $industry[] = $v['industry_p2_id'];
           $pcate[] = $v['pcate'];
       }
       $industry = array_flip(array_flip($industry));
       logRecord(var_export($industry,1), "categoryone");
       $pcate = array_flip(array_flip($pcate));
       
        if (count($industry) >= 2){//行业作为一级分类
            $industryModel = new \model\industry_model();
            $catService->addMemcacheIns($data);//加入缓存   行业====》该行业的一级栏目id
            foreach ($industry as $v){//取行业名称
                $info = $industryModel->getOneIndustry(array('gc_id'=>$v),'gc_id as id,gc_name as name');
                if ($info) $returndata[] = $info;
            }
            $type = 3;
        }else{//一级栏目作为一级分类，取一级分类名称
            $shopCatModel = new \model\shop_category_model();
            $catService->addMemcacheP1($data);//加入缓存  一级栏目====》该一级栏目的二级栏目的id
            foreach ($pcate as $v){
                $info = $shopCatModel->getOneShopCategory(array('id'=>$v),'id,name');
                $returndata[] = $info;
            }
            $type = 2;
        }
        if (empty($returndata)) ajaxReturnData(0,'暂时没有栏目');
       //缓存5分钟
       /* if(class_exists('Memcached')){
           $memcache = new \Mcache();
           $memcache->set('CATEGORY_ONE',$returndata,300);
       } */
       ajaxReturnData(1,'',array('type'=>$type,'data'=>$returndata));
   }
   //判断是否请求二级分类
   public function is_get_two(){
       $_GP = $this->request;
       $id = intval($_GP['id']);//栏目id
       $type = intval($_GP['type']);//类型
       $key_time = intval($_GP['key_time']);//时间
       $jd = isset($_GP['longitude']) ? $_GP['longitude'] : '';//经度
       $wd = isset($_GP['latitude']) ? $_GP['latitude'] : '';//纬度
   
       if (empty($id) || empty($type)) ajaxReturnData(0,'参数错误');
       $catService = new \service\wapi\categoryService();
       
       if ($type == 3){//表示给的是行业
           $flag = $catService->checkIsIndustry($id,$key_time,$jd,$wd);
       }elseif ($type == 2){//表示给的是一级栏目
           $flag = $catService->checkIsCat1($id,$key_time,$jd,$wd);
       }
   
       if ($flag) {
           ajaxReturnData(1,'',array('status'=>1));
       }else{
           ajaxReturnData(1,'',array('status'=>0));
       }
   }
   //根据一级分类id取二级分类
    public function get_two(){
        $_GP = $this->request;
        $type = intval($_GP['type']);//类型
        $id = intval($_GP['id']);//栏目id
        if (empty($id) || empty($type)) ajaxReturnData(0,'参数错误');
        //判断该区域是否有商品
        $jd = isset($_GP['longitude']) ? $_GP['longitude'] : '';//经度
        $wd = isset($_GP['latitude']) ? $_GP['latitude'] : '';//纬度
        $catService = new \service\wapi\categoryService();
        if ($type == 3){//缓存中取数据，如果没有，则getMemcacheIns方法自动会取所有数据，然后再加入缓存
            $res = $catService->getMemcacheIns($id,$jd,$wd);
        }elseif ($type == 2){//缓存中取数据，如果没有取到，则getMemcacheP1方法自动会取所有数据，然后再加入缓存
            $res = $catService->getMemcacheP1($id,$jd,$wd);
        }
        $return = $res['data'];
        $key_time = $res['key_time'];
        if (empty($return) || !is_array($return)) ajaxReturnData(0,'暂时没有栏目');
        
        $returndata = $result =array();
        if ($type == 3){//三级，则根据第一级取第二级，再根据第二级取第三级
            foreach ($return as $val){
                $returnP2 = $catService->getMemcacheP1($val,$jd,$wd);
                if ($returnP2){
                    $tempdata = array();
                    $infoP1 = getCategoryById($val);
                    $tempdata['id'] = $infoP1['id'];
                    $tempdata['title'] = $infoP1['name'];
                    foreach ($returnP2['data'] as $v){
                        $info = getCategoryById($v);
                        if (is_array($info)) {
                            $tempdata['list'][] = $info;
                        }
                    }
                    
                    $returndata[] = $tempdata;
                }
            }
        }elseif ($type == 2){//二级
            $result['id'] = '';
            $result['title'] = '';
            foreach ($return as $val){
                $info = getCategoryById($val);
                if (is_array($info)) $result['list'][] = $info;
            }
            $returndata[] = $result;
        }
        
        $allReturn = array('type'=>$type,'detail'=>$returndata,'key_time'=>$key_time);
        ajaxReturnData(1,'',$allReturn);
    }
   
   
}