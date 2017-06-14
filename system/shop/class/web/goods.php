<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/12
 * Time: 17:49
 */
namespace shop\controller;

class goods
{
    private $shopIndustry;


    public function __construct() {
        $this->shopIndustry = new \service\seller\ShopIndustryService();
    }
    
    public $request = '';
    public function display()
    {
        $_GP = $this->request;
        
        //获取一级行业分类
        $oneIndustry = $this->shopIndustry->oneIndustryCategory();
        
        $twoIndustry = $this->shopIndustry->twoIndustryCategory(intval($_GP['industry_p1_id']));
        
        $parent_category = getCategoryAllparent("id,name",$_GP['industry_p1_id'],$_GP['industry_p2_id']);
        $first_son       = array();

        $pindex = max(1, intval($_GP['page']));
        $psize = 10;
        $condition = '';

       if (! empty($_GP['p1'])) {
            $cid = intval($_GP['p1']);
            $condition .= " AND pcate = '{$cid}'";
           $first_son   = getCategoryByParentid($_GP['p1']);
        }
        if (! empty($_GP['p2'])) {
            $cid = intval($_GP['p2']);
            $condition .= " AND ccate = '{$cid}'";
        }

        if (isset($_GP['status']) && $_GP['status'] != '') {
            $condition .= " AND status = '" . intval($_GP['status']) . "'";
        }
        if (! empty($_GP['keyword'])) {
            $key_type = $_GP['key_type'];
            if ( $key_type == 'title' ){
                $condition .= " AND title LIKE '%{$_GP['keyword']}%'";
            }elseif ( $key_type == 'sn' ) {
                $condition .= " AND goodssn LIKE '%{$_GP['keyword']}%'";
            }else{
                $condition .= " AND id = {$_GP['keyword']}";
            }
        }
        
        if(!empty($_GP['industry_p2_id']))
        {
            //$_GP['industry_p2_id']
            $condition .= " AND industry_p2_id = {$_GP['industry_p2_id']}";
        }
        
//        ppd($condition);
        $list = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE deleted = 0 $condition  ORDER BY id DESC, status DESC, sort DESC  LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_goods') . " WHERE 1=1 $condition");
        $pager = pagination($total, $pindex, $psize);
        
        include page('goods/goods_list');
    }
    
    //获取二级行业分类
    public function twoIndustry(){
        $_GP = $this->request;
        
        //获取一级行业分类
        $twoIndustry = $this->shopIndustry->twoIndustryCategory($_GP['id']);

        echo json_encode($twoIndustry);
        exit;
    }
    
    /**
     * 发布产品第一步先选择分类
     */
    public function post()
    {
        $_GP = $this->request;
        
        //获取一级行业分类
        $oneIndustry = $this->shopIndustry->oneIndustryCategory();
        
        if($_GP['industry_p1_id'] > 0)
        {
            $twoIndustry = $this->shopIndustry->twoIndustryCategory($_GP['industry_p1_id']);
        }
        else{
            $twoIndustry = array();
        }
        
        $parent_category = getCategoryAllparent("id,name",$_GP['industry_p1_id'],$_GP['industry_p2_id']);
        
        include page('goods/choose_type');
    }

    public function post_product()
    {
        $_GP    = $this->request;
        $item   = $piclist = array();
        $brand  = $gtype_list =  array();

        $id     = intval($_GP['id']);
        if(empty($id)){
            //新添加的
            if(empty($_GP['p1']) || empty($_GP['p2'])){
                $url = web_url('goods',array('op'=>'post'));
                message('请选择分类',$url,'error');
            }
            $piclist = array();
            $item    = array();
        }else{
            //修改的
            $item = mysqld_select("SELECT * FROM " . table('shop_goods') . " WHERE id = :id", array(':id' => $id));
            if (empty($item)) {
                message('抱歉，商品不存在或是已经删除！', '', 'error');
            }
            if(empty($_GP['p1']) && empty($_GP['p2'])){
                $_GP['p1'] = $item['pcate'];
                $_GP['p2'] = $item['ccate'];
            }
            $piclist = mysqld_select("SELECT * FROM " . table('shop_goods_piclist') . " where goodid={$id}");
            if(!empty($piclist['picurl'])){
                $piclist = explode(',',$piclist['picurl']);
            }else{
                $piclist = array();
            }
        }

        $brand         = getBrandByCategory(0,0,0);
        //获取商品模型
        $gtype_list    = getGoodtypeByCategory($_GP['p1'],$_GP['p2'],0);
        $cat_name1     = mysqld_select("select name from ".table('shop_category')." where id={$_GP['p1']}");
        $cat_name2     = mysqld_select("select name from ".table('shop_category')." where id={$_GP['p2']}");

        include page('goods/goods');
    }

    /**
     * 表单提交操作 商品发布
     */
    public function do_addgoods()
    {
        $_GP = $this->request;
//        ppd($_GP);
        $id    = intval($_GP['id']);
        if (empty($_GP['title'])) {
            message('请输入商品名称！');
        }
        if (empty($_GP['pcate'])) {
            message('请选择商品分类！');
        }
        if ( empty($_GP['marketprice']) && ($_GP['marketprice'] <= 0) ){
            message('产品价格不能小于和等于0');
        }

        $data = array(
            'pcate'    => intval($_GP['pcate']),
            'ccate'    => intval($_GP['ccate']),
            'brand'    => $_GP['brand'],
            'gtype_id' => intval($_GP['goods_type']),
            'type'     => 0,
            'title'    => $_GP['title'],
            'subtitle' => isset($_GP['subtitle'])?$_GP['subtitle']:'',
            'status'    => $_GP['status'],
            'sort'      => intval($_GP['sort']),
            'description' => $_GP['description'],
            'content'     => changeUeditImgToAli($_GP['content']),
            'goodssn'     => $_GP['goodssn'],
            'marketprice' => FormatMoney($_GP['marketprice']),
            'productprice' => FormatMoney($_GP['productprice']),
            'store_count'  => intval($_GP['store_count']),
            'totalcnf'    => intval($_GP['totalcnf']),
            'credit'      => intval($_GP['credit']),
            'createtime'  => TIMESTAMP,
            'isnew'       => intval($_GP['isnew']),
            'isfirst'     => intval($_GP['isfirst']),
            'ishot'       => intval($_GP['ishot']),
            'isjingping'  => intval($_GP['isjingping']),
            'issendfree'  => intval($_GP['issendfree']),
            'isdiscount' => intval($_GP['isdiscount']),
            'isrecommand' => intval($_GP['isrecommand']),
            'istime' => intval($_GP['istime']),
            'timestart' => strtotime($_GP['timestart']),
            'industry_p2_id' => intval($_GP['industry_p2_id']),
            'timeend' => strtotime($_GP['timeend'])
        );
        
        // 开始检查货号
        if ( empty($_GP['id']) ){
            $sn = mysqld_select("SELECT * FROM ".table('shop_goods')." WHERE goodssn = :goodssn ",array(":goodssn"=>$_GP['goodssn']));
            $sn && message('货号已存在，请检查');
        }
        //删除因为加入权限不可见后，一些字段没有对应数据则删除  产品这边可以这么去除，宝贝那边不能这么处理
        foreach($data as $key => $val){
            if($val === null)  unset($data[$key]);
        }

        if (! empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['thumb'] = $upload['path'];
        }
        
        if (empty($id)) {
            mysqld_insert('shop_goods', $data);
            $id = mysqld_insertid();
        } else {
            unset($data['createtime']);
            mysqld_update('shop_goods', $data, array('id' => $id));
        }

        $goodsService  = new \service\shop\goodsService();
        //添加产品的时候 加入图片
        $goodsService->actGoodsPicture($id,$_GP);
        // 处理商品规格 以及属性
        $goodsService->actGoodsAttr($id,$_GP['attritem']);
        $goodsService->actGoodsSpec($id,$_GP['specitem']);

        message('商品操作成功！', web_url('goods', array(
            'op' => 'post_product',
            'id' => $id
        )), 'success');

    }
    public function delete()
    {
        $_GP = $this->request;
        $id = intval($_GP['id']);
        $row = mysqld_select("SELECT id, thumb FROM " . table('shop_goods') . " WHERE id = :id", array(
            ':id' => $id
        ));
        if (empty($row)) {
            message('抱歉，商品不存在或是已经被删除！');
        }
        // 修改成不直接删除，而设置deleted=1
        mysqld_update("shop_goods", array(
            "deleted" => 1
        ), array(
            'id' => $id
        ));

        message('删除成功！', 'refresh', 'success');
    }


    public function csv_post()
    {
        set_time_limit(1800000) ;
        setlocale(LC_ALL, 'zh_CN');
        $_GP = $this->request;
        if( !empty($_FILES['csv']["name"]) ){
            if ( $_FILES["csv"]["size"] < 10240000 ) {
                $csvreader = new CsvReader($_FILES["csv"]["tmp_name"]);
                $line_number = $csvreader->get_lines();
                $arrobj = new arrayiconv();
                $rows = ceil($line_number / 20);
                $num = 0;
                for ( $i = 0; $i < $rows; $i++ ){
                    $arr = $csvreader->get_data(20,$i*20+1);
                    $arr = $arrobj->Conversion($arr,"GBK","utf-8");
                    if ($i == 0){
                        array_shift($arr);
                    }
                    $this->c_goods($arr);
                }
            }else{
                message('文件过大,请控制在1MB', '', 'error');
            }
        }
        include page('goods/csv_goods');
    }

    public function c_goods($array=array())
    {
        if ( !empty($array) ){
            foreach ( $array as $key=>$value ){
                // 组织CSV [0] => 产品名 [1] => 产品分类 [2] => 产品详情 [3] => 产品主图  [4] 价格
                $goods_name      =   trim($value[0]);
                $category          =   trim($value[1]);
                $desc              =   $value[2];
                $img_name         =   trim($value[3]);
                $pirce              =   trim($value[4]);
                if ( empty($img_name) or empty($goods_name) or empty($category) ){
                    continue;
                }
                // 先确认产品是否存在,存在则继续下一个
                $check = mysqld_select("SELECT * FROM " . table('shop_goods') . " WHERE title = '$goods_name' ");
                if ( !$check ){
                    // 开始处理图片，如果图片处理不了，则退出
                    //下载远程图片到本地
                    $imginfo=GrabImage($img_name);
                    if ( !$imginfo ){
                        continue;
                    }
                    if (empty($imginfo)||!isset($imginfo)) {
                        $imginfo='images/nopic_big.gif';
                    }
                    //将详情页的图片下载到本地并替换图片URL
                    //1 . 将详情页的代码全部小写化
                    //2.  正则取出图片

                    $pattern = "/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg|.png]))['|\"].*?[\/]?>/";
                    preg_match_all($pattern,$desc,$match);
                    if ( !empty($match[1]) ){
                        //3.  通过图片下载函数下载图片并返回路劲
                        foreach($match[1] as $key=>$d_img){
                            $desc_img = GrabImage($d_img);
                            if ( ! $desc_img ){
                                continue;
                            }
                            $desc = str_replace( $match[1][$key] , $desc_img , $desc);
                        }
                    }
                    // 开始根据分类名来获取，分类ID
                    $ccate2 = mysqld_select("SELECT id,parentid FROM " . table('shop_category') . " WHERE name = '$category' ");
                    //Array ( [id] => 1202 [parentid] => 301 )
                    $pcate =  mysqld_select("SELECT parentid FROM " . table('shop_category') . " WHERE id = '$ccate2[parentid]' ");
                    $pcate = $pcate['parentid'];
                    $data = array(
                        'pcate' => $pcate,
                        'ccate' => $ccate2['parentid'],
                        'ccate2' => $ccate2['id'],
                        'type' => 0,
                        'marketprice'=>$pirce,
                        'thumb'=>$imginfo,
                        'status' => 1,
                        'title' => $goods_name,
                        'content' => htmlspecialchars_decode($desc),
                        'total' => 99,
                        'createtime' => TIMESTAMP,
                    );
                    mysqld_insert('shop_goods', $data);
                }
                //如果不存在则插入数据
            }
        }
    }
}