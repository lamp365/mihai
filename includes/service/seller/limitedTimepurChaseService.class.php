<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class limitedTimepurChaseService extends \service\publicService {
    private $memberData;
    private $table_area;
    private $table_dish;
    private $table_list;
    private $dishObj;           //时间戳
    private $nowtime;           //时间戳
    private $nowHour;           //当前小时
            
    function __construct() {
       parent::__construct();
       $this->memberData   = get_member_account();
       $this->table_area   = table('activity_area');
       $this->table_dish   = table('activity_dish');
       $this->table_list   = table('activity_list');
       $this->nowtime      = time();
       $this->dishObj      = new ShopDishService();
       $this->storeObj     = new shopStoreService();
       $this->nowHour      = date('H',time());
   }
   
   //获取时间区间列表
   public function getAreaGroup($fields='ac_list_id'){
       $sql = "SELECT {$fields} FROM {$this->table_area} group by ac_list_id";
       $data  = mysqld_selectall($sql);
       return $data;
   }
   
   //通过listID 获取对应的listid
   public function getAreaList($ac_id){
       $sql = "select ac_area from {$this->table_list} where ac_id = {$ac_id}";
       $rs = mysqld_select($sql);
       return $rs['ac_area'];
   }
   
   //根据时间区间分组获取具体内容
   public function getAreaGroupList($ac_list_id=0,$fields='*'){
       $sql = "SELECT {$fields} FROM {$this->table_area} where ac_list_id = '{$ac_list_id}' and ac_area_status = 1";
       $data  = mysqld_selectall($sql);
       return $data;
   }
   
   //获取活动列表  结束时间必须大于当前时间
   public function getListAll($fields='*',$is_now=0){
       if($is_now > 0)
       {
           $where = 'ac_time_str > '.$this->nowtime;
       }
       else{
           $where = 'ac_time_end > '.$this->nowtime;
       }
       $sql = "SELECT {$fields} FROM ".$this->table_list.' where ac_time_end > '.$this->nowtime.' and ac_status = 1';
       $data  = mysqld_selectall($sql);
       return $data;
   }
   
   public function addActivityDish($data){
       $rsdata = array();
       $rsdata['ac_action_id']  = $data['ac_action_id'];
       $rsdata['ac_area_id']    = intval($data['ac_area_id'])>0?intval($data['ac_area_id']):0;
       $rsdata['ac_p1_id']      = $data['ac_p1_id'];
       $rsdata['ac_p2_id']      = $data['ac_p2_id'];
       $rsdata['ac_dish_total'] = $data['ac_dish_total'];
       $rsdata['ac_dish_id']    = $data['ac_dish_id'];
       if($rsdata['ac_dish_id'] <= 0)
       {
            $rsdata['ac_shop_dish']  = $data['ac_shop_dish']; 
       }
       else{
            //获取dishid
            $dishid_sql = "select ac_shop_dish from squdian_activity_dish where ac_dish_id = {$rsdata['ac_dish_id']}";
            $dishid_rs  = mysqld_select($dishid_sql);
            $rsdata['ac_shop_dish']  = $dishid_rs['ac_shop_dish'];
            if($rsdata['ac_shop_dish'] <= 0)
            {
                return -5;
            }
            
            $where = '';
            $where .= " and ac_dish_id != {$rsdata['ac_dish_id']}";
       }
       
       //判断该产品是否属于该店铺
       $checkDish = $this->dishObj->checkStoreDish($rsdata['ac_shop_dish'], $this->memberData['store_sts_id']);
       if($checkDish['id'] <= 0)
        {
           return -4;
        }
       
       //判断某个宝贝是否已参与了某个活动了 如果参与则不用再次加入
       $activity_dish_sql = "select ac_dish_id,ac_area_id from squdian_activity_dish where ac_shop_dish = {$rsdata['ac_shop_dish']} and ac_action_id = {$rsdata['ac_action_id']} $where";
       $activity_dish_rs  = mysqld_select($activity_dish_sql);
       if($activity_dish_rs['ac_dish_id'] > 0)
       {
           //if($activity_dish_rs['ac_area_id'] == 0 || $activity_dish_rs['ac_area_id'] == $rsdata['ac_area_id']){
             return -1;
           //}
       }
       
       //判断活动是否存在且未被禁用
       $actiListInfo = $this->getActiInfo($rsdata['ac_action_id'],'ac_id');
       $ac_id_data = intval($actiListInfo['ac_id']);
       if($ac_id_data <= 0)
       {
           return -6;
       }
       
       //判断价格是否大于原产品促销价格 ac_dish_total
       $dishInfo = $this->dishObj->getDishInfo($rsdata['ac_shop_dish'],'marketprice');
       $rsdata['ac_dish_price'] = FormatMoney($data['ac_dish_price']);
       if($rsdata['ac_dish_price'] > $dishInfo['marketprice'])
       {
            return -2;
       }
       
       //获取城市code,城市区域code
       $storeShopInfo                = $this->storeObj->getStoreShop('sts_city,sts_region');
       $rsdata['ac_city']            = $storeShopInfo['sts_city'];
       $rsdata['ac_city_area']       = $storeShopInfo['sts_region'];
       $rsdata['ac_in_id']           = $this->memberData['sts_category_p1_id'];
       $rsdata['ac_shop']            = $this->memberData['store_sts_id'];
       $rsdata['ac_dish_status']     = 0;

       if($rsdata['ac_dish_id'] > 0)
       {
           mysqld_update('activity_dish',$rsdata,array('ac_dish_id'=>$rsdata['ac_dish_id']));
           return 1;
       }
       else{
            mysqld_insert('activity_dish',$rsdata);
            $acti_id = mysqld_insertid();    //获取上一次插入的ID 
            if($acti_id > 0)
            {
                return 1;
            }
            else{
                //插入失败
                return -3;
            }
       }
   }
   
   //删除
    public function delActivityDish($ac_dish_id){
        //判断该产品是否属于该店铺
        //获取dishid
        $dishid_sql = "select ac_shop_dish from squdian_activity_dish where ac_dish_id = {$ac_dish_id}";
        $dishid_rs  = mysqld_select($dishid_sql);
        if($dishid_rs['ac_shop_dish'] <= 0)
        {
            return -5;
        }
        $checkDish = $this->dishObj->checkStoreDish($dishid_rs['ac_shop_dish'], $this->memberData['store_sts_id']);
        if($checkDish['id'] <= 0)
        {
           return -4;
        }
       
        $sql = "delete from {$this->table_dish} where ac_dish_id = {$ac_dish_id}";
        $rs  = mysqld_query($sql);
        
        return 1;
    }
   
  //判断某个宝贝已是否已经参与限时购
   public function isCheckDish($dish_id){
       $sql = "select ac_dish_id from {$this->table_dish} where ac_shop_dish = {$dish_id}";
       $rs  = mysqld_select($sql);
       return $rs["ac_dish_id"];
   }
   
   //通过宝贝ID获取对应的限时购信息
   public function getLtcDish($ac_dish_id,$fields='*'){
       $sql = "select $fields from {$this->table_dish} where ac_dish_id = {$ac_dish_id}";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   public function changeActivityDish($ac_dish_id=0,$ac_dish_status=0){
       $sql = "update {$this->table_dish} set ac_dish_status = {$ac_dish_status} where ac_dish_id = {$ac_dish_id}";
       $rs  = mysqld_query($sql);
       return $rs;
   }
   
   //获取今天在进行中的活动
   public function getDayActivity($fields='*'){
       $dayTime = strtotime(date('Y-m-d'));
       $sql = "select $fields from {$this->table_list} where ac_time_str <= {$dayTime}";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   //获取当前时段在进行中的活动
   public function getDayActivityArea($fields='*'){
       $dayTime = strtotime(date('Y-m-d'));
       $now_hour = date('H',time());
       $sql = "SELECT {$fields} FROM {$this->table_list} AS a left JOIN {$this->table_area} AS b ON a.ac_area = b.ac_list_id WHERE a.ac_time_str <= {$dayTime}  AND FROM_UNIXTIME(b.ac_area_time_str, '%H') <= {$now_hour} and (FROM_UNIXTIME(b.ac_area_time_end, '%H') > {$now_hour} or FROM_UNIXTIME(b.ac_area_time_end, '%H') = 0)  ORDER BY FROM_UNIXTIME(b.ac_area_time_str, '%H') desc LIMIT 1";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   //获取明天进行的第一场
   public function getTomorrowActivityArea($fields='*'){
       $dayTime = strtotime(date('Y-m-d'))+86400;
       $now_hour = date('H',time());
       $sql = "SELECT {$fields} FROM {$this->table_list} AS a left JOIN {$this->table_area} AS b ON a.ac_area = b.ac_list_id WHERE a.ac_time_str >= {$dayTime} ORDER BY FROM_UNIXTIME(b.ac_area_time_str, '%H') asc LIMIT 1";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   //获取即将进行的活动
   public function getTrailerActivity($fields='*'){
       $dayTime = strtotime(date('Y-m-d'));
       $where = '';
       if($data['ac_action_id'] > 0){
           $where = " and ac_action_id = {$data['ac_action_id']}";
       }
       $sql = "select $fields from {$this->table_list} where ac_time_end >= {$dayTime}";
       $rs  = mysqld_selectall($sql);
       return $rs;
   }
   
   //获取进行中的活动宝贝列表
   public function getUnderwayActiList($data,$fields='*',$shop=1){
       $data['page'] = max(1, intval($data['page']));
       $data['limit'] = $data['limit']>0?$data['limit']:10; 
       $limit = " LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit'];
       $where  = '';
       $where .= "(b.ac_area_id = {$data['ac_area_id']} or a.ac_area_id = 0) and ac_action_id = {$data['ac_id']} and a.ac_dish_status = 1";
       if($shop > 0)$where .= " and ac_shop = {$this->memberData['store_sts_id']}";
       //$sql = "SELECT {$fields} FROM {$this->table_dish} AS a LEFT JOIN {$this->table_area} AS b ON a.ac_area_id = b.ac_area_id where $where ORDER BY FROM_UNIXTIME(b.ac_area_time_str,'%H:%i:%s') ASC {$limit}";
       $sql = "SELECT {$fields} FROM {$this->table_dish} AS a LEFT JOIN {$this->table_area} AS b ON a.ac_area_id = b.ac_area_id where $where ORDER BY b.ac_area_time_str ASC {$limit}";
       $dishList  = mysqld_selectall($sql);

        $dishList['total'] = mysqld_select("SELECT count(0) as total FROM {$this->table_dish} AS a LEFT JOIN {$this->table_area} AS b ON a.ac_area_id = b.ac_area_id where $where");
        $dishList['total'] = intval($dishList['total']['total']);
        unset($dishList['total']['total']);
        return $dishList;
   }
   
   //
   public function getTrailerActiList($data,$fields='ac_dish_id,a.ac_area_id,ac_shop_dish,b.ac_area_time_str,ac_time_str,ac_dish_price,ac_dish_total,ac_id',$fields1='ac_dish_id,a.ac_area_id,ac_shop_dish,ac_time_str,ac_dish_price,ac_dish_total,ac_id',$shop=1){
       $data['page'] = max(1, intval($data['page']));
       $data['limit'] = $data['limit']>0?$data['limit']:10; 
       $limit = " LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit'];
       $where  = '';
       $where .= "ac_action_id in ({$data['ac_ids']})";
       $where2 = '';
       if($data['ac_area_id'] !== null){
           $where2 .= " and a.ac_area_id = {$data['ac_area_id']}";
       }
       if($shop > 0)$where .= " and ac_shop = {$this->memberData['store_sts_id']}";
       //$this->nowHour
       $sql = "select {$fields} from (SELECT {$fields1} FROM {$this->table_dish} AS a LEFT JOIN {$this->table_list} AS b ON a.ac_action_id = b.ac_id WHERE {$where}) as a LEFT JOIN {$this->table_area} as b on a.ac_area_id = b.ac_area_id where (FROM_UNIXTIME(b.ac_area_time_str,'%H') >= {$this->nowHour} or a.ac_area_id = 0)  {$where2} order by ac_time_str asc,b.ac_area_time_str asc {$limit}";
       $dishList  = mysqld_selectall($sql);
       
        $dishList['total'] = mysqld_select("select count(0) as total from (SELECT {$fields1} FROM {$this->table_dish} AS a LEFT JOIN {$this->table_list} AS b ON a.ac_action_id = b.ac_id WHERE {$where}) as a LEFT JOIN {$this->table_area} as b on a.ac_area_id = b.ac_area_id");
        $dishList['total'] = intval($dishList['total']['total']);
        unset($dishList['total']['total']);
        return $dishList;
   }
   
   //获取某个时间段的日期列表
   public function getTimeList(){
       $end_time = $this->nowtime + (86400*15);
       $sql_start = 'select ac_time_str from '.$this->table_list.' where ac_time_end >= UNIX_TIMESTAMP(NOW()) order by ac_time_str asc limit 1';
       $rs_start  = mysqld_select($sql_start);
       
       $sql_end = 'select ac_time_end from '.$this->table_list.' where ac_time_end between UNIX_TIMESTAMP(NOW()) and '.$end_time.' order by ac_time_end desc limit 1';
       $rs_end  = mysqld_select($sql_end);

       $timeNum = ($rs_end['ac_time_end'] - $rs_start['ac_time_str'])/86400;
       $time_arr = array();
       for($i=0;$i<=$timeNum;$i++){
           $timeDau = $rs_start['ac_time_str'] + ($i*86400);
           $time_arr[$i]['date'] = date('Y-m-d',$timeDau);
       }
       
       return $time_arr;
   }
   
   //获取某天的活动的时间段列表
   public function timeAreaList($ac_id){
       $sql = "SELECT a.ac_id,ac_title,ac_area_time_str,ac_area_time_end,b.ac_area_id FROM {$this->table_list} AS a LEFT JOIN  {$this->table_area} AS b ON a.ac_area = b.ac_list_id WHERE a.ac_id = {$ac_id} AND b.ac_area_status = 1 ORDER BY FROM_UNIXTIME(b.ac_area_time_str,'%H:%i:%s') ASC";
       $rs = mysqld_selectall($sql);
       return $rs;
   }
   
   //通过ID获取对应的信息
   public function getActivityDishInfo($ac_dish_id=0,$fields='*'){
       $sql = "select {$fields} from {$this->table_dish} where ac_dish_id = {$ac_dish_id}";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   //获取某个时间段的活动宝贝列表
   public function getAreaDish($ac_action_id,$area_id,$field='*'){
       $sql = "select {$field} from {$this->table_dish} where ac_action_id={$ac_action_id} and area_id = {$area_id}";
       $rs  = mysqld_selectall($sql);
       return $rs;
   }
   
   //获取未被停止的活动
   public function getActiInfo($ac_action_id,$fields='*'){
       if($ac_action_id <= 0)
       {
           return -1;
       }
       $sql = "select {$fields} from {$this->table_list} where ac_id = {$ac_action_id} and ac_status = 1";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   //获取进行中的小时数
   public function getNowArea($ac_area_id,$fields='*'){
       $ac_area_id = intval($ac_area_id);
       $sql = "select {$fields} from {$this->table_area} where ac_list_id = {$ac_area_id} and FROM_UNIXTIME(ac_area_time_end,'%H') >= {$this->nowHour} order by FROM_UNIXTIME(ac_area_time_end,'%H') asc limit 1";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   //获取下一场进行中的小时数
   public function getNextArea($ac_area_id,$fields='*'){
       $ac_area_id = intval($ac_area_id);
       $sql = "select {$fields} from {$this->table_area} where ac_list_id = {$ac_area_id} and FROM_UNIXTIME(ac_area_time_end,'%H') > {$this->nowHour} order by FROM_UNIXTIME(ac_area_time_end,'%H') asc limit 1";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
} 
?>