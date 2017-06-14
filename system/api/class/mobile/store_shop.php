<?php

/**
 * Author: 王敬
 */

namespace api\controller;
use api\controller;

class store_shop  extends homebase{

    //不给op  默认是index
    function index() {

    }

    //返回通用店铺分类
    public function appChooseIndustry() {
        $_GP = $this->request;
//        $_GP['region_code'] =350101;
        !$_GP['region_code'] &&   ajaxReturnData(0, '请输入正确的区域CODE！');

        $Service = new \service\shop\IndustryService();
        $data = $Service->getAllDataStruct($_GP['region_code'],1);

        ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'),array('shop_cat'=>$data) );
    }
    
    
    //返回地区
    public function appChooseRange() {
        $data = recursiveRegionAssort(1); //输出带结构的数据
//        ppd($data);
        ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'), array('region'=>$data) );
    }

    //POST申请店铺第一步
    public function shopRegisterStep1() {
        $_GP = $this->request;
        $Service = new \service\seller\StoreShopService();
        $regionService = new \service\seller\regionService();
//        $_GP['cat_id'] = '102';$_GP['region_code'] = '350101';//测试数据
        #1
        !$_GP['cat2_id'] &&  ajaxReturnData(0, LANG('请选择主营业务')); //第二级分类ID
        !$_GP['region_code'] && ajaxReturnData(0, LANG('请选择地区'));

        if ( !$Service->validateShopNum($_GP['region_code'], $_GP['cat2_id']) ) {
            ajaxReturnData(0, LANG('此地区分类下的店铺已满额')); //第二级分类ID
        }

        #2
        $data['id']         = $_GP['id'];
        $data['sts_region'] = $_GP['region_code'];
        $data['sts_category_p2_id'] = $_GP['cat2_id'];
        $cityprovince         = $regionService->getParentsByRegionCode( $_GP['region_code']);
        $data['sts_province'] =$cityprovince['province'];
        $data['sts_city']     =$cityprovince['city'];
        $data['sts_shop_type'] = $_GP['sts_shop_type']?$_GP['sts_shop_type']:1;
        $_GP['cat1_id'] && $data['sts_category_p1_id'] = $_GP['cat1_id']; //非必填 

        //验证邀请码
        $data['invitation_code'] = $Service->processInvitationCode($_GP['invitation_code']);

        #3
        $store_id = $Service->step1($data);

        #4
        ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'), array('id' => $store_id));
    }

    //POST申请店铺第二步
    public function shopRegisterStep2() {
        $_GP = $this->request;
        //数据校验层
        $this->formValidate($_GP);

        #2数据组织和处理
        $data = array(
            'sts_shop_type'    =>$_GP['sts_shop_type']?$_GP['sts_shop_type']:1,
            'sts_name'         => trim($_GP['sts_name']),
            'sts_physical_shop_name' => trim($_GP['sts_physical_shop_name']),
            'sts_contact_name' => trim($_GP['sts_contact_name']),
            'sts_mobile'       => trim($_GP['sts_mobile']),
            'sts_weixin'       => trim($_GP['sts_weixin']), //非必填
            'sts_qq'           => trim($_GP['sts_qq']), //非必填
            'sts_summary'      => trim($_GP['sts_summary']),
            'sts_lat'          => trim($_GP['sts_lat']),
            'sts_lng'          => trim($_GP['sts_lng']),
            'sts_locate_add_3' => trim($_GP['cate_3']), //商家所在区的地区code
            'sts_address'      => trim($_GP['sts_address']),
        );
        //通过区code
        $regionService            = new \service\seller\regionService();
        $cityprovince             = $regionService->getParentsByRegionCode( $_GP['cate_3']);
        $data['sts_locate_add_1'] = $cityprovince['province'];
        $data['sts_locate_add_2'] = $cityprovince['city'];

        if (!empty($_FILES['sts_avatar']['tmp_name'])) {
            $upload = file_upload($_FILES['sts_avatar']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $data['sts_avatar'] = $upload['path'];
        }
        $data = array_filter($data); //过滤空值，避免sql问题
        #3 用M层去操作业务逻辑
        $Service = new \service\seller\StoreShopService();
        $data = $Service->step2($data, intval($_GP['id'])); //返回更新行数
        #4 返回数据
        $return_data = array('id'=>$data);//app数据格式要求
        ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'), $return_data) ;
        
    }
    
    
     private function formValidate($data){
        $_GP = $this->request;
        $Service = new \service\seller\StoreShopService();
        if($_GP['id']){
            //如果 id存在，说明是app操作的第二个步骤，判断传过来的id是否是自己的店铺
            $res  =  $Service->checkIsMyApplyShop($_GP['id']);
            if(!$res){
                ajaxReturnData(0,$Service->getError());
            }
        }

        !$_GP['sts_name'] &&   ajaxReturnData(0, '请输入店铺名') ; //店铺名必传
         $_GP['sts_shop_type']==1 && !$_GP['id'] &&   ajaxReturnData(0, '请输入店铺ID') ; //普通商鋪必传
    }

    //POST申请店铺第3步
    public function shopRegisterStep3() {
        $_GP     = $this->request;
        $Service = new \service\seller\StoreShopService();

        #1数据校验
        !$_GP['id']             &&   ajaxReturnData(0, '请输入店铺ID！'); //第二级分类ID
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
        $return_data = array('id' =>$data);
        ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'),$return_data);
        
    }

}
