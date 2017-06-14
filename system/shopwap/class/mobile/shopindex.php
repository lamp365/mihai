<?php 
namespace shopwap\controller;
use service\shopwap\shopindexService;
class shopindex extends base{
    //首页
    public function index(){
        $shopindexService = new shopindexService();
        $cfg = globaSetting();
        $title = $cfg['shop_title'];

        //计算店铺总数，取出店名及图标,取24个并且进行8个一组封装
        $return = $shopindexService->getStoreInfoArr();
        $total = $return['total'];
        $storeIcon = $return['storeIcon'];
        
        //取出店铺图标名称描述及推荐商品
        $storeInfoAndGoods = $shopindexService->getStoreInfoAndgoods();
        include themePage('shopindex');
    }
    
    //ajax获取店铺及推荐商品列表
    public function ajaxGetStoreGoods(){
        if (checkIsAjax()){
            $_GP = $this->request;
            $shopindexService = new shopindexService();
            $storeInfoAndGoods = $shopindexService->getStoreInfoAndgoods($_GP);
            ajaxReturnData(1,'',$storeInfoAndGoods);
        }
    }
}
?>
