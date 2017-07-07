<?php
namespace seller\controller;
use service\seller\wuliuService;
use service\seller\StoreShopService;
class wuliu extends base
{

    public function __construct(){
        parent::__construct();
        $this->wuliuService = new wuliuService();
    }
    //物流列表
    public function index()
    {
        $dispatchId = $this->wuliuService->getStoreDispatchList();
        $result = $this->wuliuService->getAllDispatchList();
        include page('wuliu/dispatchList');
    }

    //新增物流设置
    public function addDispatch(){
        $_GP = $this->request;
        $dispatchId = $_GP['dispatchId'];
        if (!empty($dispatchId)){
            $dispatchId = implode(",", $dispatchId);
        }
        $return = $this->wuliuService->addStoreDispatch($dispatchId);
        if ($return) message('添加物流成功!',refresh(),'success');
    }
    //免邮价格的设置
    public function freePrice(){
        $member = get_member_account();
        $_GP    = $this->request;
        $type = $_GP['type'];
        if ($type == 'add'){
            $inset_data = array(
                'free_dispatch' => FormatMoney($_GP['free_dispatch'],1),
                'express_fee'   => FormatMoney($_GP['express_fee'],1),
            );

            $return = mysqld_update('store_extend_info',$inset_data,array('store_id'=>$member['store_sts_id']));
            if ($return) message('邮费设置成功!',refresh(),'success');
        }

        $service        = new \service\seller\StoreShopService();
        $myFreeDispatch = $service->viewreturnAddress();
        $free_dispatch  = FormatMoney($myFreeDispatch['free_dispatch'],0);
        $express_fee    = FormatMoney($myFreeDispatch['express_fee'],0);
        $limit_send     = FormatMoney($myFreeDispatch['limit_send'],0);
        include page('wuliu/freePrice');
    }
    //退货地址配置
    public function returnAddress(){
        $_GP = $this->request;
        $status = intval($_GP['status']);//区分物流配置或者免邮设置的tab
        $type = $_GP['type'];
        $service = new \service\seller\StoreShopService();
        if ($type == 'add'){
            $data["address_province"] = $_GP['save_address_province'];
            $data["address_city"] = $_GP['save_address_city'];
            $data["address_area"] = $_GP['save_address_area'];
            $data["address_address"] = $_GP['address_address'];
            $data["address_realname"] = $_GP['address_realname'];
            $data["address_mobile"] = $_GP['address_mobile'];
            $data["postcode"] = $_GP['postcode'];
            $data["code"] = $_GP['code'];
            $data["createtime"] = time();
            if (empty($data["address_province"]) || empty($data["address_city"]) || empty($data["address_area"]) || empty($data["address_address"]) || empty($data["address_realname"]) || empty($data["address_mobile"])) message('参数不完整!','','error');
            $flag = $service->addreturnAddress($data);
            if ($flag) message('设置成功','refresh','success');
        }
        //获得省份
        $province = getProvincesOfRegion();
        $info = $service->viewreturnAddress();
        if ($info){//说明是编辑，数据库存在数据
            $regionService = new \service\seller\regionService();
            $region = $regionService->getParentsByRegionCode($info['code']);
            //获得该省份的市区
            $city = $regionService->getAllRegionByCondition(array('parent_id'=>$region['province_id']),"region_id,region_name,parent_id");
            //获得该市区的地区
            $area = $regionService->getAllRegionByCondition(array('parent_id'=>$region['city_id']),"region_id,region_name,parent_id,region_code");
        }
        include page('wuliu/returnAddress');
    }
    //ajax获取子集菜单
    public function ajaxGetChildMenu(){
        $_GP = $this->request;
        $id = intval($_GP['id']);
        if (empty($id)) ajaxReturnData(0,'id为空');
        $regionService = new \service\seller\regionService();
        $list = $regionService->getAllRegionByCondition(array('parent_id'=>$id),"region_id,region_name,region_code");
        ajaxReturnData(1,'',$list);
    }
}