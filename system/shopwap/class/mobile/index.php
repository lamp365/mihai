<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/12
 * Time: 17:49
 */
namespace shopwap\controller;

class index 
{
	public $request = '';
	//如果没有定义op 默认显示index
	public function index()
	{
        //进入首页后 就更新一下缓存，有这么一种情况，用户登录过了，申请了店铺，当前还没审核
        //用户类型还是普通会员 但审核通过了，但是他的缓存没有更新过来，所以在进入首页的时候，会调用该方法
		$member = update_member_info();
		$_GP = $this->request;
        $isLogin =  is_login_account()?1:0;
		include page ( 'index' );
	}
    
     //高德地图---弹窗
    function dialogMap() {
        $_GP = $this->request;
        include page('dialogMap');
    }
    
	/**
	 * 卖家申请入驻
	 */
	public function apply()
	{
        $_GP = $this->request;
		$isLogin =  is_login_account();
		if(!$isLogin){
			message('请您先登录！',WEBSITE_ROOT,'error');
		}
        #2 核心查询部分
        if($_GP['sts_id']){
            $memberinfo = get_member_account();
            $info   =  mysqld_select("SELECT * FROM " . table('store_shop_apply') . " where sts_id={$_GP['sts_id']} and sts_openid = {$memberinfo['openid']}  ");
            !$info && message('请核对店铺id是否正确！',WEBSITE_ROOT,'error');
            
            //编辑时为了匹配选中select。此处可以新增region表parent_code+分类数据树结构+缓存（死数据）来优化
            $region_service = new \service\seller\regionService();
            if( $info['sts_region'] ){
                $tmp_region_data = $region_service->getParentsByRegionCode($info['sts_region']);
                $info['sts_province_id'] =$tmp_region_data['province_id'] ;
                $info['sts_city_id'] =$tmp_region_data['city_id'] ;
                $info['sts_qu_id'] =$tmp_region_data['qu_id'] ;
            }
            if( $info['sts_locate_add_3'] ){
                $tmp_region_data = $region_service->getParentsByRegionCode($info['sts_locate_add_3']);
                $info['sts_locate_add_1_id'] =$tmp_region_data['province_id'] ;
                $info['sts_locate_add_2_id'] =$tmp_region_data['city_id'] ;
                $info['sts_locate_add_3_id'] =$tmp_region_data['qu_id'] ;
            }
        }else{
            //如果不是修改重写，，判断是否可以再次申请
            $apply = new \service\seller\applyService();
            $res   = $apply->checkIsCanApply();
            if(!$res){
                message($apply->getError(),refresh(),'error');
            }
        }
       
        
        #3 查询页面加载的其他数据
        //*************查询区域************//
        $result = getProvincesOfRegion();
        $childrens = array();
        $regionService = new \service\seller\regionService();
        $region_category =  $regionService->getAllData();
        foreach ($region_category as  $cate) {
            if (!empty($cate['parent_id'])) {
                $childrens[$cate['parent_id']][$cate['region_id']] = array(
                    $cate['region_id'], 
                    $cate['region_name'],
                    $cate['region_code'],
                );
            }
        }
        //*************查询行业************//
        $Service = new \service\shop\IndustryService();
        $catStruct= $Service->getAllDataStruct();
        #4
//        ppd($catStruct);
		include page ( 'apply/step1' );
	}

    
    public function apply2()
	{
        $_GP = $this->request;
        
         #2 核心查询部分
        if($_GP['id']){
            $memberinfo = get_member_account();
            $info       =  mysqld_select("SELECT * FROM " . table('store_shop_identity_apply') . " where ssi_id={$_GP['id']}   ");
            //身份证号解密
            $info['ssi_owner_shenfenhao'] = cbd_decrypt($info['ssi_owner_shenfenhao'],$memberinfo['openid']);
        }
		include page ( 'apply/step2' );
	}
}
