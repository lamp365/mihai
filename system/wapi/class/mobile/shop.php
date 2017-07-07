<?php
/**
 *小程序商品页
 */

namespace wapi\controller;
class shop extends base{
    /**
     * 商品列表页
     *   */
    public function shopList(){
        $_GP = $this->request;
        $type = intval(isset($_GP['type']))? intval($_GP['type']) : 1;//类型,1是一级栏目，2是二级栏目，3是关键词
        if ($type == 3){
            $keyword = $_GP['keyword'];//关键词
            if (empty($keyword)) ajaxReturnData(0,'请填写关键词搜索');
        }else{
            $id = intval($_GP['id']);//栏目id
            if (empty($id)) ajaxReturnData(0,'参数错误');
        }
        $jd = $_GP['longitude'];//经度
        $wd = $_GP['latitude'];//纬度
        $minpriceTemp = $_GP['minprice'];//最小价格
        $maxpriceTemp = $_GP['maxprice'];//最大价格
        $timearea = $_GP['timearea'];//时间区域
        
        if (!empty($minpriceTemp)) $minprice = FormatMoney($minpriceTemp,1);
        if (!empty($maxpriceTemp)) $maxprice = FormatMoney($maxpriceTemp,1);
        //分页
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
        $limit= ($pindex-1)*$psize;
        
        //根据经纬度获取在该区域配送的店铺
        $storeShop = new \service\shopwap\storeShopService();
        $return = $storeShop->getStoreByJdAndWd($jd,$wd);
        if (empty($return)) ajaxReturnData('0','抱歉，该地理位置没有找到相关店铺的商品');
        foreach ($return as $v){
            $ids[] = " b.sts_id = {$v['sts_id']} ";
        }
        $ids = implode(' or ' , $ids);
        
        
        $where = " b.status=1 and b.gid>0 "; 
        $where .= ' and ('.$ids.') ';
        //栏目或者关键词
        if ($type == 3){
            if (function_exists('scws_new') ){
                $word = get_word($keyword);
                if ( !empty($word) && is_array($word) ){
                    foreach ($word as $word_value ) {
                        $keys[] = " b.title like '%".$word_value."%' ";
                    }
                     
                    $keys = implode(' or ' , $keys);
                    $where .= ' and ('.$keys.')';
                }
            }else {
                //$where .=" AND LOCATE('$keyword',b.title) >0";
            }
        }else{
            $where .=" and a.ccate='$id' ";
        }
        //价格
        if ($minprice && $maxprice){
            if ($maxprice < $minprice) ajaxReturnData(0,'抱歉，价格输入不正确');
            $where .= " AND b.marketprice >= '$minprice' AND  b.marketprice <= '$maxprice' ";
        }elseif ($minprice){
            $where .= " AND b.marketprice >= '$minprice' ";
        }elseif ($maxprice) {
            $where .= " AND b.marketprice <= '$maxprice' ";
        }
        
        
        $sql = "select b.sts_id,b.id,b.title,b.thumb,b.marketprice,b.productprice,b.store_count from ".table('shop_goods')." AS a right join ".table('shop_dish')." AS b on a.id=b.gid ";      
        //搜索的时间区域和自动筛选出来
        if (!empty($timearea)){
            $active = getCurrentAct();
            if (empty($active)) ajaxReturnData(0,'暂无限时购活动');
            $ac_id = $active['ac_id'];
            $where .=" and c.ac_dish_status = 1 and c.ac_action_id = '$ac_id' ";
            $timeareaArr = explode(",",$timearea);
            $timeareaArr[0] = '0';
            foreach ($timeareaArr as $v){
                $timeWhere[] = " c.ac_area_id='$v' ";
            }
            $timearea = implode(' or ' , $timeWhere);
            //$timeareaStr = to_sqls($timearea,'','a.ac_area_id');
            $where3 = $where.' and ('.$timearea.') ';
            $sql .= " LEFT JOIN ".table('activity_dish')." AS c ON b.id=c.ac_shop_dish  WHERE ".$where3;
        }else{
            $sql .= " where $where ";
        }
        //排序
        $status = intval(isset($_GP['status'])) ? intval($_GP['status']) : 1;//1表示综合，2表示价格，3表示销量
        $orderby = ' order by ';
        if ($status == 1){
            $orderby .= " b.id DESC ";
        }elseif ($status == 2){
            $price_type = $_GP['price_type'] ? $_GP['price_type'] : '1';
            if($price_type == 1){
                $price = ' asc ';
            }else{
                $price = ' desc ';
            }
            $orderby .= " b.marketprice $price ";
        }else {
            $orderby .= " b.sales_num DESC ";
        }
        $limit = " limit ".$limit." , ".$psize;
        $sql .= $orderby.$limit;
        logRecord($sql, "shopList");
        $list = mysqld_selectall($sql);
        if (empty($list)) ajaxReturnData(1,'暂时没有商品');
        
        $storeShopModel = new \model\store_shop_model();
        $regionM = new \model\region_model();
        foreach ($list as $key=>$v){
            $list[$key]['ac_dish_price'] = '';
            $list[$key]['is_active'] = 0;
            $flag = checkDishIsActive($v['id'],$v['store_count']);
            if (!empty($flag)){
                if ($flag['ac_dish_total'] > 0){
                    $list[$key]['is_active'] = 1;
                    $list[$key]['ac_dish_price'] = FormatMoney($flag['ac_dish_price'],0);
                }
            }
            $list[$key]['marketprice'] = FormatMoney($v['marketprice'],0);
            $list[$key]['productprice'] = FormatMoney($v['productprice'],0);
            $store = $storeShopModel->getOneStoreShop(array('sts_id'=>$v['sts_id']),'sts_id,sts_name,sts_avatar');
            $list[$key]['storeInfo'] = $store;
        }
        ajaxReturnData(1,'',$list);
    }
    /**
     * 热搜词
     *   */
    public function gethot(){
        $_GP = $this->request;
        $catid = intval($_GP['catid']);
        $actService = new \service\wapi\activityService();
        $info = $actService->gethot($catid);
        if (empty($info)) ajaxReturnData('1','暂无数据');
        ajaxReturnData('1','',$info);
    }

    /**
     * 获取购物车最新的时间
     * 不放入购物车模块，因为购物车模块必选先授权登录
     */
    public function getcarttime()
    {
        check_shop_cart_time();
        $_GP      = $this->request;
        $meminfo  = get_member_account();
        $cart_num = getCartTotal(2);
        $last_time = 0;
        if(!empty($cart_num)){
            $cart_info = mysqld_select("select last_time from ".table('shop_cart_record')." where session_id='{$meminfo['openid']}'");
            $last_time = $cart_info['last_time'] ?: 0;
        }
        ajaxReturnData(1,'请求成功',array('cart_num'=>$cart_num,'last_time'=>$last_time,'now_time'=>time()));
    }
}