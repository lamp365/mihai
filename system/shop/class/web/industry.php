<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/12
 * Time: 17:49
 */
namespace shop\controller;

class industry
{
    protected  $myTableName = 'industry';
    
    public function index(){
        $_GP = $this->request;
        $Where = ' where gc_deleted = 0 ';
        if($_GP['pid']){
            $Where .= " and  gc_pid =". intval($_GP['pid']) ." ";
        }else{
            $Where .= " and  gc_pid =0 ";
        }
        
        $result = mysqld_selectall("SELECT gc_id as id,gc_name as name,gc_order,gc_limit FROM " . table($this->myTableName) . "  $Where ORDER BY gc_pid,gc_order,gc_id ASC");
//        ppd($result);
        if(checkIsAjax()){
            empty($result)?ajaxReturnData(0,'无数据'):ajaxReturnData(1,$result);
        }else{
            include page('region_category/industry_index');
        }
   }
   
    private function addedit(){//GET页面
        $_GP = $this->request;
//        $Service = new \service\shop\IndustryService();
//        $result =   $Service->getAllDataStruct();
        
        if($_GP['id']){
            $where= '  where gc_id = '.$_GP['id'];
            $info = mysqld_select("SELECT gc_id as id, gc_pid,gc_name as name,gc_order,gc_limit FROM " . table($this->myTableName).$where);
//            ppd($info);
        }
//        ppd($result);
        include page('region_category/industry_addedit');
    }
   
    public function add(){
        if(checksubmit('submit')){
            $_GP = $this->request;
            $this->core_post($_GP);
        }else{
            $this->addedit();
        }
    }
     
    public function edit(){//POST接口
        if(checksubmit('submit')){
            $_GP = $this->request;
            $this->core_post($_GP);
        }else{
            $this->addedit();
        }
    }
 
     
    public function delete(){//POST接口
        $_GP = $this->request;
        !$_GP['id']&& message('没有对应ID', refresh(), 'error');
        mysqld_delete($this->myTableName, array('gc_id' => $_GP['id']));
        mysqld_delete($this->myTableName, array('gc_pid' => $_GP['id']));
        message(LANG('COMMON_DELETE_SUCCESS'), refresh(), 'success');
    }
   
    
    private function formValidate($data){
        if(!trim($data['gc_name'])){
            checkIsAjax()?ajaxReturnData(0,  LANG('名称必填，不能为空')): message( LANG('名称必填，不能为空'),refresh(),'error');
        }
        
        //ppd($data);
        if(trim($data['gc_name'])){
            $where =  " where gc_name ='".trim($data['gc_name'])."'";
            $find = mysqld_selectall("SELECT * FROM " . table($this->myTableName) . " {$where}");
            if($find){
                checkIsAjax()?ajaxReturnData(0,  LANG('此分类名已存在')): message( LANG('此分类名已存在'),refresh(),'error');
            }
        }
    }
    
    private function core_post($_GP){
        
        $this->formValidate($_GP);
        $data=array(
            'gc_name'=> trim($_GP['gc_name']), 
            'gc_order'=> intval($_GP['gc_order']), 
            'gc_limit'=> intval($_GP['gc_limit']), 
            'gc_pid'=>  intval($_GP['pid']),
        );
        
        if( $_GP['id'] ){
            mysqld_update($this->myTableName,$data, array('gc_id' => intval($_GP['id'])));
        }else{
            mysqld_insert($this->myTableName, $data);
        }
        if(checkIsAjax()){
            ajaxReturnData(1,  LANG('COMMON_OPERATION_SUCCESS'));
        }else{
            message( LANG('COMMON_OPERATION_SUCCESS'),  web_url('industry'),'success');
        }
	
    }
    
    
    public function UpdateLimitSingle(){//ajax POST接口
        $_GP = $this->request;
        if(empty($_GP['id'])){
            ajaxReturnData(0, '缺少参数');
        }else{
            $update_data = array();
            $_GP['gc_limit']> 0 &&  $update_data['gc_limit'] = intval($_GP['gc_limit']);
            is_numeric($_GP['gc_order']) &&  $update_data['gc_order'] = intval($_GP['gc_order']);
            if(!$update_data){
                ajaxReturnData(0, '缺少参数');
            }
            $id_key   = $_GP['id'];
            mysqld_update($this->myTableName,$update_data, array('gc_id' => $id_key));
            ajaxReturnData(1, LANG('COMMON_UPDATE_SUCCESS'));
        }
    }
    
         
    public function getCategroysByP2ID(){//POST接口
        $_GP = $this->request;
        if(empty($_GP['p2_id'])){
            ajaxReturnData(0, '缺少参数');
        }else{
            $where = ' where deleted=0 and parentid=0  and enabled=1 and industry_p2_id = '.intval( $_GP['p2_id'] );
            $list = mysqld_selectall("SELECT * FROM " . table('shop_category') . " {$where} ");
            ajaxReturnData(1, LANG('COMMON_UPDATE_SUCCESS'),array('list'=>$list));
        }
          
    }
}
