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
       $this->table_commission_settlement_paylog = table('commission_settlement_paylog');
       $this->table_member_blong_relation        = table('member_blong_relation');
       
   }
   
   //获取dish列表
   public function getMemberinfo($openid='',$fields='*'){
       $sql = "SELECT {$fields} FROM {$this->table_member} where openid = '{$openid}'";
       $data  = mysqld_select($sql);
       return $data;
   }
   
   //获取用户总收益
   public function getTotalIncome($openid){
       $sql = "SELECT sum(account_fee) as account_fee FROM {$this->table_member_paylog} where openid = '{$openid}' and (type = 3 or type = -3)";
       $data  = mysqld_select($sql);
       return $data['account_fee'];
   }
  
   //
   public function getProfitList($openid,$data,$fields='*'){
       $data['page'] = max(1, intval($data['page']));
       $data['limit'] = $data['limit']>0?$data['limit']:10; 
       $limit = " LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit'];
       
       $sql = "SELECT {$fields} FROM {$this->table_member_paylog} where openid = '{$openid}' and (type = 3 or type = -3) order by createtime desc {$limit}";
       $rs  = mysqld_selectall($sql);
       return $rs;
   }
   
   public function getMemberInfos($openids,$fields){
       $sql = "select {$fields} from {$this->table_member} where openid in ({$openids})";
       $rs = mysqld_selectall($sql);
       return $rs;
   }
   
   //获取结算列表
   public function getCspList($openid,$data,$fields='*',$type=1){
      $data['page'] = max(1, intval($data['page']));
      $data['limit'] = $data['limit']>0?$data['limit']:10; 
      $limit = " LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit'];
      $sql = "select {$fields} from {$this->table_commission_settlement_paylog} where type = {$type} and payee_openid = '{$openid}' order by createtime desc {$limit}";

      $rs  = mysqld_selectall($sql);
      return $rs;
   }
   
   //判断下属的子账户
   public function getParentIdAccount($data,$openid,$fields='*'){
      $data['page'] = max(1, intval($data['page']));
      $data['limit'] = $data['limit']>0?$data['limit']:10; 
      $limit = " LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit']; 
       
      $sql = "select {$fields} from {$this->table_member_store_relation} where parent_openid = '{$openid}' order by id desc {$limit}";
      $rs  = mysqld_selectall($sql);
      return $rs;
   }
   
   public function getParentIdAccountCount($openid,$fields='*'){
      $sql = "select {$fields} from {$this->table_member_store_relation} where parent_openid = '{$openid}'";
      $rs  = mysqld_select($sql);
      return $rs;
   }
   
   //统计用户数
   public function getChildrenCount($openid,$sts_id,$fields='*'){
       //$this->table_member_blong_relation
       $sql = "select {$fields} from {$this->table_member_blong_relation} where p_sid = {$sts_id} and m_openid = {$openid}";
       $rs  = mysqld_select($sql);
       return $rs;
       
   }
   
   //变更子账户状态状态
   public function upMemberSubStatus($openid,$is_sub_status){
       //is_sub_status
       $sql = "update {$this->table_member} set is_sub_status = {$is_sub_status} where openid = '{$openid}'";
       $rs  = mysqld_query($sql);
       return $rs;
   }
   
   public function getMemberStoreRelation($openid,$stsid,$fields='id'){
       $sql = "select {$fields} from {$this->table_member_store_relation} where openid = '{$openid}' and sts_id = {$stsid}";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
} 
?>