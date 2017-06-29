<?php
/**
* 地区城市输出 按[parent_id]=array()来排列
* @author  王敬
*/
function getRegionStruct() {
    $region_category = mysqld_selectall("SELECT * FROM " . table('region') . "order by region_order ASC");
    foreach ($region_category as  $cate) {
        if (!empty($cate['parent_id'])) {
            $childrens[$cate['parent_id']][$cate['region_id']] = array(
                $cate['region_id'],
                $cate['region_name']
            );
        }
    }
    return $childrens;
}




function recursiveRegionAssort($assortPid = 0) {
    if (extension_loaded('Memcache')) {
        $mcache = new Mcache();
        $arr = $mcache->get('cache_func_recursiveRegionAssort');
    }
    if(!$arr){
        $children = mysqld_selectall("SELECT * FROM " . table('region') . " where  parent_id = $assortPid order by region_order ASC");
        if ( $children ) {
            foreach ( $children as $value ) {
                $value[ 'branch' ] = recursiveRegionAssort( $value[ 'region_id' ] );
                $arr[  ] = $value; //组合数组 
            }
        } else { }
        $mcache->set('cache_func_recursiveRegionAssort', $arr);//地区数据基本不会过期，可以设置永不过期
    }
    return $arr;
}
/**
* 自定义地区分类输出，按[parent_id]=array()来排列
* @author  王敬
*/

function getCustomRegionStruct() {
    $region_category = mysqld_selectall("SELECT * FROM " . table('region_custom') . "order by region_order ASC");
    foreach ($region_category as  $cate) {
        if (!empty($cate['parent_id'])) {
            $childrens[$cate['parent_id']][$cate['region_id']] = array(
                $cate['region_id'],
                $cate['region_name']
            );
        }
    }
    return $childrens;
}

function getProvincesOfRegion() {
	$result = mysqld_selectall("SELECT region_id,region_name,region_code  FROM " . table('region') . " WHERE  parent_id=1 ORDER BY region_order ASC");
    return $result;
}

//返回子类id
function getChildrenOfRegion($parent_id=1,$fields='*'){
   $result = mysqld_selectall("select {$fields} from ".table('region')." where parent_id = {$parent_id} order by region_order ASC");
   return $result;
}

function region_func_getNameByCode($id) {
    if(!$id){return ;}
    if (extension_loaded('Memcached')) {
        $mcache = new Mcache();
        // 登陆初始化
        $data_cache = $mcache->get('region_func_getNameByCode');
        if(!$data_cache){
            $all_data = mysqld_selectall("select region_code,region_name from ".table('region') );
            $data_cache =  array_column($all_data, 'region_name','region_code');
            $mcache->set('region_func_getNameByCode',$data_cache,3600);//缓存一小时
        }
        return $data_cache[$id];
    }else{
        $data = mysqld_select("SELECT region_name FROM " . table('region') . " WHERE region_code = ".$id);
        return $data['region_name'];
    }
}

function getIndustryByid($id){
    if(!$id){return '' ;}
    $industry = mysqld_select("select gc_name from ".table('industry')." where gc_id={$id}");
    return $industry['gc_name'];
}

//通过经纬度访问区域code
function getCodeByLttAndLgt($jd,$wd){
    if (empty($jd) || empty($wd)) return false;
    $key = GD_KEY;
    $url = AL_CODE;
    $location = $jd.",".$wd;
    
    $url .="key=$key&location=$location"; 
    return http_get($url);
}
//通过ip取得城市名称
function getCodeByIP($ip){
    if (empty($ip)) return false;
    $key = GD_KEY;
    $url = GD_IP;
    $url .="key=$key&ip=$ip";
    return http_get($url);
}
//根据经纬度获取区域id
function getAreaid($jd,$wd){
    $openid = get_member_account();
    $key = $openid."_LOCATION";
    //缓存5分钟，如果有数据则取缓存数据
     if(class_exists('Memcached')){
         $memcache = new \Mcache();
         $data = $memcache->get($key);
         if (!empty($data)) return $data;
     }
     
    if (empty($jd) || empty($wd)) return '';
     //高德接口获取区域id
    $return = json_decode(getCodeByLttAndLgt($jd,$wd),1);
    if ($return['infocode'] != 10000){
        return array(
            'status'=>0,
            'ac_city'=>'350100',
        );
    }
    $ac_city_area = isset($return['regeocode']['addressComponent']['adcode'])?$return['regeocode']['addressComponent']['adcode']:'';
    
    //取市id
    $regionModel = new \model\region_model();
    $info = $regionModel->getPCodeByCCode($ac_city_area);
    $ac_city = !empty($info) ? $info['region_code']:'';
    
    if (empty($ac_city) || empty($ac_city_area)) {
        return array(
            'status'=>0,
            'ac_city'=>'350100',
        );
    }
    $data = array(
        'status'=>1,
        'ac_city'=>$ac_city,
        'ac_city_area'=>$ac_city_area
    
    );
    //加入缓存
    if(class_exists('Memcached')){
        $memcache = new \Mcache();
        $memcache->set($key, $data,300);
    }
    return $data;
}
//通过ip取区域id
function getCityidByIp(){
    $ip = getClientIP();
    //高德地图通过ip取城市
    $info = getCodeByIP($ip);
    $cityCode = '';
    if ($info){
        $info = json_decode($info,1);
        $cityCode = $info['adcode'];
    }
    logg($ip, "ip");
    logg(var_export($info,1), "info");
    if (empty($cityCode)) return '350100';//如果未取到ip，则取福州
    return $cityCode;
}






