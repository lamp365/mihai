<?php
/**
优惠券的service层
 */
namespace service\shopwap;
use service\publicService;
class shopindexService extends publicService
{
    /**
     * 取出店铺图标名称描述及推荐商品
     *
     *  **/
    public function getStoreInfoAndgoods($data = array()){
        $pindex = max(1, intval($data['page']));
        $psize = isset($data['limit']) ? $data['limit'] : 4;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $storeShopService = new \service\shopwap\storeShopService();
        $storeIcon = $storeShopService->getAllStoreShop('','sts_id,sts_name,sts_avatar,sts_summary',"sts_score DESC LIMIT {$limit} , {$psize}");
        if ($storeIcon){
            foreach ($storeIcon as $k=>$v){
                $where = array(
                    'sts_id' => $v['sts_id'],
                    'status' => 1,
                    'isrecommand' => 1,
                );
                $shopDishService = new \service\shopwap\shopDishService();
                $info = $shopDishService->getAllShopDish($where,'id,title,thumb,marketprice,productprice',"id DESC LIMIT 0 , 2");
                foreach ($info as $key=>$val){
                    $info[$key]['marketprice'] = FormatMoney($val['marketprice'],0);
                    $info[$key]['productprice'] = FormatMoney($val['productprice'],0);
                }
                $storeIcon[$k]['goodsinfo'] = $info;
            }
        }
        return $storeIcon;
    }
    /**
     * 获取店铺总数，取24个店铺信息，进行八个一组封装
     *   */
    public function getStoreInfoArr(){
        //计算店铺总数，取出店名及图标,取24个并且进行8个一组封装
        $storeShopService = new \service\shopwap\storeShopService();
        $storeList = $storeShopService ->getAllStoreShop('','sts_id,sts_name,sts_avatar','sts_score desc');
        $total = count($storeList);
        $storeInfo = array_chunk($storeList,24);
        $storeIcon = array_chunk($storeInfo[0],8);
        return array('total'=>$total,'storeIcon'=>$storeIcon);
    }
}