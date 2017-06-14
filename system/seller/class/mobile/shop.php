<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/4/19
 * Time: 14:35
 */
namespace seller\controller;
use seller\controller;

class shop extends base
{
    //不给op  默认是index
    function index()
    {
       
        $member = get_member_account();
        $_GP = $this->request;
        
        #1//*************查询区域数据************//
        $regionService = new \service\seller\regionService();
        $result =  getProvincesOfRegion();
        $region_category =  $regionService->getAllData();
        $childrens=array();
        foreach ($region_category as  $cate) {
            if (!empty($cate['parent_id'])) {
                $reCodeToID[$cate['region_code']] =  $cate['region_id'];
                $childrens[$cate['parent_id']][$cate['region_id']] = array(
                    $cate['region_id'], 
                    $cate['region_name'],
                    $cate['region_code'],
                );
            }
        }
        //ppd( $childrens[$reCodeToID[ $storeInfo['sts_locate_add_3'] ] ] );
        $fields = '*';
        //获取店铺信息直接用session取id，如果没有则代表用户还没新增店铺
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
        $storeInfo['sts_locate_add_1_id'] = $reCodeToID[$storeInfo['sts_locate_add_1']]  ;
        $storeInfo['sts_locate_add_2_id'] = $reCodeToID[$storeInfo['sts_locate_add_2']]  ;
        $storeInfo['sts_locate_add_3_id'] = $reCodeToID[$storeInfo['sts_locate_add_3']]  ;
        //ppd($childrens[$reCodeToID[ $storeInfo['sts_locate_add_2']]]);
        include page('shop/index');
    }
	function child()
    {
        $_GP = $this->request;
        include page('shop/child_shop');
    }


    public function change_store()
    {
        $_GP    = $this->request;
        $member = get_member_account();
        //提交操作 切换店铺
        if(!empty($_GP['sts_id'])){
            $service = new \service\seller\StoreShopService();
            $res = $service->changeshop($_GP['sts_id']);
            if($res){
                message('操作成功！',refresh(),'success');
            }else{
                message($service->getError(),refresh(),'error');
            }
        }

        $mem_store = member_allstore_get($member,'sts_id,sts_name,sts_info_status',0);
        foreach($mem_store as $key => $one){
            if($one['sts_info_status'] != '0'){
                //审核未通过的删掉
                unset($mem_store[$key]);
            }
        }
        include page('shop/change_store');
    }


    function changeStoreInfo(){
        $_GP = $this->request;
        $_GP['cate_1']<=0 &&   ajaxReturnData(0, '请选择一级城市') ; //第二级分类ID
        $_GP['cate_2']<=0 &&   ajaxReturnData(0, '请选择二级城市') ; //第二级分类ID
        $_GP['cate_3']<=0 &&   ajaxReturnData(0, '请选择三级城市') ; //第二级分类ID
        $_GP['id']<=0     &&   ajaxReturnData(0, '没有店铺ID?') ; //第二级分类ID

        $data = array(
            'sts_locate_add_1'=>  intval($_GP['cate_1']),
            'sts_locate_add_2'=>  intval($_GP['cate_2']),
            'sts_locate_add_3'=>  intval($_GP['cate_3']),
        );
        $effect= mysqld_update('store_shop', $data, array('sts_id' => $_GP['id']));
        $effect!==false &&  mysqld_update('store_shop_identity', array('ssi_shenhe_region'=>0), array('ssi_id' => $_GP['id']));

        if( $effect !==false ){
            ajaxReturnData(1,  LANG('COMMON_OPERATION_SUCCESS'));
        }else{
            ajaxReturnData(0,  LANG('COMMON_OPERATION_FAIL'));
        }

    }

    function changeIdentityInfo(){
        $_GP = $this->request;
        //ppd($_FILES);
//        $_GP['id'] = 7;$_GP['sts_name'] = '102';$_GP['sts_contact_name'] = '350101';//测试数据
        #1数据校验
        !$_GP['ssi_id'] &&   ajaxReturnData(0, '请输入店铺ID！'); //第二级分类ID

        #2图片处理
        if (!empty($_FILES['ssi_yingyezhizhao']['tmp_name'])) {
            $upload = file_upload($_FILES['ssi_yingyezhizhao']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $ssi_yingyezhizhao = $upload['path'];
        }
        if (!empty($_FILES['ssi_xukezheng']['tmp_name'])) {
            $upload = file_upload($_FILES['ssi_xukezheng']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $ssi_xukezheng = $upload['path'];
        }
        if (!empty($_FILES['ssi_dianmian']['tmp_name'])) {
            $upload = file_upload($_FILES['ssi_dianmian']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $ssi_dianmian = $upload['path'];
        }

        #3 组织更新数据
        $data = array(
            'ssi_id'               => $_GP['ssi_id'],
            'ssi_yingyezhizhao'    => trim($ssi_yingyezhizhao),
            'ssi_xukezheng'        => trim($ssi_xukezheng),
            'ssi_dianmian'         => trim($ssi_dianmian),
        );
        $data = array_filter($data);

        $ssi_yingyezhizhao && $data['ssi_shenhe_yingye'] = 0;
        $ssi_xukezheng && $data['ssi_shenhe_xukezheng'] = 0;
        $ssi_dianmian && $data['ssi_shenhe_dianmian'] = 0;

        #4 用M层去操作业务逻辑
        $Service = new \service\seller\StoreShopService();
        $effect = $Service->step3($data);
        #5
        if( $effect !==false ){
            ajaxReturnData(1,  LANG('COMMON_OPERATION_SUCCESS'));
        }else{
            ajaxReturnData(0,  LANG('COMMON_OPERATION_FAIL'));
        }

    }


    /**
     * 账户安全
     */
    public function safe()
    {
        $_GP = $this->request;
        //获取店铺法人的信息  以及店铺信息
        $member = get_member_account();
        $sts_id = $member['store_sts_id'];
        $store  = mysqld_select("select sts_tran_passwd,sts_openid from ".table('store_shop')." where sts_id={$sts_id}");
        $apply_man = member_get($store['sts_openid'],'mobile');
        include page('shop/safe');
    }

    /**
     * 设置支付密码
     */
    public function setpwd()
    {
        $_GP    = $this->request;

        if(!empty($_GP['do_pwd'])){
            $service = new \service\seller\StoreShopService();
            $res     = $service->set_store_pwd($_GP);
            if($res){
                message('操作成功！',refresh(),'success');
            }else{
                message($service->getError(),refresh(),'success');
            }
        }

        $member = get_member_account();
        //获取店铺法人的信息
        $member = get_member_account();
        $sts_id = $member['store_sts_id'];
        $store  = member_store_getById($sts_id,'sts_tran_passwd');
        include page('shop/setpwd');
    }

    /**
     * 发送短信验证码
     */
    public function phonecode()
    {
        $_GP     = $this->request;
        $service = new \service\seller\StoreShopService();
        $res     = $service->send_mobile_code($_GP);
        if($res){
            ajaxReturnData(1,LANG('COMMON_SMS_SEND_SUCCESS'));
        }else{
            ajaxReturnData(1,$service->getError());
        }
    }
    /**
     * 体现账户列表
     */
    public function zhanghu()
    {
        $_GP = $this->request;
        //获取店铺法人的 银行卡账户信息
        $service    = new \service\seller\StoreShopService();
        $bank_list  = $service->get_bank_list();
        if(!is_array($bank_list)){
            //说明返回的是 false  app可以提示 不是管理员  pc则不显示数据不提示
            $bank_list  = array('all'=>array());
        }
        include page('shop/zhanghu');
    }

    public function add_zhanghu()
    {
        $_GP = $this->request;
        if(!empty($_GP['action'])){
            //添加账户
            $service = new \service\seller\StoreShopService();
            $res     = $service->add_zhanghu($_GP);
            if($res){
                message('操作成功！',refresh(),'success');
            }else{
                message($service->getError(),refresh(),'error');
            }
        }
        $edit_bank = array();
        $member    = get_member_account();
        if(empty($member['store_is_admin'])){
            ajaxReturnData(0,'您不是最高管理员');
        }
        include page('shop/add_zhanghu');
    }

    public function edit_zhanghu()
    {
        $_GP = $this->request;
        if(empty($_GP['id'])){
            ajaxReturnData(0,'参数有误！');
        }
        if(!empty($_GP['action'])){
            //修改账户
            $service = new \service\seller\StoreShopService();
            $res     = $service->add_zhanghu($_GP);
            if($res){
                message('操作成功！',refresh(),'success');
            }else{
                message($service->getError(),refresh(),'error');
            }
        }
        $memInfo   = get_member_account();
        if(empty($memInfo['store_is_admin'])){
            ajaxReturnData(0,'您不是最高管理员');
        }
        $edit_bank = mysqld_select("select * from ".table('member_bank')." where id={$_GP['id']} and openid='{$memInfo['openid']}'");
        if(empty($edit_bank)){
            ajaxReturnData(0,'该账户不存在');
        }
        include page('shop/add_zhanghu');
    }

    public function del_zhanghu()
    {
        $_GP = $this->request;
        if(empty($_GP['id'])){
            message('参数有误！',refresh(),'error');
        }
        $member    = get_member_account();
        if(empty($member['store_is_admin'])){
            message('您不是最高管理员！',refresh(),'error');
        }
        $res = mysqld_delete('member_bank',array('id'=>$_GP['id'],'openid'=>$member['openid']));
        if($res){
            message('删除成功！',refresh(),'success');
        }else{
            message('删除失败！',refresh(),'success');
        }
    }
}