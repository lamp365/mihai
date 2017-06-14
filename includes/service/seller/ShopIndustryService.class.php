<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class ShopIndustryService extends \service\publicService {
    private $memberData;
    private $table;
    
    function __construct() {
       parent::__construct();
       $this->memberData = get_member_account();
       $this->table      = table('industry');
   }
   
   
   public function oneIndustryCategory($fields='*'){
       $sql = "select {$fields} from " . $this->table . " where gc_pid = 0";
       $data = mysqld_selectall($sql);
       return $data;
   }
   
   public function twoIndustryCategory($pid,$fields='*'){
       $sql = "select {$fields} from " . $this->table . " where gc_pid = {$pid}";
       $data = mysqld_selectall($sql);
       return $data;
   }
   
   
   
   
}   