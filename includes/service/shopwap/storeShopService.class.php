<?php
/**
店铺的service层
 */
namespace service\shopwap;
use model\store_shop_model;
use model\shop_dish_model;
use model\member_belong_relation_model;
use model\store_shop_adv_model;
use service\publicService;
class storeShopService extends publicService
{
    /**
     * 取出店铺的特卖商品
     *   */
    public function getStoreTMshop($storeid){
        if (empty($storeid)) return '';
        $shopDishModel = new shop_dish_model();
        $where = array(
            'sts_id' => $storeid,
            'status' => 1,
            'isdiscount' => 1
        );
        $shopDiscount = $shopDishModel->getAll($where,"id,title,thumb,description,marketprice,productprice","id DESC LIMIT 0 , 4");
        foreach ($shopDiscount as $key=>$v){
            $shopDiscount[$key]['marketprice'] = FormatMoney($v['marketprice'],0);
            $shopDiscount[$key]['productprice'] = FormatMoney($v['productprice'],0);
        }
        return $shopDiscount;
    }
    /**
     * 根据店铺id取出店铺推荐商品列表
     *   */
    public function getRecShopByStoreid($data = array()){
        $storeid = $data['storeid'];
        if (empty($storeid)) return false;
        $shopDishModel = new shop_dish_model();
        $pindex = max(1, intval($data['page']));
        $psize = isset($data['limit']) ? $data['limit'] : 10;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $where = array(
            'sts_id' => $storeid,
            'status' => 1,
            'isrecommand' => 1
        );
        $shopRecommand = $shopDishModel->getAll($where,"id,title,thumb,description,marketprice,productprice","id DESC LIMIT {$limit} , {$psize}");
        foreach ($shopRecommand as $key=>$val){
            $shopRecommand[$key]['marketprice'] = FormatMoney($val['marketprice'],0);
            $shopRecommand[$key]['productprice'] = FormatMoney($val['productprice'],0);
        }
        return $shopRecommand;
    }
    /**
     * 根据店铺id及筛选条件取出店铺商品列表
     *   */
    public function getShopByStoreid($data = array()){
        $storeid = $data['storeid'];
        if (empty($storeid)) return false;
        $shopDishModel = new shop_dish_model();
        $pindex = max(1, intval($data['page']));
        $psize = isset($data['limit']) ? $data['limit'] : 10;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $where = array(
            'sts_id' => $storeid,
            'status' => 1,
        );
        $where = to_sqls($where);
        if ($data['beginprice'] && $data['endprice']){
            $beginprice = FormatMoney($data['beginprice']);
            $endprice = FormatMoney($data['endprice']);
            $where .= " and marketprice >={$beginprice} and marketprice <= {$endprice} ";
        }elseif ($data['beginprice']){
            $beginprice = FormatMoney($data['beginprice']);
            $where .=" and marketprice >={$beginprice} ";
        }
        
        if ($data['type_name'] =='sales_num'){//销量
            $limitSql = "sales_num DESC ";
        }elseif ($data['type_name'] == 'is_new'){//上新
            $limitSql = "isnew DESC, id DESC";
        }elseif ($data['type_name'] == 'price'){//价格
            if ($data['type'] == 1){//上升
                $limitSql = "marketprice ASC";
            }elseif ($data['type'] == 0){
                $limitSql = "marketprice DESC";
            }
        }else{//综合
            $limitSql = "id DESC ";
        }
        $limitSql .=" LIMIT {$limit} , {$psize} ";
        $shopList = $shopDishModel->getAll($where,"id,title,thumb,description,marketprice,productprice",$limitSql);
        foreach ($shopList as $key=>$val){
            $shopList[$key]['marketprice'] = FormatMoney($val['marketprice'],0);
            $shopList[$key]['productprice'] = FormatMoney($val['productprice'],0);
        }
        return $shopList;
    }
    /**
     * 根据店铺id取出店铺活动文章
     *   */
    public function getShopadByStoreid($data = array()){
        $storeid = $data['storeid'];
        if (empty($storeid)) return false;
        $shopShopAdvModel = new store_shop_adv_model();
        $pindex = max(1, intval($data['page']));
        $psize = isset($data['limit']) ? $data['limit'] : 4;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $where = array(
            'ssa_shop_id' => $storeid,
        );
        $shop_adv = $shopShopAdvModel->getAll($where,"ssa_adv_id,ssa_title,ssa_sub_title,ssa_thumb,ssa_type,ssa_weixin_url,ssa_click_count","ssa_is_require_top DESC LIMIT {$limit} , {$psize}");
        if ($shop_adv){
            foreach ($shop_adv as $key=>$v){
                $img_list = explode(",", $v['ssa_thumb']);
                foreach ($img_list as $k=>$val){
                    if(empty($val)) unset($img_list[$k]);
                }
                $shop_adv[$key]['count'] = count($img_list);
                $shop_adv[$key]['ssa_thumb'] = $img_list;
            }
        }
        return $shop_adv;
    }
    /**
     * 取出所有的店铺广告信息
     *   */
    public function getAllAds($data = array()){
        $shopShopAdvModel = new store_shop_adv_model();
        $pindex = max(1, intval($data['page']));
        $psize = isset($data['limit']) ? $data['limit'] : 4;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $shop_adv = $shopShopAdvModel->getAll('',"ssa_adv_id,ssa_shop_id,ssa_title,ssa_sub_title,ssa_thumb,ssa_type,ssa_weixin_url,ssa_click_count","ssa_is_require_top DESC LIMIT {$limit} , {$psize}");
        if ($shop_adv){
            foreach ($shop_adv as $key=>$v){
                $storeInfo = $this->getStoreInfoByStoreid($v['ssa_shop_id']);
                if (empty($storeInfo)) unset($shop_adv[$key]);
                $shop_adv[$key]['storename'] = $storeInfo['sts_name'];
                $shop_adv[$key]['storethumb'] = $storeInfo['sts_avatar'];
                $img_list = explode(",", $v['ssa_thumb']);
                foreach ($img_list as $k=>$val){
                    if(empty($val)) unset($img_list[$k]);
                }
                $shop_adv[$key]['count'] = count($img_list);
                $shop_adv[$key]['ssa_thumb'] = $img_list;
            }
        }
        return $shop_adv;
    }
    /**
     * 根据店铺id取出店铺详细
     *   */
    public function getStoreInfoByStoreid($storeid){
        if (empty($storeid)) return '';
        $storeShopModel = new store_shop_model();
        $storeInfo = $storeShopModel->getOne(array('sts_id'=>$storeid));
        $storeInfo['identity'] = $this->getStoreIdentityInfo($storeid);
        return $storeInfo;
    }
    /**
     * 根据店铺id取出店铺营业执照
     *   */
    private function getStoreIdentityInfo($storeid){
        if (empty($storeid)) return '';
        $storeShopIdenModel = new \model\store_shop_identity_model();
        $storeInfo = $storeShopIdenModel->getOne(array('sts_id'=>$storeid));
        return $storeInfo;
    }
    /**
     * 获得店铺粉丝数量
     *   */
    public function getFunCount($storeid){
        if (empty($storeid)) return '';
        $memberBelongRelationModel = new member_belong_relation_model();
        $count = $memberBelongRelationModel->getAll(array('p_sid'=>$storeid),'id');
        $count = count($count);
        return $count;
    }
    /**
     * 根据店铺活动id，取出活动信息
     *   */
    public function getStoreAdvInfo($advid){
        if (empty($advid)) return '';
        $shopShopAdvModel = new store_shop_adv_model();
        $advInfo = $shopShopAdvModel->getOne(array('ssa_adv_id'=>$advid));
        if ($advInfo){
            $img_list = explode(",", $advInfo['ssa_thumb']);
            foreach ($img_list as $k=>$val){
                if(empty($val)) unset($img_list[$k]);
            }
            $advInfo['count'] = count($img_list);
            $advInfo['ssa_thumb'] = $img_list;
        }
        return $advInfo;
    }
    /**
     * 店铺活动的点击量增加1
     *   */
    public function addStoreAdvHits($advid){
        if (empty($advid)) return '';
        $shopShopAdvModel = new store_shop_adv_model();
        $sql = "update ".table($shopShopAdvModel->table_name)." set ssa_click_count=ssa_click_count+1 where ssa_adv_id={$advid}";
        $res = $shopShopAdvModel->query($sql);
        if ($res) return true;
    }
    /**
     * 根据dishid取出商品信息
     *   */
    public function getGoodsInfoByDishid($dishid){
        if (empty($dishid)) return '';
        $shopDishModel = new shop_dish_model();
        $info = $shopDishModel->getOne(array('id'=>$dishid));
        if (empty($info)) return '';
        $piclist = $this->getGoodsPiclist($dishid);
        $info['piclist'] = $piclist['piclist'];
        $info['contentpicurl'] = $piclist['contentpicurl'];
        $info['categoreyname'] = $this->getCategoryName($info['store_p2']);
        $info['brandarr'] = $this->getBrantName($info['brand']);
        return $info;
    }
    /**
     * 根据dishid取出商品的轮播图及详情页的图
     *   */
    private function getGoodsPiclist($dishid){
        if (empty($dishid)) return '';
        $shopDishPiclistModel = new \model\shop_dish_piclist_model();
        $info = $shopDishPiclistModel->getOne(array('id'=>$dishid),'picurl,contentpicurl');
        if (!empty($info)){
            $piclist = explode(",", $info['picurl']);
            $contentpicurl = explode(",", $info['contentpicurl']);
            $return = array('piclist'=>$piclist,'contentpicurl'=>$contentpicurl);
            return $return;
        }
    }
    /**
     * 根据分类id取分类名称
     *   */
    private function getCategoryName($categoryid){
        if (empty($categoryid)) return '';
        $shopCategoryModel = new \model\shop_category_model();
        $category = $shopCategoryModel->getOne(array('id'=>$categoryid),'name');
        if ($category['name']) {
            return $category['name'];
        }
    }
    /**
     * 根据品牌id取品牌名称
     *   */
    private function getBrantName($brandid){
        if (empty($brandid)) return '';
        $shopBrantModel = new \model\shop_brand_model();
        $brandarr = $shopBrantModel->getOne(array('id'=>$brandid),'brand,icon');
        if ($brandarr['brand']) {
            return $brandarr;
        }
    }
}