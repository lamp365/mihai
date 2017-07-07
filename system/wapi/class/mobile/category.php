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
       
        if (count($industry) > 2){
            $industryModel = new \model\industry_model();
            $this->addMemcacheIns($data);
            foreach ($industry as $v){
                $info = $industryModel->getOneIndustry(array('gc_id'=>$v),'gc_id as id,gc_name as name');
                if ($info) $returndata[] = $info;
            }
            $type = 3;
        }else{
            $shopCatModel = new \model\shop_category_model();
            $this->addMemcacheP1($data);
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
        if ($type == 3){
            $return = $this->getMemcacheIns($id,$jd,$wd);
        }elseif ($type == 2){
            $return = $this->getMemcacheP1($id,$jd,$wd);
        }
        if (empty($return) || !is_array($return)) ajaxReturnData(0,'暂时没有栏目');
        
        $returndata = $result =array();
        if ($type == 3){
            foreach ($return as $val){
                $returnP2 = $this->getMemcacheP1($val,$jd,$wd);
                if ($returnP2){
                    $infoP1 = getCategoryById($val);
                    $tempdata['id'] = $infoP1['id'];
                    $tempdata['title'] = $infoP1['name'];
                    foreach ($returnP2 as $v){
                        $info = getCategoryById($v);
                        if (is_array($info)) {
                            $tempdata['list'][] = $info;
                        }
                    }
                    $returndata[] = $tempdata;
                }
            }
        }elseif ($type == 2){
            $result['id'] = '';
            $result['title'] = '';
            foreach ($return as $val){
                $info = getCategoryById($val);
                if (is_array($info)) $result['list'][] = $info;
            }
            $returndata[] = $result;
        }
        
        $allReturn = array('type'=>$type,'detail'=>$returndata);
        ajaxReturnData(1,'',$allReturn);
    }
   
    /**
     * 加入缓存 
     * 行业==》array(一级分类id)
     * **/
    private function addMemcacheIns($data){
        if (!is_array($data)) return false;
        $openid = checkIsLogin();
        $industry = array();
        foreach ($data as $key=>$val){
            $industry[$val['industry_p2_id']][$val['pcate']] = $val['pcate'];
        }
        foreach ($industry as $key=>$val){
            $id = array_keys($industry,$val);
            $string = implode(',', $val);
            $array = explode(",", $string);
            if(class_exists('Memcached')){
                $memcache = new \Mcache();
                $key = "industry_memcache_category1_".$openid."_".$id[0];
                $key = md5($key);
                $memcache->set($key, $array,300);
            }
        }
    }
    /**
     * 加入缓存 
     * 一级分类==》array(二级分类id)
     * **/
    private function addMemcacheP1($data){
        if (!is_array($data)) return false;
        $openid = checkIsLogin();
        $pcate = array();
        foreach ($data as $key=>$val){
            $pcate[$val['pcate']][$val['ccate']] = $val['ccate'];
        }
        foreach ($pcate as $key=>$val){
            $id = array_keys($pcate,$val);
            $string = implode(',', $val);
            $array = explode(",", $string);
            if(class_exists('Memcached')){
                $memcache = new \Mcache();
                $key = "category1_memcache_category2_".$openid."_".$id[0];
                $key = md5($key);
                $memcache->set($key, $array,300);
            }
        }
    }
    /**
     * 取出缓存数据
     * 行业==》array(一级分类id)
     *   */
    private function getMemcacheIns($id,$jd,$wd){
        if (empty($id)) return false;
        $openid = checkIsLogin();
        $catService = new \service\wapi\categoryService();
        $return = array();
        if(class_exists('Memcached')){
            $memcache = new \Mcache();
            $key = "industry_memcache_category1_".$openid."_".$id;
            $key = md5($key);
            $return = $memcache->get($key);
            if (empty($return)){
                $data = $catService->getAllInsName($jd,$wd);
                $this->addMemcacheIns($data);
                $this->addMemcacheP1($data);
                $return = $memcache->get($key);
            }
        }
        return $return;
    }
    /**
     * 取出缓存数据
     * 一级分类==》array(二级分类id)
     *   */
    private function getMemcacheP1($id,$jd,$wd){
        if (empty($id)) return false;
        $openid = checkIsLogin();
        $catService = new \service\wapi\categoryService();
        $return = array();
        if(class_exists('Memcached')){
            $memcache = new \Mcache();
            $key = "category1_memcache_category2_".$openid."_".$id;
            $key = md5($key);
            $return = $memcache->get($key);
            if (empty($return)){
                $data = $catService->getAllInsName($jd,$wd);
                $this->addMemcacheIns($data);
                $this->addMemcacheP1($data);
                $return = $memcache->get($key);
            }
        }
        return $return;
    }
}