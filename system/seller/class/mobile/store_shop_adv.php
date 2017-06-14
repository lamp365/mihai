<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/4/19
 * Time: 14:35
 */

namespace seller\controller;

use seller\controller;

class store_shop_adv extends base {

    protected  $myTableName = 'store_shop_adv';
    private $loginUser = null;
    public function __construct() {
        $this->loginUser = get_member_account();
    }
    public function index(){
//        include page('shop/advlist');exit();
        $_GP = $this->request;
        $sts_id = $this->loginUser['store_sts_id'];
//        $sts_id = 28;
        $condition ='';
        if( isset( $_GP['ssa_is_require_top'] ) && intval($_GP['ssa_is_require_top'])>=0){
			$condition .= " and ssa_is_require_top=".$_GP['ssa_is_require_top'];
		}
        $_GP['ssa_title']  && $condition .= " and ssa_title like '%".trim($_GP['ssa_title'])."%'";
		$_GP['ssa_start_time_s']  && $condition .= " and ssa_start_time >=".  strtotime($_GP['ssa_start_time_s']);
        $_GP['ssa_start_time_e']  && $condition .= " and ssa_start_time <".  strtotime("+1 day",strtotime($_GP['ssa_start_time_e']))  ;
        
        $_GP['ssa_end_time_s']  && $condition .= " and ssa_end_time >=".  strtotime($_GP['ssa_end_time_s']);
        $_GP['ssa_end_time_e']  && $condition .= " and ssa_end_time <".  strtotime("+1 day",strtotime($_GP['ssa_end_time_e']))  ;

		$pindex = max(1, intval($_GP['page']));
		$psize = 10;
		$limit = " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		$fields = '*';
		$result  = mysqld_selectall("SELECT {$fields} FROM " . table('store_shop_adv') . " WHERE ssa_shop_id = {$sts_id}  {$condition} ORDER BY ssa_is_require_top DESC,ssa_adv_id DESC ".$limit);
		if ($result){
		    foreach ($result as $key=>$v){
		        $ssa_thumb = explode(",", $v['ssa_thumb']);
		        $result[$key]['ssa_thumb'] = $ssa_thumb[0];
		    }
		}
		$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('store_shop_adv') . " WHERE ssa_shop_id = {$sts_id}  {$condition}");
		$pager = pagination($total, $pindex, $psize);
        
        if(checkIsAjax()){
            empty($result)?ajaxReturnData(0,'无数据'):ajaxReturnData(1,'',$result);
        }else{
            include page('shop/advlist');
        }
   }
   
    private function addedit(){//GET页面
        $_GP = $this->request;
        if($_GP['id']){
            $where= '  where ssa_adv_id = '.$_GP['id'];
            $info = mysqld_select("SELECT * FROM " . table($this->myTableName).$where);
            $isEdit = 1;
            $picArr  = explode(',',$info['ssa_thumb']);
            foreach($picArr as $k=>$v)
            {
                if (!empty($v)){
                    $xqPicArr[$k]['filename'] = $v;
                }
            }
            $xqImgJson = json_encode($xqPicArr);
//            ppd($info);
        }
//        ppd($result);
        include page('shop/store_shop_adv_addedit');
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
        mysqld_delete($this->myTableName, array('ssa_adv_id' => $_GP['id'],'ssa_shop_id'=>$this->loginUser['store_sts_id']));
        message(LANG('COMMON_DELETE_SUCCESS'), refresh(), 'success');
    }
   
    
    private function formValidate($data){
        $data['ssa_type'] = $data['ssa_type']==2?2:1;
        if($data['ssa_type']==2 && !$data['ssa_weixin_url']){
            checkIsAjax()?ajaxReturnData(0,  LANG('微信地址URL不能为空')): message( LANG('微信地址URL不能为空'),refresh(),'error');
        }
        if($data['ssa_type']==1 && !$data['ssa_content']){
            checkIsAjax()?ajaxReturnData(0,  LANG('活动内容不能为空')): message( LANG('活动内容不能为空'),refresh(),'error');
        }
        if( $data['id'] ){//检查这个文章是不是本店铺的
            $find      = mysqld_select("select * from ". table('store_shop_adv') ." where ssa_adv_id={$data['id']} and ssa_shop_id =".$this->loginUser['store_sts_id'] );
            if(!$find){
                checkIsAjax()?ajaxReturnData(0,  LANG('非法ID请检查')): message( LANG('非法ID请检查'),refresh(),'error');
            }
        }
        
//        
//        //ppd($data);
//        if(trim($data['gc_name'])){
//            $where =  " where gc_name ='".trim($data['gc_name'])."'";
//            $find = mysqld_selectall("SELECT * FROM " . table($this->myTableName) . " {$where}");
//            if($find){
//                checkIsAjax()?ajaxReturnData(0,  LANG('此分类名已存在')): message( LANG('此分类名已存在'),refresh(),'error');
//            }
//        }
    }
    
    private function core_post($_GP){
//        ppd($_GP);
        $this->formValidate($_GP);
        $service = new \service\seller\StoreShopAdvService();
        $result = $service->save($_GP, $_GP['id']);
        if(checkIsAjax()){
            $result!==false?ajaxReturnData(1,  LANG('COMMON_OPERATION_SUCCESS')):
                ajaxReturnData(0,  LANG('COMMON_OPERATION_FAILED'));
        }else{
            message( LANG('COMMON_OPERATION_SUCCESS'),  web_url('industry'),'success');
        }
	
    }
    
    
   

}
