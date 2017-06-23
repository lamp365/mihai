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
        
    function __construct() {
       parent::__construct();
       $this->memberData   = get_member_account();
       $this->table_area   = table('activity_area');
       $this->table_dish   = table('activity_dish');
       $this->table_list   = table('activity_list');
       $this->nowtime      = time();
       $this->dishObj      = new ShopDishService();
       $this->storeObj     = new shopStoreService();
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
       $sql = "SELECT {$fields} FROM {$this->table_area} where ac_list_id = '{$ac_list_id}'";
       $data  = mysqld_selectall($sql);
       return $data;
   }
   
   
   //获取活动列表  结束时间必须大于当前时间
   public function getListAll($fields='*'){
       $sql = "SELECT {$fields} FROM ".$this->table_list.' where ac_time_end > '.$this->nowtime.' and ac_status = 1';
       $data  = mysqld_selectall($sql);
       return $data;
   }
   
   public function addActivityDish($data){
       $rsdata = array();
       $rsdata['ac_action_id'] = $data['ac_action_id'];
       $rsdata['ac_area_id']   = intval($data['ac_area_id'])>0?intval($data['ac_area_id']):0;
       $rsdata['ac_p1_id']     = $data['ac_p1_id'];
       $rsdata['ac_p2_id']     = $data['ac_p2_id'];
       $rsdata['ac_shop_dish'] = $data['ac_shop_dish'];
       $rsdata['ac_dish_total'] = $data['ac_dish_total'];
       $rsdata['ac_dish_id']    = $data['ac_dish_id'];
       
       //判断价格是否大于原产品促销价格 ac_dish_total
       $dishInfo = $this->dishObj->getDishInfo($rsdata['ac_shop_dish'],'marketprice');
       $rsdata['ac_dish_price'] = FormatMoney($data['ac_dish_price']);
       if($rsdata['ac_dish_price'] > $dishInfo['marketprice'])
       {
            return -1;
       }
       //获取城市code,城市区域code
       $storeShopInfo                = $this->storeObj->getStoreShop('sts_city,sts_region');
       $rsdata['ac_city']            = $storeShopInfo['sts_city'];
       $rsdata['ac_city_area']       = $storeShopInfo['sts_region'];
       $rsdata['ac_in_id']           = $this->memberData['sts_category_p1_id'];
       $rsdata['ac_shop']            = $this->memberData['store_sts_id'];
       $rsdata['ac_dish_status']     = 0;
       if(isset($rsdata['ac_dish_id']) && $rsdata['ac_dish_id'] > 0)
       {
           mysqld_update('activity_dish',$rsdata,array('ac_dish_id'=>$rsdata['ac_dish_id']));
           return 1;
           
       }
       
       else{
            mysqld_insert('activity_dish',$rsdata);
            $acti_id = mysqld_insertid();    //获取上一次插入的ID 
            if($acti_id > 0)
            {
                //更新对应的宝贝表
                /* $upsql = "update squdian_shop_dish set ac_dish_id = {$acti_id} where id = {$rsdata['ac_shop_dish']}";
                $upStatus = mysqld_query($upsql); */
                return 1;
            }
            else{
                //插入失败
                 return -2;
            }
       }
   }
   
   //删除
    public function delActivityDish($ac_dish_id){
        //获取对应的dish_id
        $dish_sql = "select ac_shop_dish from squdian_activity_dish where ac_dish_id = {$ac_dish_id}";
        $dish_rs  = mysqld_select($dish_sql);
        
        $sql = "delete from squdian_activity_dish where ac_dish_id = {$ac_dish_id}";
        $rs  = mysqld_query($sql);
        
        //更新对应的宝贝表
        $upsql = "update squdian_shop_dish set ac_dish_id = 0 where id = ".intval($dish_rs['ac_shop_dish']);
        $upStatus = mysqld_query($upsql);
        
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
   
} 
?>