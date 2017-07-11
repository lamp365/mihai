<?php 
/**
 * 取出当前活动
 *   */
function getCurrentAct(){
    $now = time();
    $where = " ac_status=1 and ac_time_end > $now  and ac_time_str < $now ";
    $sql = "SELECT ac_id,ac_title,ac_time_str,ac_time_end,ac_area FROM ".table('activity_list')." where ".$where;
    $list = mysqld_selectall($sql);
    if($list){
        return $list[0];//默认只有一个
    }
}

/**
 * 判断商品是否在限时购中:正在限时购，或者还没有开始的限时购
 */
function getDishIsOnActive($dishid){
    if (empty($dishid)) return '';
    $now = time();
    $where = " ac_status=1 and ac_time_end > $now ";
    $sql = "SELECT ac_id,ac_title,ac_time_str,ac_time_end,ac_area FROM ".table('activity_list')." where ".$where;
    $list = mysqld_selectall($sql);
    $find   = array();
    if(!empty($list)){
        $acIdArr = array();
        foreach ($list as $key=>$v){
            $acIdArr[] = " ac_action_id =".$v['ac_id'];
        }
        $acIdStr = implode(" or ", $acIdArr);
        
        $where = " ac_shop_dish={$dishid} ";
        $where .= ' and ('.$acIdStr.')';
        //有活动，那么判断该商品是不是属于限时购商品
        $sql = "select ac_dish_id,ac_dish_status,ac_dish_total,ac_dish_price,ac_action_id from ".table('activity_dish')." where ";
        $sql .= $where;
        $find = mysqld_select($sql);
    }
    return $find;
}
/**
 * 同步库存
 * 如果shop_dish的库存小于activity_dish的库存，则需要同步一下库存
 *   */
 function synchroscope_store_count($store_cont,$act_count,$ac_dish_id){
     if (!empty($find)) {
         //校验一下活动表的库存跟dish表的库存
         if($store_cont < $act_count){
             mysqld_update('activity_dish',array('ac_dish_total'=>$store_cont),array('ac_dish_id'=>$ac_dish_id));
         }
     }
 }
 
 /**
  * 一级栏目id加入到该行业id的缓存,并且更新该行业id的缓存的时间
  * @param  $insId 行业id
  * @param  $catId1 一级栏目id  
  * */
 function addCat1byIns($insId,$catId1){
     if (empty($insId) || empty($catId1)) return false;
     $key = "industry_memcache_category1_id_".$insId."_by_industry";
     $key_time = "industry_memcache_category1_".$insId."_member_time";//缓存时间
     $key = md5($key);
     $key_time = md5($key_time);
     $mytime = time();
     if(class_exists('Memcached')){
         $memcache = new \Mcache();
         $data = $memcache->get($key);
         if (empty($data)) {//第一次加入缓存
             $insert[] = $catId1;
             $memcache->set($key,$insert);
             $memcache->set($key_time,$mytime);
         }elseif (in_array($catId1, $data)){//id已经在缓存中了，还缓存毛线
             
         }else{//缓存中有数据了，则追加
             $data[] = $catId1;
             $memcache->set($key,$data);
             $memcache->set($key_time,$mytime);
         }
     }
     //return $mytime;
 }
 
 /**
  * 二级栏目id加入到该一级栏目id的缓存,并且更新该一级栏目id的缓存的时间
  * @param  $insId 行业id
  * @param  $catId1 一级栏目id  
  * */
 function addCat2bycat1($catid1,$catid2){
     if (empty($catid1) || empty($catid1)) return false;
     $key = "category1_memcache_category2_id_".$catid1."_by_category1";
     $key_time = "category1_memcache_category2_".$catid1."_member_time";//缓存时间
     $key = md5($key);
     $key_time = md5($key_time);
     $mytime = time();
     if(class_exists('Memcached')){
         $memcache = new \Mcache();
         $data = $memcache->get($key);
         if (empty($data)) {//第一次加入缓存
             $insert[] = $catid2;
             $memcache->set($key,$insert);
             $memcache->set($key_time,$mytime);
         }elseif (in_array($catid2, $data)){//id已经在缓存中了，还缓存毛线
             
         }else{//缓存中有数据了，则追加
             $data[] = $catid2;
             $memcache->set($key,$data);
             $memcache->set($key_time,$mytime);
         }
     }
     //return $mytime;
 }
 
 /**
  * 根据栏目id取栏目信息，并且把信息缓存住
  *
  *   */
 function getCatInfoAndMemcache($catid){
     if (empty($catid)) return false;
     $shopCatModel = new \model\shop_category_model();
     $catInfo = array();
     $catInfo = $shopCatModel->getOneShopCategory(array('id'=>$catid),'id,name,thumb');
     if ($catInfo) {
         if(class_exists('Memcached')){
             $key = "categoryInfo_".$catid."_bycatid";
             $key = md5($key);
             $memcache = new \Mcache();
             $info = $memcache->get($key);
             if (!is_array($info)){
                 $memcache->set($key, $catInfo);
             }
         }
     }
     return $catInfo;
 }
 
 /**
  * 根据行业id，判断其缓存的一级栏目的时间是否发生改变
  * @param $insId 行业id
  * @param $checkTime 需要验证的缓存时间
  *   */
 function checkInsMemtime($insId,$checkTime){
     if (empty($insId) || empty($checkTime)) return false;
     $key_time = "industry_memcache_category1_".$insId."_member_time";//缓存时间
     $key_time = md5($key_time);
     if(class_exists('Memcached')){
         $memcache = new \Mcache();
         $getmemtime = $memcache->get($key_time);
         if ($getmemtime == $checkTime) return true;
     }
 }
 /**
  * 根据行业id，获得缓存一级栏目的id
  * @param $insId 行业id
  *   */
 function getCategory1ByIns($insId){
     if (empty($insId)) return false;
     $key = "get_memcache_category1_".$insId."_by_indutry";
     $key_time = "industry_memcache_category1_".$insId."_member_time";//缓存时间
     $key = md5($key);
     $key_time = md5($key_time);
     if(class_exists('Memcached')){
         $memcache = new \Mcache();
         $data = $memcache->get($key);
         $datatime = $memcache->get($key_time);
         logRecord(var_export($data), 'cat1byins');
         return array('data'=>$data,'datatime'=>$datatime);
     }
 }
 /**
  * 根据一级栏目id，判断其缓存的二级栏目的时间是否发生改变
  * @param $catid1 一级栏目id
  * @param $checkTime 需要验证的缓存时间
  *   */
 function checkCat1Memtime($catId1,$checkTime){
     if (empty($catId1) || empty($checkTime)) return false;
     $key_time = "category1_memcache_category2_".$catId1."_member_time";//缓存时间
     $key_time = md5($key_time);
     if(class_exists('Memcached')){
         $memcache = new \Mcache();
         $getmemtime = $memcache->get($key_time);
         if ($getmemtime == $checkTime) return true;
     }
 }
 /**
  * 根据一级栏目id，获得缓存二级栏目的id
  * @param $catId1 一级栏目id
  *   */
 function getCategory2ByCategory1($catId1){
     if (empty($catId1)) return false;
     $key = "get_memcache_category2_".$catId1."_by_category1";
     $key_time = "category1_memcache_category2_".$catId1."_member_time";//缓存时间
     $key = md5($key);
     $key_time = md5($key_time);
     if(class_exists('Memcached')){
         $memcache = new \Mcache();
         $data = $memcache->get($key);
         $datatime = $memcache->get($key_time);
         logRecord(var_export($data), 'cat2bycat1');
         return array('data'=>$data,'datatime'=>$datatime);
     }
 }
 /**
  * 根据栏目id，获得缓存的栏目信息
  * @param $catid 栏目id
  *   */
 function getMemberCategoryInfo($catid){
     if (empty($catId1)) return false;
     $key = "get_memcache_category_info".$catid."_by_category_id";
     $key = md5($key);
     if(class_exists('Memcached')){
         $memcache = new \Mcache();
         return $memcache->get($key);
     }
 }
 /**
  * 根据经纬度获取code
  * 
  * @param $jd 经度
  * @param $wd 纬度 */
 function get_area_code_by_jw($jd,$wd){
     //经纬度地区
     if (empty($jd) || empty($wd)){
         $cityCode = getCityidByIp();
         return array('status'=>0,'citycode'=>$cityCode);
     }else{
         $jdwd = getAreaid($jd,$wd);
         if (empty($jdwd)) return false;
         if ($jdwd['status'] == 0) {//取默认城市
             $cityCode = $jdwd['ac_city'];
             return array('status'=>0,'citycode'=>$cityCode);
         }else{
             $cityCode = $jdwd['ac_city'];
             $areaCode = $jdwd['ac_city_area'];
             return array('status'=>1,'citycode'=>$cityCode,'areaCode'=>$areaCode);
         }
     }
 }
 /**
  * 根据经纬度查询的sql条件封装
  * @param $jd 经度
  * @param $wd 纬度
  * return string
  *   */
 function get_area_condition_sql($jd,$wd){
     if (empty($jd) || empty($wd)){
         $cityCode = getCityidByIp();
         $sql =" and (a.ac_city='$cityCode' or a.ac_city=0) ";
     }else{
         $jdwd = getAreaid($jd,$wd);
         if (empty($jdwd)) return false;
         if ($jdwd['status'] == 0) {//取默认城市
             $cityCode = $jdwd['ac_city'];
             $sql =" and (a.ac_city='$cityCode' or a.ac_city=0) ";
         }else{
             $ac_city = $jdwd['ac_city'];
             $ac_city_area = $jdwd['ac_city_area'];
             //$sql .= " and IF(a.ac_city='$ac_city',a.ac_city_area='$ac_city_area' or a.ac_city_area=0,IF(a.ac_city_area=0,a.ac_city=0,a.ac_city_area='$ac_city_area'))";
             $sql = " and IF(a.ac_city='$ac_city',a.ac_city_area='$ac_city_area' OR a.ac_city_area=0,a.ac_city=0) ";
         }
     }
     return $sql;
 }

function getAreaTitleByAreaid($ac_area_id){
    if(empty($ac_area_id)) return '全时段';
    $ac_area_id = intval($ac_area_id);
    $sql  = "select ac_area_title from ".table('activity_area')." where ac_area_id={$ac_area_id}";
    $area = mysqld_select($sql);
    return $area['ac_area_title'];
}

function getRegionName($region_code){
    if(empty($region_code)) return '';
    $region = mysqld_select('select region_name from '.table('region')." where region_code='{$region_code}'");
    return $region['region_name'];
}
?>