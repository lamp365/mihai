<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/4/19
 * Time: 14:35
 */
namespace api\controller;
use api\controller;
use service\seller\wuliuService;
use service\seller\StoreShopService;
class shop extends base
{

    /**
     * 体现账户列表
     */
    public function account()
    {
        $_GP = $this->request;
        //获取店铺法人的 银行卡账户信息
        $service    = new \service\seller\StoreShopService();
        $bank_list  = $service->get_bank_list();
        if($bank_list == false){
            ajaxReturnData(0,$service->getError());
        }
        ajaxReturnData(1,'获取成功！',$bank_list);
    }

    /**
     * 添加账户表单提交
     */
    public function add_account()
    {
        $_GP = $this->request;
        //添加账户
        $service = new \service\seller\StoreShopService();
        $res     = $service->add_zhanghu($_GP,1);
        if($res){
            ajaxReturnData(1,'操作成功！',$res);
        }else{
            ajaxReturnData(0,$service->getError());
        }
    }

    /**
     * 银行卡的操作添加持卡人
     */
    public function accountOwn()
    {
        $_GP = $this->request;
        //操作持卡人 的更新
        $service = new \service\seller\StoreShopService();
        $res     = $service->add_zhanghu($_GP,2);
        if($res){
            ajaxReturnData(1,'操作成功！');
        }else{
            ajaxReturnData(0,$service->getError());
        }
    }

    /**
     * 编辑账户  获取要修改的账户 以及 提交表单修改操作
     */
    public function edit_account()
    {
        $_GP = $this->request;
        if(empty($_GP['id'])){
            ajaxReturnData(0,'参数有误！');
        }
        if(!empty($_GP['action'])){
            if($_GP['action'] != 'edit'){
                ajaxReturnData(0,'action参数有误！');
            }
            //修改账户
            $service = new \service\seller\StoreShopService();
            $res     = $service->add_zhanghu($_GP);
            if($res){
                ajaxReturnData(1,'操作成功！');
            }else{
                ajaxReturnData(0,$service->getError());
            }
        }
        $memInfo   = get_member_account();
        if(empty($memInfo['store_is_admin'])){
            ajaxReturnData(0,'您不是最高管理员！');
        }
        $edit_bank = mysqld_select("select * from ".table('member_bank')." where id={$_GP['id']} and openid='{$memInfo['openid']}'");
        if(empty($edit_bank)){
            ajaxReturnData(0,'该账户不存在');
        }
        $data['edit_bank'] = $edit_bank;
        ajaxReturnData(1,'获取成功',$data);
    }

    /**
     * 删除提现账户
     */
    public function del_account()
    {
        $_GP = $this->request;
        if(empty($_GP['id'])){
            ajaxReturnData(0,'参数有误!');
        }
        $member    = get_member_account();
        if(empty($member['store_is_admin'])){
            ajaxReturnData(0,'您不是最高管理员');
        }
        $res = mysqld_delete('member_bank',array('id'=>$_GP['id'],'openid'=>$member['openid']));
        if($res){
            ajaxReturnData(1,'删除成功!');
        }else{
            ajaxReturnData(0,'删除失败!');
        }
    }

    /**
     * 设置提款账户为默认
     */
    public function defaultAccount()
    {
        $_GP = $this->request;
        if(empty($_GP['id'])){
            ajaxReturnData(0,'未传入银行卡id!');
        }
        $member    = get_member_account();
        if(empty($member['store_is_admin'])){
            ajaxReturnData(0,'您不是最高管理员');
        }
        //把当前的卡设置为 默认
        set_bank_default($member['openid'],$_GP['id']);
        ajaxReturnData(1,'已设置成功');
    }

    /**
     * 店铺信息设置修改
     */
    public function setshop()
    {
        $_GP = $this->request;
        $service = new \service\seller\StoreShopService();
        $res = $service->setshop($_GP);
        if($res){
            $member_info  = get_member_account();
            $loginService = new \service\shopwap\loginService();
            $store_data   = $loginService->getStoreData($member_info);
            $data = array(
                'store'          => $store_data['store_info'],
                'store_identity' => $store_data['store_identity'],
                'member'         => $member_info
            );
            ajaxReturnData(1,'操作成功！',$data);
        }else{
            ajaxReturnData(0,$service->getError());
        }
    }
    //免邮以及运费价格的设置
    public function freePrice(){
        $member = get_member_account();
        $_GP    = $this->request;
        $type = $_GP['type'];
        if ($type == 'update'){
            $inset_data = array(
                'free_dispatch' => FormatMoney($_GP['free_dispatch'],1),
                'express_fee'   => FormatMoney($_GP['express_fee'],1),
            );

            $return = mysqld_update('store_extend_info',$inset_data,array('store_id'=>$member['store_sts_id']));
            if ($return)
                ajaxReturnData(1,'运费设置成功');
            else
                ajaxReturnData(0,'运费设置失败');
        }

        $service        = new \service\seller\StoreShopService();
        $myFreeDispatch = $service->viewreturnAddress();
        $res_data       = array();
        $res_data['free_dispatch']  = FormatMoney($myFreeDispatch['free_dispatch'],0);  //满多少免邮
        $res_data['express_fee']    = FormatMoney($myFreeDispatch['express_fee'],0);    //运费
        ajaxReturnData(1,'请求成功！',$res_data);
    }

    /**
     * 退货地址配置
     */
    public function addreturnAddress(){
        $_GP = $this->request;
        $data["address_province"] = $_GP['address_province'];
        $data["address_city"] = $_GP['address_city'];
        $data["address_area"] = $_GP['address_area'];
        $data["address_address"] = $_GP['address_address'];
        $data["address_realname"] = $_GP['address_realname'];
        $data["address_mobile"] = $_GP['address_mobile'];
        $data["postcode"] = $_GP['postcode'];
        $data["code"] = $_GP['code'];
        $data["createtime"] = time();
        if (empty($data["address_province"]) || empty($data["address_city"]) || empty($data["address_area"]) || empty($data["address_address"]) || empty($data["address_realname"]) || empty($data["address_mobile"]) || empty($data["code"])) ajaxReturnData(0,'参数不完整');
        $regionService = new \service\seller\regionService();
        $info = $regionService->getAllRegionByCondition(array('region_code'=>$data['code']),"region_id");
        if (empty($info)) ajaxReturnData(0,'查无此code');
        $service = new \service\seller\StoreShopService();
        $flag = $service->addreturnAddress($data);
        if ($flag) ajaxReturnData(1,'设置成功！');
    }

    /**
     * 查看退货地址
     *   */
    public function viewreturnAddress(){
        $service = new \service\seller\StoreShopService();
        $info = $service->viewreturnAddress();
        ajaxReturnData(1,'',$info);
    }
    /**
     * 切换店铺
     */
    public function change_store()
    {
        $_GP    = $this->request;
        $member = get_member_account();
        //提交操作 切换店铺
        if(!empty($_GP['sts_id'])){
            $service = new \service\seller\StoreShopService();
            $res = $service->changeshop($_GP['sts_id']);
            //获取用户信息 和店铺新的信息 给app
            if($res){
                $member_info = get_member_account();

                $loginService = new \service\shopwap\loginService();
                $store_data   = $loginService->getStoreData($member_info);

                $data = array('store'=>$store_data['store_info'],'store_identity'=>$store_data['store_identity'],'member'=>$member_info);
                ajaxReturnData(1,'操作成功！',$data);
            }else{
                ajaxReturnData(0,$service->getError());
            }
        }

        $mem_store = member_allstore_get($member,'sts_id,sts_name,sts_info_status',0);
        foreach($mem_store as $key => $one){
            if($one['sts_info_status'] != '0'){
                //审核未通过的删掉
                unset($mem_store[$key]);
            }
        }

        //获取每一个店铺的 基本销售金额
        $service = new \service\seller\StoreShopService();
        $mem_store_info = $service->getEachStoreSaleOrder($mem_store);
        ajaxReturnData(1,'请求成功！',array('shop'=>$mem_store_info));
    }

    /**
     * 通用详情
     */
    public function commondetail()
    {
        $_GP    = $this->request;
        $member = get_member_account();
        if(empty($_GP['position'])){
            ajaxReturnData(0,"position参数不能为空！");
        }
        $commondetail = mysqld_selectall("select * from ".table('shop_dish_commontop')." where sts_id={$member['store_sts_id']} and position={$_GP['position']}");
        ajaxReturnData(1,"请求成功！",array('commondetail'=>$commondetail));
    }
    /**
     * 通用详情 获取默认的
     */
    public function commondetail_get()
    {
        $_GP    = $this->request;
        $member = get_member_account();
        $commondetail = mysqld_selectall("select * from ".table('shop_dish_commontop')." where sts_id={$member['store_sts_id']} and is_default=1");
        $data['top']    = array();
        $data['bottom'] = array();
        foreach($commondetail as $item){
            if($item['position'] == 1){
                $data['top'] = $item;
            }else if($item['position'] == 2){
                $data['bottom'] = $item;
            }
        }
        ajaxReturnData(1,"请求成功！",$data);
    }
    /**
     * 添加 通用详情
     */
    public function commondetail_add()
    {
        $_GP    = $this->request;
        $service = new \service\seller\StoreShopService();
        $res = $service->commondetail_add($_GP);
        if($res){
            ajaxReturnData(1,'操作成功！');
        }else{
            ajaxReturnData(0,$service->getError());
        }
    }
    /**
     * 添加 通用详情
     */
    public function commondetail_edit()
    {
        $_GP    = $this->request;
        $service = new \service\seller\StoreShopService();
        if(empty($_GP['id'])){
            ajaxReturnData(0,'id不能为空');
        }
        $res = $service->commondetail_add($_GP);
        if($res){
            ajaxReturnData(1,'操作成功！');
        }else{
            ajaxReturnData(0,$service->getError());
        }
    }

    /**
     * 设置通用详情为默认
     */
    public function commondetail_default()
    {
        $_GP    = $this->request;
        $service = new \service\seller\StoreShopService();
        $res = $service->commondetail_default($_GP['id'],$_GP['position']);
        if($res){
            ajaxReturnData(1,'操作成功！');
        }else{
            ajaxReturnData(0,$service->getError());
        }
    }
    /**
     * 删除通用详情
     */
    public function commondetail_del()
    {
        $_GP    = $this->request;
        $service = new \service\seller\StoreShopService();
        $res = $service->commondetail_del($_GP['id']);
        if($res){
            ajaxReturnData(1,'操作成功！');
        }else{
            ajaxReturnData(0,$service->getError());
        }
    }

}