<?php
/**
店铺的service层
 */
namespace service\shopwap;
use model\store_shop_model;
use model\member_belong_relation_model;
use model\store_shop_adv_model;
use service\publicService;
class storeShopService extends publicService
{
    /**
     * 获得单条store_shop表信息
     *   */
    public function getOneStoreShop($where = array(),$param="*"){
        if (empty($where)) return false;
        $storeShopModel = new store_shop_model();
        return $storeShopModel->getOne($where,$param);
    }
    /**
     * 获得多条store_shop表信息
     *   */
    public function getAllStoreShop($where = array(),$param="*",$orderby=false){
        $storeShopModel = new store_shop_model();
        return $storeShopModel->getAll($where,$param,$orderby);
    }
    
    /**
     * 取出店铺的特卖商品
     *   */
    public function getStoreTMshop($storeid){
        if (empty($storeid)) return '';
        $where = array(
            'sts_id' => $storeid,
            'status' => 1,
            'isdiscount' => 1
        );
        $shopDishService = new \service\shopwap\shopDishService();
        $shopDiscount = $shopDishService->getAllShopDish($where,'id,title,thumb,description,marketprice,productprice',"id DESC LIMIT 0 , 4");
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
        $pindex = max(1, intval($data['page']));
        $psize = isset($data['limit']) ? $data['limit'] : 10;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $where = array(
            'sts_id' => $storeid,
            'status' => 1,
            'isrecommand' => 1
        );
        $shopDishService = new \service\shopwap\shopDishService();
        $shopRecommand = $shopDiscount = $shopDishService->getAllShopDish($where,'id,title,thumb,description,marketprice,productprice',"id DESC LIMIT {$limit} , {$psize}");
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
        $shopDishService = new \service\shopwap\shopDishService();
        $shopList = $shopDiscount = $shopDishService->getAllShopDish($where,'id,title,thumb,description,marketprice,productprice',$limitSql);
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
        $pindex = max(1, intval($data['page']));
        $psize = isset($data['limit']) ? $data['limit'] : 4;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $where = array(
            'ssa_shop_id' => $storeid,
        );
        $shopShopAdvModel = new store_shop_adv_model();
        $shop_adv = $shopShopAdvModel->getAllShopAdv($where,"ssa_adv_id,ssa_title,ssa_sub_title,ssa_thumb,ssa_type,ssa_weixin_url,ssa_click_count","ssa_is_require_top DESC LIMIT {$limit} , {$psize}");
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
        $shop_adv = $shopShopAdvModel->getAllShopAdv('',"ssa_adv_id,ssa_shop_id,ssa_title,ssa_sub_title,ssa_thumb,ssa_type,ssa_weixin_url,ssa_click_count","ssa_is_require_top DESC LIMIT {$limit} , {$psize}");
        if ($shop_adv){
            foreach ($shop_adv as $key=>$v){
                $storeInfo = $this->getOneStoreShop(array('sts_id'=>$v['ssa_shop_id']),'sts_name,sts_avatar');
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
     * 根据店铺id取出店铺详细信息
     *   */
    public function StoreDetailByStoreid($storeid,$param = "*"){
        if (empty($storeid)) return '';
        $storeInfo =  $this->getOneStoreShop(array('sts_id'=>$storeid),$param);
        $storeInfo['identity'] = $this->getStoreIdentityInfo($storeid,$param);
        return $storeInfo;
    }
    /**
     * 根据店铺id取出店铺营业执照
     *   */
    private function getStoreIdentityInfo($storeid){
        if (empty($storeid)) return '';
        $storeShopIdenModel = new \model\store_shop_identity_model();
        $storeInfo = $storeShopIdenModel->getOneStoreShopIdentity(array('sts_id'=>$storeid));
        return $storeInfo;
    }
    /**
     * 获得店铺粉丝数量
     *   */
    public function getFunCount($storeid){
        if (empty($storeid)) return '';
        $memberBelongRelationModel = new member_belong_relation_model();
        $count = $memberBelongRelationModel->getAllMemberBelong(array('p_sid'=>$storeid),'id');
        $count = count($count);
        return $count;
    }
    /**
     * 根据店铺活动id，取出活动信息
     *   */
    public function getStoreAdvInfo($advid){
        if (empty($advid)) return '';
        $shopShopAdvModel = new store_shop_adv_model();
        $advInfo = $shopShopAdvModel->getOneShopAdv(array('ssa_adv_id'=>$advid));
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
     * $detail true 获取详细（包括图,栏目等等），false 获取普通的信息
     *   */
    public function getGoodsInfoByDishid($dishid,$param="*"){
        if (empty($dishid)) return '';
        $shopDishService = new \service\shopwap\shopDishService();
        $info = $shopDishService->getOneShopDish(array('id'=>$dishid),$param);
        if (empty($info)) return '';
        //取出商品的轮播图及详情页的图
        $shopDishPiclistModel = new \model\shop_dish_piclist_model();
        $shopDishPiclist = $shopDishPiclistModel->getOneShopDishPiclist(array('id'=>$dishid),'picurl,contentpicurl');
        if (!empty($shopDishPiclist)){
            $piclist = explode(",", $shopDishPiclist['picurl']);
            $contentpicurl = explode(",", $shopDishPiclist['contentpicurl']);
            $info['piclist'] = $piclist;
            $info['contentpicurl'] = $contentpicurl;
        }
        //取分类名称
        $info['categoreyname'] = $this->getCategoryName($info['store_p2']);
        //取品牌名称
        $info['brandarr'] = $this->getBrantName($info['brand']);
        return $info;
    }
   
    /**
     * 根据分类id取分类名称
     *   */
    private function getCategoryName($categoryid){
        if (empty($categoryid)) return '';
        $shopCategoryModel = new \model\shop_category_model();
        $category = $shopCategoryModel->getOneShopCategory(array('id'=>$categoryid),'name');
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
        $brandarr = $shopBrantModel->getOneShopBrand(array('id'=>$brandid),'brand,icon');
        if ($brandarr['brand']) {
            return $brandarr;
        }
    }
    /***
     * 通过经纬度返回在这个区域配送的店铺
     *   */
    public function getStoreByJdAndWd($jd='',$wd=''){
        //根据经纬度找到城市code和区域code
        $codeArr = get_area_code_by_jw($jd,$wd);
        if (is_array($codeArr)){
            $cityCode = $codeArr['citycode'];
            $areaCode ='';
            if ($codeArr['status']==1){
                $areaCode = $codeArr['areaCode'];
            }
        }
        
        if ($areaCode){
            $where = " IF(b.level_type>1,a.sts_city='$cityCode',a.sts_region='$areaCode' )";
        }else{//如果得到的区域code为空，说明用户没有定位或者高德没有返回区域code,此时取默认城市的code
            $where = " a.sts_city='$cityCode' ";
        }
        $where .=" and a.is_ban=1 ";
        $sql = "select a.sts_id,a.sts_province,a.sts_city,a.sts_region,b.level_type from ".table('store_shop')." as a left join ".table('store_shop_level')." as b on a.sts_shop_level=b.rank_level where ".$where;
        $ShopDishModel = new \model\shop_dish_model();
        $return = $ShopDishModel->fetchall($sql);
        return $return;
    } 
}