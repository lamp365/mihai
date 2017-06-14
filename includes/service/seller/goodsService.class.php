<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/7
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\seller\goodsService();
if($a->todo()){
    //操作成功 则继续业务
}else{
    message($a->getError());
}
 */
namespace service\seller;

class goodsService extends \service\publicService
{
    public function addGoods($_GP){

        //1、检验参数是否有误 是否个别必须填写的未填写
        $chekres = $this->beforeAddCheckParame($_GP);
        if(!$chekres){
            return false;
        }

        $meminfo = get_member_account();
        // 店铺id/分类1_分类2/年月/xxxxxxxx.jpg
        $alidir  = $meminfo['store_sts_id'].'/'.$_GP['store_p1'].'_'.$_GP['store_p2'].'/'.date("Ym");
        $thumb = $_GP['thumb']!=''?$_GP['thumb']:$_GP['xcimg'][0];

        $contentArr = changeWebImgToAli($_GP['content']);

        if(count($_GP['xqimg']) <= 0)
        {
            $_GP['xqimg'] = $contentArr['img'];
        }
        
        if(count($_GP['xqimg']) <= 0)
        {
            $is_contentimg = 0;
        }
        else{
            $is_contentimg = 1;
        }
        @$contentArr['content'] = $contentArr['content']!=''?$contentArr['content']:'';

        $data = array(
            'gid'          => $_GP['gid'],
            'store_p1'     => $_GP['store_p1'],
            'store_p2'     => $_GP['store_p2'],
            'status'       => $_GP['status'],
            'title'        => $_GP['title'],
            'thumb'        => $thumb,    //缩略图等待
            'description'  => $_GP['description'],
            'content'      => trim($contentArr['content']),
            'commision'    => $_GP['commision'],
            'marketprice'    => FormatMoney($_GP['marketprice']),
            'productprice'   => FormatMoney($_GP['productprice']),
            'goodssn'        => $_GP['goodssn'],
            'store_count'    => $_GP['store_count'],
            'createtime'   => time(),
            'updatetime'   => time(),
            'gtype_id'     => intval($_GP['gtype_id']),
            'isnew'        => $_GP['isnew'],
            'isreason'     => $_GP['isreason'],
            'qr_code'      => $_GP['qr_code'],
            'brand'        => $_GP['brand'],
            'sts_id'       => $meminfo['store_sts_id'],
            'is_contentimg'=> $is_contentimg
        );
        if(empty($_GP['dish_id'])){
            mysqld_insert('shop_dish',$data);
            $dish_id = mysqld_insertid();
            
            //图片上传 squdian_shop_dish_piclist
            if(count($_GP['xqimg'])>0 || count($_GP['xcimg'])>0)
            {
                $picData  = array();
                $xcimgStr = '';
                $xqimg    = '';

                if(count($_GP['xcimg'])>0){
                    foreach($_GP['xcimg'] as $v){
                        $xcimgStr .= $v.',';
                    }
                    $xcimgStr = rtrim($xcimgStr,',');
                    $picData['picurl']        = $xcimgStr;
                }

                if(count($_GP['xqimg'])>0){
                    foreach($_GP['xqimg'] as $v){
                        $xqimgStr .= $v.',';
                    }
                    $xqimgStr = rtrim($xqimgStr,',');
                    $picData['contentpicurl'] = $xqimgStr;
                }

                $picData['goodid']        = $dish_id;
                mysqld_insert('shop_dish_piclist',$picData);
                
                $pic_id = mysqld_insertid();
            }
            
        }else{
            //获取宝贝的配置信息和价格信息 shop_dish
            $checkDish = checkSellerRoler();
            if($checkDish == 3)
            {
                $dishObj   = new ShopDishService();
                $dishInfo  = $dishObj->getDishInfo($_GP['dish_id'],'marketprice,productprice');
                $dishPrice = $dishObj->dishPrice($_GP['dish_id'],'marketprice,productprice,spec_key');
                
                //规格价格判断
                if(!is_array($_GP['itemPriceJson']))
                {
                    $itemPriceJson = html_entity_decode($_GP['itemPriceJson']);
                    $itemPriceJson = json_decode($itemPriceJson,true);
                }
                else{
                    $ischeck = 1;
                }
                
                if(count($itemPriceJson)==count($dishPrice) && count($itemPriceJson)>0)
                {
                    foreach($itemPriceJson as $k=>$v){
                            $spec_key_str = '';
                            $spec_key_str = $v['spec_key'];
                            $spec_key_arr = explode('_',$spec_key_str);
                            sort($spec_key_arr);   //对 spec_item_id 进行升序 为了 下单购物时方便检索
                            $spec_key = implode('_',$spec_key_arr);
                        foreach($dishPrice as $vv){
                            if($vv['spec_key'] == $spec_key){
                                $v['marketprice'] = FormatMoney($v['marketprice']);
                                $v['productprice'] = FormatMoney($v['productprice']);
                                if($vv['marketprice'] != $v['marketprice'] || $vv['productprice'] != $v['productprice'])
                                {
                                    $ischeck = 1;
                                }
                            }
                        }
                    }
                }
                
                
                if($data['marketprice'] != $dishInfo['marketprice'] || $data['productprice'] != $dishInfo['productprice'] || count($itemPriceJson) != count($dishPrice) || $ischeck > 0)
                {
                    $data['status']  = 0;
                }
            }
            
            mysqld_update('shop_dish',$data,array('id'=>$_GP['dish_id']));
            $dish_id = $_GP['dish_id'];
            
            if(count($_GP['xcimg'])>0 || count($_GP['xqimg'])>0)
            {
                $picData  = array();
                $xcimgStr = '';
                $xqimg    = '';

                if(count($_GP['xcimg'])>0){
                    foreach($_GP['xcimg'] as $v){
                        $xcimgStr .= $v.',';
                    }
                    $xcimgStr = rtrim($xcimgStr,',');
                    $picData['picurl']        = $xcimgStr;
                }

                if(count($_GP['xqimg'])>0){
                    foreach($_GP['xqimg'] as $v){
                        $xqimgStr .= $v.',';
                    }
                    $xqimgStr = rtrim($xqimgStr,',');
                    $picData['contentpicurl'] = $xqimgStr;
                }

                $picData['goodid']        = $dish_id;
                mysqld_update('shop_dish_piclist',$picData,array('goodid'=>$_GP['dish_id']));
            }
            
        }
        
        if(empty($dish_id)){
            $this->error = '操作失败';
            return false;
        }
        
        //插入规格
        $this->addSpecOnaddGood($dish_id,$_GP);
        return $dish_id;
    }

    /**
     * 编辑或者添加商品的时候 对数据的校验
     * @param $_GP
     * @return bool
     */
    public function beforeAddCheckParame($_GP){
        if(empty($_GP['store_p1']) || empty($_GP['store_p2'])){
            $this->error = '请选择分类';
            return false;
        }
        if(empty($_GP['title'])){
            $this->error = '商品名称必须填写';
            return false;
        }
        
        if(empty($_GP['marketprice'])){
            $this->error = '促销价必须填写';
            return false;
        }
        
        if(empty($_GP['productprice'])){
            $this->error = '市场价必须填写';
            return false;
        }
        //
        if(empty($_GP['store_count'])){
            $this->error = '库存必须填写';
            return false;
        }
        
        //if(empty($_GP['xcimg'])){
            //$this->error = '产品缩略图必须上传';
            //return false;
        //}
        return true;
    }

    /**
     * 无论编辑商品 还是 添加产品  都可以调用该方法 进行操作 规格项对应价格得处理
     数据格式   key  对应 json字符串
     array('itemPriceJson'  => [{"场地":"香港","保质期":"五年","productprice":"2342","marketprice":"34","store_count":"23121","bar_code":"312","spec_key":"37_39"},{"场地":"意大利","保质期":"五年","productprice":"3423","marketprice":"3421","store_count":"1232","bar_code":"234234","spec_key":"36_39"}] )
     * @param $dish_id
     * @param $_GP
     * @return string
     */
    public function addSpecOnaddGood($dish_id,$_GP)
    {
        //1先删除 规格价格 后再次添加
        mysqld_delete('dish_spec_price',array('dish_id'=>$dish_id));
        if(empty($_GP['itemPriceJson'])){
            return '';
        }

        if(!is_array($_GP['itemPriceJson']))
        {
            $itemPriceJson = html_entity_decode($_GP['itemPriceJson']);
            $itemPriceJson = json_decode($itemPriceJson,true);
        }
        else{
            $itemPriceJson = $_GP['itemPriceJson'];
        }
        foreach($itemPriceJson as $itemprice){
            $spec_key_str = $itemprice['spec_key'];
            $spec_key_arr = explode('_',$spec_key_str);
            sort($spec_key_arr);   //对 spec_item_id 进行升序 为了 下单购物时方便检索

            //从头部取出规格中文名字  组装成 内存:16G@硬盘500G
            $length = count($spec_key_arr);
            $j = 0;
            $spec_name = '';
            foreach($itemprice as $key => $val){
                if($j == $length){
                    break;
                }
                //内存|^|8G@@硬盘|^|32G
                $spec_name .= $key.'|^|'.$val.'@@';
                $j++;
            }
            !empty($spec_name) && $spec_name = trim($spec_name,'@@');
            $spec_key = implode('_',$spec_key_arr);
            $data = array();
            $data = array(
                'dish_id'        => $dish_id,
                'spec_key'       => $spec_key,
                'key_name'       => $spec_name,
                'marketprice'    => FormatMoney($itemprice['marketprice']),
                'productprice'   => FormatMoney($itemprice['productprice']),
                'store_count'    => $itemprice['store_count'],
                'bar_code'       => $itemprice['bar_code'],
                'createtime'     => time(),
                'updatetime'     => time(),
            );
            $insertStatus = mysqld_insert('dish_spec_price',$data);
        }
        return $insertStatus;
    }

    public function getSpecInfoOnEdit($dish_id)
    {
        $spec_price = mysqld_selectall("select * from ".table('dish_spec_price')." where dish_id={$dish_id}");
        if(empty($spec_price)){
            return '';
        }
        //组装数据
        $spec_info = $item_arr = array();
        foreach($spec_price as $key=> $one_row){
            $spec_data = array();
            //内存|^|8G@@硬盘|^|32G  得到 array(内存=>8G)
            $spec_data = analyzeSpecprice_keyname($one_row['key_name'],$item_arr);
            $spec_data['productprice'] = FormatMoney($one_row['productprice'],2);
            $spec_data['marketprice']  = FormatMoney($one_row['marketprice'],2);
            $spec_data['store_count']  = $one_row['store_count'];
            $spec_data['bar_code']     = $one_row['bar_code'];
            $spec_data['spec_key']     = $one_row['spec_key'];
            $spec_info[] = $spec_data;
        }
        return array('spec_info'=>$spec_info,'item_arr'=>$item_arr);
    }
    
    public function goodsListPage($data,$fields='*'){
        $memberData   = get_member_account();
        
        //获取用户导入的产品
        $sql_gids = "select gid from ".table('shop_dish')." where sts_id = {$memberData['store_sts_id']} and industry_p2_id = {$memberData['sts_category_p2_id']} and gid > 0";
        $rs_gids  = mysqld_selectall($sql_gids);
        $gidStr = '';
        foreach($rs_gids as $v){
            $gidStr .= $v['gid'].',';
        }
        $gidStr = rtrim($gidStr,',');
        
        $data['page'] = max(1, intval($data['page']));
        $data['limit'] = $data['limit']>0?$data['limit']:10; 
  
        $wheres = "1 and deleted = 0";
        
        if($gidStr != '')
        {
            $wheres .= " and id not in ($gidStr)";
        }
        
        if($data['pcate'] > 0)
        {
            $wheres .= " and pcate = {$data['pcate']}";
        }
        
        if($data['ccate'] > 0)
        {
            $wheres .= " and ccate = {$data['ccate']}";
        }
        
        if($data['brands'] != '')
        {
            $wheres .= " and brand in({$data['brands']})";
        }
        
        if(isset($data['status']))
        {
            $wheres .= " and status = {$data['status']}";
        }
        
        if($data['store_count'] != '')
        {
            $order = "order by marketprice {$data['store_count']}";
        }
        else{
            $order = "order by sort desc";
        }
        
        if($data['marketprice_less'] > 0 || $data['marketprice_many'] > 0)
        {
            $data['marketprice_less'] = $data['marketprice_less']>0?FormatMoney($data['marketprice_less']):0;
            $data['marketprice_many'] = $data['marketprice_many']>0?FormatMoney($data['marketprice_many']):'99999999';
            $wheres .= " and marketprice BETWEEN {$data['marketprice_less']} AND {$data['marketprice_many']}";
        }
        if($data['search_key'] != '')
        {
            $wheres .= " and title like '%{$data['search_key']}%'";
        }
        
        $limit = " LIMIT " . ($data['page'] - 1) * $data['limit'] . ',' . $data['limit'];
        $sql = "select {$fields} from ".table('shop_goods')." where {$wheres} {$order} {$limit}";
        $goodsList['goodslist'] = mysqld_selectall($sql);
        foreach($goodsList['goodslist'] as $k=>$v){
           $goodsList['goodslist'][$k]['marketprice']     = FormatMoney($v['marketprice'],2);
           $goodsList['goodslist'][$k]['productprice']    = FormatMoney($v['productprice'],2);
        }
        $goodsList['goodslisttotal'] = mysqld_select("select count(0) as total from ".table('shop_goods')." where {$wheres}");
        
        return $goodsList;
    }
    
    
    //根据
    public function getGoodInfo($data,$fields='*'){
        $redata = array();

        $sql  = "select {$fields} from ".table('shop_goods')." where id = {$data['id']}";
        $redata = mysqld_select($sql);

        $redata['marketprice']     = FormatMoney($redata['marketprice'],2);
        $redata['productprice']    = FormatMoney($redata['productprice'],2);

        return $redata;
    }
    
    //
    public function getGoodInfos($data,$fields='*'){
        $redata = array();

        $sql  = "select {$fields} from ".table('shop_goods')." where id in ({$data['goods_ids']})";
        foreach($redata as $k=>$v){
            $redata[$k]['marketprice']     = FormatMoney($v['marketprice'],2);
            $redata[$k]['productprice']    = FormatMoney($v['productprice'],2);
        }
        $redata = mysqld_selectall($sql);
        
        return $redata;
    }
    
    public function getGoodInfoGroup($data,$fields='*'){
        $redata = array();

        $sql  = "select {$fields} from ".table('shop_goods')." where pcate in ({$data['categoryOneIds']})";
        
        $redata = mysqld_selectall($sql);
        foreach($redata as $k=>$v){
            $redata[$k]['marketprice']     = FormatMoney($v['marketprice'],2);
            $redata[$k]['productprice']    = FormatMoney($v['productprice'],2);
        }
        
        return $redata;
    }
    
    //搜索产品库产品
    public function searchGoods($data=array(),$fields='*'){
        $sql = "select {$fields} from ".table('shop_goods')." where title like '{$data['key']}%' limit {$data['page']},{$data['limit']}";
        $redata = mysqld_selectall($sql);
        
        foreach($redata as $k=>$v){
           $redata[$k]['marketprice']     = FormatMoney($v['marketprice'],2);
           $redata[$k]['productprice']    = FormatMoney($v['productprice'],2);
        }
        
        $data_total = mysqld_select("select count(0) as total from ".table('shop_goods')." where title like '{$data['key']}%'");
        $redata['total'] = $data_total['total'];
        return $redata;
    }
    
    //根据分类一级和二级分类id获取宝贝信息
    public function getPcontent($_GP,$fields='*'){
        $data = array();

        $sql  = "select {$fields} from ".table('shop_goods')." where ccate = {$_GP['store_p2']}";
        $data = mysqld_selectall($sql);
        
        foreach($data as $k=>$v){
            $data[$k]['marketprice']     = FormatMoney($v['marketprice'],2);
            $data[$k]['productprice']    = FormatMoney($v['productprice'],2);
        }
        
        return $data;
    }
    
    //获取产品库图片
    public function getGoodsPic($_GP,$fields='*'){
        $data = array();
        $sql  = "select {$fields} from ".table('shop_goods_piclist')." where goodid = {$_GP['goodid']}";
        $data = mysqld_select($sql);
        return $data;
    }
    
}