<?php
namespace service\seller;

class photoApplyService extends \service\publicService
{
   private $table;
   function __construct() {
       parent::__construct();
       $this->table      = table('store_material_type');
       $this->table_store_material_management      = table('store_material_management');
   }
   
   //物料类型操作
   public function addMaterialType($data,$id=0){
       if($id > 0)
       {
           $where = array();
           $where = array('ms_type_id' => $id);
           $insertStatus = mysqld_update('store_material_type', $data,$where);
           $insertId = 1;
       }
       else{
            $insertStatus = mysqld_insert('store_material_type', $data);
            $insertId = mysqld_insertid();
       }
       
       return $insertId;
   }
   
   //物料类型列表
   public function listMaterialType($data,$field='*'){
       //ms_province_code ms_city_code ms_county_code
       $where = 'where 1';
       if($data['is_area'] > 0)
       {
           $where .= " and ms_province_code = {$data['sts_province']} and ms_city_code = {$data['sts_city']} and ms_county_code = {$data['sts_region']}";
       }
       
       if($data['ms_province'] > 0)
       {
           $where .= " and ms_province_id = {$data['ms_province']}";
       }
       if($data['ms_city'] > 0)
       {
           $where .= " and ms_city_id = {$data['ms_city']}";
       }
       if($data['ms_county'] > 0)
       {
           $where .= " and ms_county_id = {$data['ms_county']}";
       }
       
       if($data['is_page'] > 0)
       {
            $limit = "order by ms_type_id desc LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit'];
       }
       
       if($data['ms_is_default'] > 0)
       {
           $data['ms_is_default'] = $data['ms_is_default']==1?0:1;
           $where .= " and ms_is_default = {$data['ms_is_default']}";
       }
       
       $sql = "select {$field} from {$this->table} {$where} {$limit}";
       $rs  = mysqld_selectall($sql);
       
       if($data['is_page'] > 0)
       {
           $sql = "select count(0) as total from {$this->table} {$where}";
           $rs['total']  = mysqld_select($sql);
       }
       
       return $rs;
   }
   
   //获取默认类型
   public function getMaterialDefaultType($field='*'){
       $sql = "select {$field} from {$this->table} where ms_is_default = 1";
       $rs  = mysqld_selectall($sql);
       return $rs;
   }
   
   //获取物料类型
   public function getMaterialType($id,$fields='*'){
       $sql = "select {$fields} from {$this->table} where ms_type_id = {$id} limit 1";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   public function addMaterialManagement($data,$id=0){
       if($id > 0)
       {
           $where = array();
           $where = array('id' => $id);
           $insertStatus = mysqld_update('store_material_management', $data,$where);
           $insertId = 1;
       }
       else{
            $insertStatus = mysqld_insert('store_material_management', $data);
            $insertId = mysqld_insertid();
       }
       return $insertId;
   }
   
   //通过$_GP['sts_id'] 获取信息
   public function getMaterialManagement($sts_id,$fields='*'){
       //squdian_store_material_management
       $sql = "select {$fields} from {$this->table_store_material_management} where sts_id = {$sts_id}";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   
   //通过$_GP['sts_id'] 获取信息
   public function getMaterialManagementById($id,$fields='*'){
       //squdian_store_material_management
       $sql = "select {$fields} from {$this->table_store_material_management} where id = {$id}";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
   //获取申请列表
   public function getMaterialManagementList($data,$fields='*'){
       $redata = array();
       
       $data['page'] = max(1, intval($data['page']));
       $data['limit'] = $data['limit']>0?$data['limit']:10; 
       $limit = " LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit'];
       
       $sql = "select {$fields} from {$this->table_store_material_management} where audit_status = {$data['audit_status']} order by create_time desc {$limit}";
       $redata['data']  = mysqld_selectall($sql);
       
       $sql_total = "select count(0) as total from {$this->table_store_material_management} where audit_status = {$data['audit_status']}";
       $rs_total  = mysqld_select($sql_total);
       $redata['total'] = $rs_total['total'];
       
       return $redata;
   }
   
    public function upMaterialManagementListStatus($data,$id){
        $where = array();
        $where = array('ms_type_id' => $id);
        $updateStatus = mysqld_update('store_material_type', $data,$where);
        return $updateStatus;
    }
   
    //ms_is_default
    public function upMaterialManagementDefault($data,$id){
        $where = array();
        $where = array('ms_type_id' => $id);
        $updateStatus = mysqld_update('store_material_type', $data,$where);
        return $updateStatus;
    }
    
    public function delMaterialManagementDefault($ms_type){
        $upDefault = "update ".table('store_material_type')." set ms_is_default = 0 where ms_category = {$ms_type}";
        $rsDefault = mysqld_query($upDefault);
        return $rsDefault;
        
    }
    
    public function upStoreMaterialManagement($data,$id){
        $where = array();
        $where = array('id' => $id);
        $updateStatus = mysqld_update('store_material_management', $data,$where);
        return $updateStatus;
    }
    
    //$this->table_store_material_management
    public function getMaterialManagementTypeByArea($data,$fields){
        $sql = "select {$fields} from ".table('store_material_type')." where ms_is_default = 0 and ms_province_id = {$data['ms_province_id']} and ms_city_id = {$data['ms_city_id']} and ms_county_id = {$data['ms_county_id']}";
        $rs = mysqld_select($sql);
        return $rs;
    }
    
}
?>