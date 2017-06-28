<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class subAccountService extends \service\publicService {
    private $memberData;
    private $table;
    
    function __construct() {
       parent::__construct();
       $this->memberData   = get_member_account();
       $this->table_member_store_relation        = table('member_store_relation');
       $this->table_member  = table('member');
       $this->table_member_paylog = table('member_paylog');
       
   }
   
   //获取dish列表
   public function getMemberinfo($openid='',$fields='*'){
       $sql = "SELECT {$fields} FROM {$this->table_member} where openid = {$openid}";
       $data  = mysqld_select($sql);
       return $data;
   }
   
   //获取用户总收益
   public function getTotalIncome($openid){
       $sql = "SELECT sum(account_fee) as account_fee FROM {$this->table_member_paylog} where openid = {$openid} and (type = 3 or type = -3)";
       $data  = mysqld_select($sql);
       return $data['account_fee'];
   }
  
   //
   public function getProfitList($openid,$fields='*'){
       $data['page'] = max(1, intval($data['page']));
       $data['limit'] = $data['limit']>0?$data['limit']:10; 
       $limit = " LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit'];
       
       $sql = "SELECT {$fields} FROM {$this->table_member_paylog} where openid = {$openid} and (type = 3 or type = -3) order by createtime desc {$limit}";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
} 
?>