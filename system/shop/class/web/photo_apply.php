<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/12
 * Time: 17:49
 */
namespace shop\controller;

class photo_apply extends \common\controller\basecontroller
{
    private $photo_apply;
    private $store_shop;
    private $region;
    function __construct() {
        $this->photo_apply = new \service\seller\photoApplyService();
        $this->store_shop = new \service\seller\StoreShopService();
        $this->region   =  new \service\seller\regionService();
        
        error_reporting(E_ERROR);
   }
    
    /**
     * 物料管理列表
     */
    public function index(){
        $_GP                  = $this->request;
        $data                 = array();
        $data['audit_status'] = $_GP['audit_status']>0?$_GP['audit_status']:1;
        
        //
        $data['page'] = max(1, intval($data['page']));
        $data['limit'] = $data['limit']>0?$data['limit']:10; 
        
     
        $materialManagement = $this->photo_apply->getMaterialManagementList($data);

        $sts_id_str = '';
        foreach($materialManagement['data'] as $v){
            $sts_id_str .= $v['sts_id'].',';
        }
        $sts_id_str = rtrim($sts_id_str,',');
        
        if($sts_id_str != '')
        {
            //获取对应的店铺信息
            $shopStoresRs = $this->store_shop->getShopStores($sts_id_str);

            $shopStoresData = array();
            foreach($shopStoresRs as $v){
                $shopStoresData[$v['sts_id']]['sts_name'] = $v['sts_name'];
                $shopStoresData[$v['sts_id']]['sts_physical_shop_name'] = $v['sts_physical_shop_name'];
                $shopStoresData[$v['sts_id']]['sts_contact_name'] = $v['sts_contact_name'];
            }

            foreach($materialManagement['data'] as $k=>$v){
                $materialManagement['data'][$k]['sts_name']               = $shopStoresData[$v['sts_id']]['sts_name'];
                $materialManagement['data'][$k]['sts_physical_shop_name'] = $shopStoresData[$v['sts_id']]['sts_physical_shop_name'];
                $materialManagement['data'][$k]['sts_contact_name']       = $shopStoresData[$v['sts_id']]['sts_contact_name'];
            }

            $pager = pagination($materialManagement['total'], $data['page'] , $data['limit']);
        }
        include page('photo_apply/index');
    }
    
    //变更物料申请状态
    public function change_mm_status(){
        
    }
    
    /**
     * 物料设置
     */
    public function photo_config()
    {
        $_GP = $this->request;
        
        if($_GP['id'] > 0)
        {
            $id = intval($_GP['id']);
            $photoApplyInfo = $this->photo_apply->getMaterialType($id);

            $piclist = explode(',', $photoApplyInfo['ms_type_url']);
            
            $cityData   = $this->region->getCityData($photoApplyInfo['ms_province_id']);
            $countyData = $this->region->getCountyData($photoApplyInfo['ms_city_id']);
        }
        
        $provinceData = $this->region->getProvinceData();
        
        include page('photo_apply/photo_config');
    }
    
    public function photo_city(){
       $_GP = $this->request;
       $data = explode('|', $_GP['parentid']);
       if($data['0'] > 0)
       $cityData = $this->region->getCityData($data['0']);
       $select_str = '<select class="form-control" name="ms_city" id="ms_city"><option value="">请选择市级</option>';
       if($cityData!=''){
        foreach($cityData as $v){
            $select_str .= "<option value='{$v['region_id']}|{$v['region_name']}|{$v['region_code']}'>{$v['region_name']}</option>";
        }
       }
       $select_str .= '</select>';
       echo $select_str;
       exit;
    }
    
    public function photo_county(){
       $_GP = $this->request;
       $data = explode('|', $_GP['parentid']);
       $countyData = $this->region->getCountyData($data['0']);
       $select_str = '<select class="form-control" name="ms_county" id="ms_county"><option value="">请选择区级</option>';
       if($countyData != ''){
            foreach($countyData as $v){
                $select_str .= "<option value='{$v['region_id']}|{$v['region_name']}|{$v['region_code']}'>{$v['region_name']}</option>";
            }
       }
       $select_str .= '</select>';
       echo $select_str;
       exit;
    }
    
    public function photo_config_sub(){
        $_GP = $this->request;
        $data = array();
       
        $ms_province = explode('|', $_GP['ms_province']);
        $ms_city     = explode('|', $_GP['ms_city']);
        $ms_county   = explode('|', $_GP['ms_county']);
        
        //判断是否已经存在对应的区域
        $areaData = array();
        $areaData['ms_province'] = $ms_province[0];
        $areaData['ms_city'] = $ms_city[0];
        $areaData['ms_county'] = $ms_county[0];
        $materialManagementType = $this->photo_apply->getMaterialManagementTypeByArea($areaData);
        
        if($_GP['ms_is_default'] == 1)
        {
            //移除默认状态
            $del = $this->photo_apply->delMaterialManagementDefault($_GP['ms_category']); 
        }
        
        if($_GP['id'] > 0)
        {
            $id = $_GP['id'];
            $msg = '更新';
            //
            $data = array(
                'ms_category'=>$_GP['ms_category'],
                'ms_type'=>$_GP['ms_category']==1?'小型':'中型',
                'ms_size'=>$_GP['ms_size'],
                'ms_type_url'=>implode(',', $_GP['attachment-new']),
                'edit_time'=>time(),
                'ms_status'=>$_GP['ms_status'],
                'ms_is_default'=>$_GP['ms_is_default'],
                'ms_province_id'=>$ms_province[0],
                'ms_city_id'=>$ms_city[0],
                'ms_county_id'=>$ms_county[0],
                'ms_province_name'=>$ms_province[1],
                'ms_city_name'=>$ms_city[1],
                'ms_county_name'=>$ms_county[1],
                'ms_province_code'=>$ms_province[2],
                'ms_city_code'=>$ms_city[2],
                'ms_county_code'=>$ms_county[2],
            );
        }
        else{
            //判断该种类是否已经存在 存在则不需要再次添加
            
            
            $id = 0;
            $msg = '添加';
            
            $data = array(
                'ms_category'=>$_GP['ms_category'],
                'ms_type'=>$_GP['ms_category']==1?'小型':'中型',
                'ms_size'=>$_GP['ms_size'],
                'ms_type_url'=>implode(',', $_GP['attachment-new']),
                'create_time'=>time(),
                'ms_status'=>$_GP['ms_status'],
                'ms_is_default'=>$_GP['ms_is_default'],
                'ms_province_id'=>$ms_province[0],
                'ms_city_id'=>$ms_city[0],
                'ms_county_id'=>$ms_county[0],
                'ms_province_name'=>$ms_province[1],
                'ms_city_name'=>$ms_city[1],
                'ms_county_name'=>$ms_county[1],
                'ms_province_code'=>$ms_province[2],
                'ms_city_code'=>$ms_city[2],
                'ms_county_code'=>$ms_county[2],
            );
            
        }
        $insertId = $this->photo_apply->addMaterialType($data,$id);
        $url = web_url('photo_apply', array('op' => 'list_photo_apply'));
        //attachment-new
        if($insertId > 0)
        {
             message($msg.'成功',$url,'success');
        }
        else{
             message($msg.'失败',$url,'error');
        }
       
    }
    
    public function apply(){
        $_GP = $this->request;
        include page('photo_apply/apply');
    }
    
    public function list_photo_apply(){
        $_GP = $this->request;
        
        //省 市 县
        $provinceData = $this->region->getProvinceData();
        
        $data = array();
        $data['is_page']    = 1;
        if($_GP['ms_is_default'] > 0)
        {
            $data['ms_is_default']    = $_GP['ms_is_default'];
        }
        $data['page'] = max(1, intval($_GP['page']));
        $data['limit'] = $_GP['limit']>0?$_GP['limit']:10; 
        
        
        //判断是否存在搜索
        if($_GP['is_search'] > 0)
        {
            if($_GP['ms_province'] != '')
            {
                $ms_province = array();
                $ms_province = explode('|', $_GP['ms_province']);
                $data['ms_province'] = $ms_province[0];
                
                $ms_city = array();
                $ms_city = explode('|', $_GP['ms_city']);
                $data['ms_city'] = $ms_city[0];
                
                $cityData = $this->region->getCityData($ms_province[0]);
            }
            
            if($_GP['ms_city'] != ''){
                $ms_county = array();
                $ms_county = explode('|', $_GP['ms_county']);
                $data['ms_county'] = $ms_county[0];
                
                $countyData = $this->region->getCountyData($ms_city[0]);
            }
        }
        
        $list = $this->photo_apply->listMaterialType($data);
        
        $pager = pagination($list['total']['total'], $data['page'], $data['limit']);
        unset($list['total']);
        
        
        include page('photo_apply/list_photo_apply');
    }
    
	
    public function spec(){
        $_GP = $this->request;
        $sts_id = intval($_GP['sts_id']);
        $materialManagementInfo = $this->photo_apply->getMaterialManagement($sts_id);

        $materialManagementInfo['detialArray'] = json_decode($materialManagementInfo['detial'],true);

        //获取店铺所在省市县
        $storeShopData = $this->store_shop->getShopStoresByStsid($sts_id, 'sts_province,sts_city,sts_region');
        
        //获取默认样板
        $defaultData = $this->photo_apply->getMaterialDefaultType();

        $data = array();
        $data['sts_province'] = $storeShopData['sts_province'];
        $data['sts_city'] = $storeShopData['sts_city'];
        $data['sts_region'] = $storeShopData['sts_region'];
        $data['is_page']    = 0;
        $data['is_area']    = 1;
        $data['ms_is_default']    = 0;

        $list = $this->photo_apply->listMaterialType($data);
        foreach($defaultData as $v){
            array_push($list,$v);
        }
        
        include page('photo_apply/spec');
    }
    
    public function spec_show(){
        $_GP = $this->request;
        $sts_id = intval($_GP['sts_id']);
        $materialManagementInfo = $this->photo_apply->getMaterialManagement($sts_id);
        $materialManagementInfo['detialArray'] = json_decode($materialManagementInfo['detial'],true);

        $list = $this->photo_apply->listMaterialType();
        
        include page('photo_apply/spec_show');
    }
	
    public function spec_sub(){
        $_GP = $this->request;
        foreach($_GP['ms'] as $k=>$v){
            if($v['ms_type_radio'] != 'on')
            {
                unset($_GP['ms'][$k]);
            }
        }
  
        $id = intval($_GP['id']);
        if($id > 0)
        {
            $data                 = array();
            $data['detial']       = json_encode($_GP['ms']);
            $data['audit_detial'] = $_GP['audit_detial'];
            $insertId = $this->photo_apply->addMaterialManagement($data,$id);
            
            $msg = '更新';
        }
        else{
            $data                 = array();
            $data['sts_id']       = intval($_GP['sts_id']);
            $data['create_time']  = time();
            $data['audit_status'] = 1;
            $data['detial']       = json_encode($_GP['ms']);
            $data['audit_detial'] = $_GP['audit_detial'];
            $insertId = $this->photo_apply->addMaterialManagement($data);
            $msg = '添加';
        }
        
        
        if($insertId > 0)
        {
             message($msg.'成功',$url,'success');
        }
        else{
             message($msg.'失败',$url,'error');
        }
        
    }
    
    public function materialTypeStatus(){
        $_GP = $this->request;
        $reData = array();
        $id = $_GP['id'];
        $data = array();
        $data['ms_status'] = $_GP['ms_status'];
        //ms_is_default
        $upStatus = $this->photo_apply->upMaterialManagementListStatus($data,$id); 
        $upStatus = 1;
        $reData['data'] = $upStatus;
        echo json_encode($reData);
        exit;
    }
    
    //默认设置
    public function materialTypeDefault(){
        $_GP = $this->request;
        $reData = array();
        $id = $_GP['id'];
        $ms_type = $_GP['type'];
        $data = array();
        $data['ms_is_default'] = $_GP['ms_is_default'];
        if($data['ms_is_default'] == 1)
        {
            //移除默认状态
            $del = $this->photo_apply->delMaterialManagementDefault($ms_type); 
        }
        //ms_is_default
        $upStatus = $this->photo_apply->upMaterialManagementDefault($data,$id); 
        $upStatus = 1;
        $reData['data'] = $upStatus;
        echo json_encode($reData);
        exit;
    }
    
    public function AuditFailure(){
        $_GP = $this->request;
        
        //获取审核的状态信息
        $audit = $this->photo_apply->getMaterialManagementById($_GP['id'],'id,audit_fail_detial');

        include page('photo_apply/photo_apply_fail');
    }
    
    
    public function AuditSuccessSub(){
        $_GP = $this->request;
        $data = array();
        $data['audit_status']      = 2;
        //
        $auditStatus = $this->photo_apply->upStoreMaterialManagement($data,$_GP['id']);
        $url = web_url('photo_apply',array('op'=>'index','audit_status'=>2));
        message('审核通过',$url,'success');
    }
    
    public function AuditFailureSub(){
        $_GP = $this->request;
        $data = array();
        $data['audit_fail_detial'] = $_GP['audit_detial'];
        $data['audit_status']      = 3;
        //
        $auditStatus = $this->photo_apply->upStoreMaterialManagement($data,$_GP['id']);
        
        ajaxReturnData(1, '更新成功');
    }
    
}
?>