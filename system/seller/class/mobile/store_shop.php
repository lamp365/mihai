<?php

/**
 * Author: 王敬
 */

namespace seller\controller;

use seller\controller;

class store_shop  {


    //不给op  默认是index
    function index() {
        include page('shop/shop');
    }
    
    
    function dialogMap() {
        include page('shop/dialogMap');
    }
    
    //充值新会员等级---弹窗
    function dialogCharge() {
        $_GP = $this->request;
        //目前让用户随意购买会员等级，视情况做级别过滤（只返回高级别的数据供用户筛选）
        $result = mysqld_selectall("select * from ".table('store_shop_level')." order by rank_level ");
        include page('shop/dialogCharge');
    }
    
    //充值新会员等级
    function postNewLevel() {
        $_GP = $this->request;
//        $_GP['id'] = 1;$_GP['rank_level'] =3;
        intval($_GP['rank_level'])<=0 && message('请选择正确的等级！' ,refresh(),'error');  
        !$_GP['id'] &&  message('请检查表单ID！' ,refresh(),'error');   
        
        $Service = new \service\seller\RankService();
        $result = $Service->updateLevel( $_GP['id'],$_GP['rank_level'] );
        
        if ( $result!==false ) {
            message( LANG('COMMON_OPERATION_SUCCESS') ,refresh(),'success');
        }else{
            message( $Service->getError() ,refresh(),'error');
        }
    }

    public function getStep(){//防止突然关闭店铺，查询未填写完整的店铺，并跳转到下个注册页
        $_GP = $this->request;
		//获取用户所有店铺中有一家处于申请未完毕的
        $member = get_member_account();
        //查看该法人 是否有正在申请的店铺
        $onChaekStore = mysqld_select("select * from ".table('store_shop_apply')." where sts_openid='{$member['openid']}'");

        $this->request['id']= $onChaekStore['sts_id'];
        //有店铺
        switch ($onChaekStore['sts_info_status']) {
            case 1:
                $this->addShopTwo();
                break;
            case 12:
                $this->addShopThree();
                break;
            case 2://审核中
                message('您有店铺已经在审核中',  mobile_url('main') );
                break;
            case 3: //审核不通过
                message('店铺审核失败,'.$onChaekStore['fail_reason'],  mobile_url('main') );
                break;
            default://没有店铺则进行跳转添加页
                $this->addShopOne();
                break;
        }
    }
        
    function addShopOne() {//pc端输出,GET页面
        $_GP = $this->request;
         #1//*************查询区域数据************//
        $regionService = new \service\seller\regionService();
        $result =  getProvincesOfRegion();
        $region_category =  $regionService->getAllData();
        $childrens=array();
        foreach ($region_category as  $cate) {
            if (!empty($cate['parent_id'])) {
                $childrens[$cate['parent_id']][$cate['region_id']] = array(
                    $cate['region_id'], 
                    $cate['region_name'],
                    $cate['region_code'],
                );
            }
        }
        #2
        $Service = new \service\shop\IndustryService();
        $catStruct= $Service->getAllDataStruct();
//        ppd($catStruct);
        include page('shop/addShopOne');
    }
    function addShopTwo() {//pc端输出2
        $_GP = $this->request;
        #1//*************查询区域数据************//
        $regionService = new \service\seller\regionService();
        $result =  getProvincesOfRegion();
        $region_category =  $regionService->getAllData();
        $childrens=array();
        foreach ($region_category as  $cate) {
            if (!empty($cate['parent_id'])) {
                $childrens[$cate['parent_id']][$cate['region_id']] = array(
                    $cate['region_id'], 
                    $cate['region_name'],
                    $cate['region_code'],
                );
            }
        }
        include page('shop/addShopTwo');
    }
    
    function addShopThree() {//pc端输出,GET页面
        $_GP = $this->request;
//        ppd($catStruct);
        include page('shop/addShopThree');
    }

    //返回通用店铺分类
    public function appChooseIndustry() {
        $_GP = $this->request;
        !$_GP['region_code'] &&   ajaxReturnData(0, '请输入正确的区域CODE！');

        $Service = new \service\shop\IndustryService();
        $data = $Service->getAllDataStruct($_GP['region_code']);
//        ppd($data);
        if (checkIsAjax()) {
            ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'),array('shop_cat'=>$data) );
        }
    }
    
    
    //返回地区
    public function appChooseRange() {
        $data = recursiveRegionAssort(1); //输出带结构的数据
//        ppd($data);
        if (checkIsAjax()) {
            ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'), array('region'=>$data) );
        }
    }

    //POST申请店铺第一步
    public function shopRegisterStep1() {
        $_GP = $this->request;
        $Service = new \service\seller\StoreShopService();
//        $_GP['cat_id'] = '102';$_GP['region_code'] = '350101';//测试数据
        #1
        !$_GP['cat2_id'] &&  ajaxReturnData(0, LANG('请输入分类ID')); //第二级分类ID
        !$_GP['region_code'] && ajaxReturnData(0, LANG('请选择地区'));

        if ( !$Service->validateShopNum($_GP['region_code'], $_GP['cat2_id']) ) {
            ajaxReturnData(0, LANG('此地区分类下的店铺已满额')); //第二级分类ID
        }

        #2
        $data['id']            = $_GP['id'];
        $data['sts_region']    = $_GP['region_code'];
        $data['sts_shop_type'] = $_GP['sts_shop_type'];
        $data['sts_category_p2_id'] = $_GP['cat2_id'];
        $_GP['sts_city'] && $data['sts_city'] = $_GP['sts_city']; //非必填
        $_GP['sts_province'] && $data['sts_province'] = $_GP['sts_province']; //非必填
        $_GP['cat1_id'] && $data['sts_category_p1_id'] = $_GP['cat1_id']; //非必填

        //验证邀请码
        $data['invitation_code'] = $Service->processInvitationCode($_GP['invite_code']);
        #3
        $data = $Service->step1($data);

        #4
        if (checkIsAjax()) {
            ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'), array('id' => $data));
        }
    }

    //POST申请店铺第二步
    public function shopRegisterStep2() {
        $_GP = $this->request;
        $this->formValidate($_GP);//数据校验层
        $Service = new \service\seller\StoreShopService();

        /**通过第三级找到城市与省份的code**/
        $regionService = new \service\seller\regionService();
        $cityprovince  = $regionService->getParentsByRegionCode($_GP['cate_3']);
        #2数据组织和处理
        $data = array(
            'sts_province'       => trim($_GP['sts_province']),
            'sts_city'       => trim($_GP['sts_city']),
            'sts_region'       => trim($_GP['sts_region']),
            'sts_category_p1_id'       => trim($_GP['sts_category_p1_id']),
            'sts_category_p2_id'       => trim($_GP['sts_category_p2_id']),
            
            'sts_shop_type'       => intval($_GP['sts_shop_type'])?intval($_GP['sts_shop_type']):1,
            'sts_avatar'       => trim($_GP['sts_avatar']),
            'sts_name'         => trim($_GP['sts_name']),
            'sts_physical_shop_name' => trim($_GP['sts_physical_shop_name']),
            'sts_contact_name' => trim($_GP['sts_contact_name']),
            'sts_mobile'       => trim($_GP['sts_mobile']),
            'sts_weixin'       => trim($_GP['sts_weixin']), //非必填
            'sts_qq'           => trim($_GP['sts_qq']), //非必填
            'sts_summary'      => trim($_GP['sts_summary']),
            'sts_lat'          => trim($_GP['sts_lat']),
            'sts_lng'          => trim($_GP['sts_lng']),
            'sts_locate_add_1' => $cityprovince['province'], //商家所在区的地区code
            'sts_locate_add_2' => $cityprovince['city'], //商家所在区的地区code
            'sts_locate_add_3' => trim($_GP['cate_3']), //商家所在区的地区code
            'sts_address'      => trim($_GP['sts_address']),
        );
        if(empty($_GP['id'])){
            $data['sts_creatime'] = time();
        }
        $data = array_filter($data); //过滤空值，避免sql问题
        #3 用M层去操作业务逻辑
        //验证邀请码
        $data['invitation_code'] = $Service->processInvitationCode($_GP['invitation_code']);

        $data = $Service->step2($data, intval($_GP['id'])); //返回id

        #4 返回数据
        if (checkIsAjax()) {
            $return_data = array('id' => $_GP['id']?$_GP['id']:$data);
            ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'), $return_data) ;
        }
    }
    
    
     private function formValidate($data){
        $_GP = $data;
        $Service = new \service\seller\StoreShopService();
         if($_GP['id']){
             //如果 id存在，说明是app操作的第二个步骤，判断传过来的id是否是自己的店铺
             $res  =  $Service->checkIsMyApplyShop($_GP['id']);
             if(!$res){
                 ajaxReturnData(0,$Service->getError());
             }
         }
        !$_GP['sts_name']      &&    ajaxReturnData(0, LANG('店铺名必须')) ;
        !$_GP['sts_city']      &&    ajaxReturnData(0, LANG('请选择配送范围区域')) ;
        !$_GP['sts_category_p2_id'] &&    ajaxReturnData(0, '请选择主营业务！') ;
    }

    //POST申请店铺第3步
    public function shopRegisterStep3() {
        $_GP = $this->request;
//        ppd($_FILES,$_GP);
        $Service = new \service\seller\StoreShopService();
//        $_GP['id'] = 7;$_GP['sts_name'] = '102';$_GP['sts_contact_name'] = '350101';//测试数据
        #1数据校验
        !$_GP['id'] &&   ajaxReturnData(0, '请输入店铺ID！'); //第二级分类ID
        !$_GP['ssi_owner_name'] &&   ajaxReturnData(0, '法人姓名不为空！'); //第二级分类ID
        //如果 id存在 判断传过来的id是否是自己的店铺
        $res  =  $Service->checkIsMyApplyShop($_GP['id']);
        if(!$res){
            ajaxReturnData(0,$Service->getError());
        }

        $checkIdent = new \Validator();
        if(empty($_GP['ssi_owner_shenfenhao']) || !$checkIdent->identityNumberValidator($_GP['ssi_owner_shenfenhao']) ){
            ajaxReturnData(0, '身份证有误！');
        }

        $data = array();
        #2数据组织和处理
        if (!empty($_FILES['ssi_shenfenzheng']['tmp_name'])) {
            //身份证保存在本地
            $upload = file_upload($_FILES['ssi_shenfenzheng'],0,'identy');
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $data['ssi_shenfenzheng'] = $upload['path'];
        }
        if (!empty($_FILES['ssi_yingyezhizhao']['tmp_name'])) {
            $upload = file_upload($_FILES['ssi_yingyezhizhao']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $data['ssi_yingyezhizhao'] = $upload['path'];
        }
        if (!empty($_FILES['ssi_xukezheng']['tmp_name'])) {
            $upload = file_upload($_FILES['ssi_xukezheng']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $data['ssi_xukezheng'] = $upload['path'];
        }
        if (!empty($_FILES['ssi_dianmian']['tmp_name'])) {
            $upload = file_upload($_FILES['ssi_dianmian']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $data['ssi_dianmian'] = $upload['path'];
        }
        if (!empty($_FILES['ssi_diannei']['tmp_name'])) {
            $upload = file_upload($_FILES['ssi_diannei']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $data['ssi_diannei'] = $upload['path'];
        }

        $member = get_member_account();
        $data['ssi_id']               = $_GP['id'];
        $data['ssi_owner_name']       =  trim($_GP['ssi_owner_name']);
        //省份证加密
        $data['ssi_owner_shenfenhao'] =  cbd_encrypt(trim($_GP['ssi_owner_shenfenhao']),$member['openid']);

        #3 用M层去操作业务逻辑
        $data = $Service->step3($data); //返回更新行数
 
        #4 返回数据
        if (checkIsAjax()) {
            $return_data = array('id' => $_GP['id']);
            ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'),$return_data);
        }
    }
    
    public function shop_edit(){
        $member = get_member_account();
        $_GP = $this->request;

        //获取店铺信息直接用session取id，如果没有则代表用户还没新增店铺
        $fields = '*';
        $storeInfo = member_store_getById($member['store_sts_id'],$fields);
        
        if($storeInfo){
            $store_shop_identity_info =  mysqld_select("SELECT * FROM " . table('store_shop_identity') . " WHERE ssi_id = ".  $storeInfo['sts_id']);
            $status = array('2'=>'审核中','0'=>'已认证','1'=>'填写资料中','12'=>'填写资料中','3'=>'审核不通过');
            $storeInfo['sts_info_status_text'] = $status[$storeInfo['sts_info_status'] ];

            $serviceRank = new \service\seller\RankService();
            $rank_info =$serviceRank->getInfoByRankLevel($storeInfo['sts_shop_level']);
            $storeInfo = array_merge($rank_info,$storeInfo);
            $level_type_text = array('2'=>'市代理','1'=>'区代理','3'=>'省代理');
            $storeInfo['level_type_text'] = $level_type_text[$rank_info['level_type'] ];
//            ppd($storeInfo);
        }
        //用来匹配区域定位到select框
        //$storeInfo['sts_locate_add_1_id'] = $reCodeToID[$storeInfo['sts_locate_add_1']]  ;
        //$storeInfo['sts_locate_add_2_id'] = $reCodeToID[$storeInfo['sts_locate_add_2']]  ;
        //$storeInfo['sts_locate_add_3_id'] = $reCodeToID[$storeInfo['sts_locate_add_3']]  ;

        #1//*************查询区域数据************//
        $regionService  = new \service\seller\regionService();
        $resultProvince = getChildrenOfRegion();                              //省
        $resultCity     = getChildrenOfRegion($storeInfo['sts_locate_add_1']);    //市
        $resultCounty   = getChildrenOfRegion($storeInfo['sts_locate_add_2']);    //县
        
        include page('shop/shop_edit');
    }
    
    public function shop_edit_sub(){
        $_GP = $this->request;
        $storeShopService = new \service\seller\StoreShopService();
        
        $status = $storeShopService->setshop($_GP);
        
        echo $status;
    }
    
    public function childen_region(){
        $_GP = $this->request;
        $resultRegion     = getChildrenOfRegion($_GP['id'],'region_id,region_name,region_code');    //市
        
        echo json_encode($resultRegion);
        exit;
    }
    
}
