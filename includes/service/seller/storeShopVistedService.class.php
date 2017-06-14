<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class storeShopVistedService extends \service\publicService {
    private $memberData;
    private $table;
    
    function __construct() {
       parent::__construct();
       $this->memberData   = get_member_account();
       $this->table        = table('store_shop_visted');
       
   }
   
   //获取某个时间段的UV PV值
   function bettenTime($start_time,$end_time,$fields='sum(pv_count) as pv_num,sum(uv_count) as uv_num'){
       $sql = "select {$fields} from {$this->table} where zero_time between {$start_time} and {$end_time}";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
}