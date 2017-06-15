<?php
namespace seller\controller;
use  seller\controller;

class category extends base
{
    private $myTableName= 'store_shop_category';

    public function addcate()
    {
        $_GP    = $this->request;
        include page('shop/addcate');
    }

    public function post(){
        $_GP = $this->request;
        $cateService = new \service\seller\ShopCategoryService();
        $res = $cateService->formValidateBeforeAddCate($_GP);
        if(!$res){
            if(checkIsAjax()){
                ajaxReturnData(0,  $cateService->getError());
            }else{
                message($cateService->getError(),refresh(),'success');
            }
        }

        $cate_id = $cateService->do_addCate($_GP);
        if(!$cate_id){
            if(checkIsAjax()){
                ajaxReturnData(0,  $cateService->getError());
            }else{
                message($cateService->getError(),refresh(),'error');
            }
        }else{
            if(checkIsAjax()){
                ajaxReturnData(1,  LANG('COMMON_OPERATION_SUCCESS'));
            }else{
                message( LANG('COMMON_OPERATION_SUCCESS'),refresh(),'success');
            }
        }
	}


    //编辑分类
    public function editcate()
    {
        $_GP    = $this->request;
        $info   = mysqld_select("SELECT * FROM " . table($this->myTableName) . " where id={$_GP['id']} ");
        include page('shop/addcate');
    }



    public function delete()
    {
        // 1、有子类，先提示删除子类
        // 2、没有子类 或者当前是子类 判断所删除的 该类 是否有产品 关联过  有 更新status 为 0  否则 直接删除
        //封装提取 到service
        $_GP = $this->request;
        $cateService = new \service\seller\ShopCategoryService();
        $res = $cateService->delCate($_GP['id']);
        if($res){
            message('删除成功！',refresh(),'success');
        }else{
            message($cateService->getError(),refresh(),'error');
        }
        
    }
    
       
	public function index()
	{
      
        #1数据接收拼接查询语句
		$_GP = $this->request;
        $member = get_member_account(false);
        $where = '';
        $where.="  where store_shop_id =". $member['store_sts_id'] ." and status =1" ;
        
        $sql = "SELECT *,pid as parentid FROM " . table($this->myTableName) ." {$where}";
        
        #2查询数据
        $allcate   = mysqld_selectall( $sql);
        $cate_list = array();
        shopCategoryTree($cate_list,$allcate);
//        ppd($cate_list);
        include page('shop/category');
	}
}