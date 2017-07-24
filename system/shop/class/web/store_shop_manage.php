<?php

/**
 * author: 王敬
 */

namespace shop\controller;

use common\controller\basecontroller;

class store_shop_manage extends basecontroller {

    //该参数必须定义，他已经接受了 get 与 post的值。尽量不要用get post
    //因为get post在init初始化的时候 对一些数据做过处理了 比如空值 或者后期可以加入对初始化时安全验证
    protected $info_status_text = array('2' => '审核中', '0' => '已认证', '1' => '填写资料中', 12=>'填写资料中','3' => '审核不通过');
    private $memberData;
    private $shopPic;
    private $ShopBrand;
    private $goodsTypeGroup;
    private $goodstype;
    private $goods;
    private $shopdish;
    private $shopltc;
    
    function __construct() {
        parent::__construct();
        $this->memberData           = get_member_account();
        $this->shopPic              = new \service\seller\ShopPicService();     //宝贝图片
        $this->ShopBrand            = new \service\seller\ShopBrandService();     //品牌
        $this->goodsTypeGroup       = new \service\seller\goodsTypeGroupService();    //分组操作对象
        $this->goodstype            = new \service\seller\goodstypeService();    //规格操作对象
        $this->goods                = new \service\seller\goodsService();     //
        $this->shopdish             = new \service\seller\ShopDishService();
        $this->shopltc              = new \service\seller\limitedTimepurChaseService();  //限时购
        $this->industry             = new \service\shop\IndustryService();  //限时购
        $this->shopcate             = new \service\seller\ShopCateService();  //限时购
        $this->shopRegion           = new \service\seller\regionService();
        $this->shopStore            = new \service\seller\shopStoreService();
   }
    
    public function ltcExamineSuccess(){
        $_GP = $this->request;
        $psize = 15;
        $pindex = max(1, intval($_GP["page"]));
        $limit = ' limit ' . ($pindex - 1) * $psize . ',' . $psize;

        $sql = "select * from ".table('activity_dish')." where ac_dish_status = 1";
        
        $list = mysqld_selectall($sql);
        
        foreach($list as $k=>$v){
            //行业
            $industry = $this->industry->getIndustryInfo($v['ac_in_id'],'gc_name');
            $list[$k]['gc_name'] = $industry['gc_name'];
            
            //分类1
            $cate1 = $this->shopcate->shopCategoryInfo($v['ac_p1_id'],'name');
            $list[$k]['cate1_name'] = $cate1['name'];

            //分类2
            $cate2 = $this->shopcate->shopCategoryInfo($v['ac_p2_id'],'name');
            $list[$k]['cate2_name'] = $cate2['name'];
            
            //城市
            $city = $this->shopRegion->getRegionInfo($v['ac_city']);
            $list[$k]['city'] = $city['region_name'];
            
            //城市区域
            $city_area = $this->shopRegion->getRegionInfo($v['ac_city_area']);
            $list[$k]['city_area'] = $city_area['region_name'];
            
            //宝贝名称
            $dish = $this->shopdish->getDishInfo($v['ac_shop_dish']);
            $list[$k]['dish_title'] = $dish['title'];
            
            //店铺名称
            $storeShop = $this->shopStore->getStoreShopInfo($v['ac_shop']);
            $list[$k]['store_shop'] = $storeShop['sts_name'];
        }
        
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM '.table('activity_dish')." where ac_dish_status = 0");
        $pager = pagination($total, $pindex, $psize);
        include page('check_store/ltc_dish_list_success');
    }
    
    
    public function ltcExamine(){
        $_GP = $this->request;
        $psize = 15;
        $pindex = max(1, intval($_GP["page"]));
        $limit = ' limit ' . ($pindex - 1) * $psize . ',' . $psize;

        $sql = "select * from ".table('activity_dish')." where ac_dish_status = 1";
        
        $list = mysqld_selectall($sql);
        
        foreach($list as $k=>$v){
            //行业
            $industry = $this->industry->getIndustryInfo($v['ac_in_id'],'gc_name');
            $list[$k]['gc_name'] = $industry['gc_name'];
            
            //分类1
            $cate1 = $this->shopcate->shopCategoryInfo($v['ac_p1_id'],'name');
            $list[$k]['cate1_name'] = $cate1['name'];

            //分类2
            $cate2 = $this->shopcate->shopCategoryInfo($v['ac_p2_id'],'name');
            $list[$k]['cate2_name'] = $cate2['name'];
            
            //城市
            $city = $this->shopRegion->getRegionInfo($v['ac_city']);
            $list[$k]['city'] = $city['region_name'];
            
            //城市区域
            $city_area = $this->shopRegion->getRegionInfo($v['ac_city_area']);
            $list[$k]['city_area'] = $city_area['region_name'];
            
            //宝贝名称
            $dish = $this->shopdish->getDishInfo($v['ac_shop_dish']);
            $list[$k]['dish_title'] = $dish['title'];
            
            //店铺名称
            $storeShop = $this->shopStore->getStoreShopInfo($v['ac_shop']);
            $list[$k]['store_shop'] = $storeShop['sts_name'];
        }
        
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM '.table('activity_dish')." where ac_dish_status = 0");
        $pager = pagination($total, $pindex, $psize);
        include page('check_store/ltc_dish_list');
    }
    
    //状态变更
    public function changeStatus(){
        $_GP = $this->request;
        if($_GP['ac_dish_id'] <= 0)echo -1;
        $dish_status = $this->shopltc->changeActivityDish($_GP['ac_dish_id'],$_GP['ac_dish_status']);
        
        echo 1;
        exit;
    }
    
    public function index() {
        $_GP = $this->request;
        $storeType = array(1=>'交收商铺','2'=>'集团大客户','3'=>'合作客户');
        $psize = 15;
        $pindex = max(1, intval($_GP["page"]));
        $limit = ' limit ' . ($pindex - 1) * $psize . ',' . $psize;
        $where = '';
        
        if($_GP['invitation_code'] != '')
        {
            $where = " where invitation_code = '{$_GP['invitation_code']}'";
        }
        
        $isAgentAdmin = isAgentAdmin();
        if($isAgentAdmin){
            $admin_mobile = $_SESSION['account']['mobile'];
            $where = " where invitation_code = '{$admin_mobile}'";
        }
        else{
            //获取业务员组ID
            $rolers = mysqld_select("select id from ".table('rolers')." where isdelete=0 and type=1");
            
            //获取业务员列表
            $sqlAgenAdmin = "select uid from ". table('rolers_relation')." where rolers_id = {$rolers['id']}";
            $rsAgentAdmin  = mysqld_selectall($sqlAgenAdmin);
            
            $uidStr = '';
            foreach($rsAgentAdmin as $k=>$v){
                $uidStr .= $v['uid'].',';
            }
            $uidStr = rtrim($uidStr, ',');
            
            //获取用户信息
            $sqlAgentData = "select mobile,nickname from ". table('user')." where id in ($uidStr)";
            $rsAgentData  = mysqld_selectall($sqlAgentData);
        }
        
        $sql = "SELECT * FROM " . table('store_shop') . " as store_shop "
            . " LEFT JOIN " . table('store_shop_identity') . " on ssi_id=sts_id  {$where}  "
            . "order by sts_info_status desc, sts_id desc {$limit}";
        $list = mysqld_selectall($sql);
        
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('store_shop') . " as a $where");

//         ppd("SELECT * FROM " . table('store_shop') . "  where {$where} order by sts_id desc {$limit}");
        $pager = pagination($total, $pindex, $psize);
        include page('check_store/store_shop_manage_list');
    }

    public function apply()
    {
        $_GP = $this->request;
        $storeType = array(1=>'交收商铺','2'=>'集团大客户','3'=>'合作客户');

        $psize = 15;
        $pindex = max(1, intval($_GP["page"]));
        $limit = ' limit ' . ($pindex - 1) * $psize . ',' . $psize;

        $sql = "SELECT * FROM " . table('store_shop_apply') . " as store_shop "
            . " LEFT JOIN " . table('store_shop_identity_apply') . " on ssi_id=sts_id    "
            . " order by sts_info_status desc, sts_id desc {$limit}";

        $list = mysqld_selectall($sql);

        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('store_shop_apply') . " as a");
        $pager = pagination($total, $pindex, $psize);
        include page('check_store/store_shop_apply');
    }

    public function dialog() {
        //审核不通过弹出框
        $_GP = $this->request;
        !$_GP['id'] && ajaxReturnData(0, "ID不能为空");
        
        $info = mysqld_select ( "SELECT * FROM " . table ( 'store_shop_identity' ) . " where ssi_id=".intval($_GP ['id']) );
        include page('check_store/store_shop_manage_fail');
    }
    public function dialog2() {
        //审核通过弹出框
        $_GP = $this->request;
        !$_GP['id'] && ajaxReturnData(0, "ID不能为空");
         
        $typeText =array('1'=>'区代理','2'=>'市代理','3'=>'省代理');       //1区代理     2市代理     3省代理
        $result = mysqld_selectall ( "SELECT * FROM " . table ( 'store_shop_level' ) . " order by level_type asc" );
        
        include page('check_store/store_shop_manage_success');
    }

    public function shenhe() {
        $_GP = $this->request;
        #1 数据校验
        !$_GP['id'] && ajaxReturnData(0, "ID不能为空");
        !isset($_GP['sts_info_status']) && intval($_GP['sts_info_status'])<0 && ajaxReturnData(0, "审核是否通过值缺少");
        #2.1 组织store_shop数据
        $store_shop_update_data['sts_info_status'] = $_GP['sts_info_status'];
        $store_shop_update_data['fail_reason']     = trim($_GP['ssi_shenhe_beizhu']);

        if( $_GP['level_type'] >0 ){//审核动作
            $is_success = true;   //审核通过
            $info = mysqld_select ( "SELECT time_range FROM " . table ( 'store_shop_level' ) . " where rank_level=".intval($_GP ['level_type']) );
            $store_shop_update_data['sts_shop_level']       = $_GP['level_type'];
            $store_shop_update_data['sts_level_valid_time'] = strtotime("+".$info['time_range']." year");//防止閏年等問題
            $store_shop_update_data['sts_shenhe_time']      = time();
        }else{
            $is_success = false;   //审核失败
        }

        $res = mysqld_update('store_shop_apply',$store_shop_update_data,array('sts_id'=>$_GP['id']));
        if(!$res){
            ajaxReturnData(0,'操作失败，待会再试');
        }
        if($is_success){
            //从申请表的记录 挪到主表去
            $store_apply  = mysqld_select("select * from ".table('store_shop_apply')." where sts_id={$_GP['id']}");
            $identy_apply = mysqld_select("select * from ".table('store_shop_identity_apply')." where ssi_id={$_GP['id']}");

            $store_data   = $store_apply;
            unset($store_data['sts_id']);
            unset($store_data['fail_reason']);
            mysqld_insert('store_shop',$store_data);
            if($store_id = mysqld_insertid()){
                if(!empty($identy_apply)){
                    $identy_apply['ssi_id'] = $store_id;
                    mysqld_insert('store_shop_identity',$identy_apply);
                }

                //更新当前会员类型为商家类型
                mysqld_update("member",array('member_type'=>2),array('openid'=>$store_apply['sts_openid']));

                //插入当前用户跟 店铺的关系
                $relat_data = array(
                    'sts_id' => $store_id,
                    'openid' => $store_apply['sts_openid'],
                    'is_admin'   => 1,
                    'createtime' => time(),
                );
                //查找是否 关系表 有其他表 有的话，该店铺不设置为默认，没有的话，该店铺设置为默认。
                $find = mysqld_select ( "SELECT * FROM " . table ( 'member_store_relation' ) . " where  openid=".intval($store_apply['sts_openid']) );
                if(empty($find)){
                    $relat_data['is_default'] = 1;
                }
                mysqld_insert('member_store_relation',$relat_data);

                //插入商铺的扩展信息表
                mysqld_insert('store_extend_info',array('store_id'=>$store_id,'createtime'=>time()));

                //删除掉  申请表里的记录
                mysqld_delete('store_shop_apply',array('sts_id'=>$_GP['id']));
                mysqld_delete('store_shop_identity_apply',array('ssi_id'=>$_GP['id']));
            }else{
                //本次操作 还原回去
                mysqld_update('store_shop_apply',array('sts_info_status'=>2),array('sts_id'=>$_GP['id']));
                ajaxReturnData(0,'操作失败，待会再试');
            }
        }

        ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS'));
    }

    /**
     * 审核模型
     */
    public function check_gtype(){
        
        #1.2 输出other数据  ------- 行业下的细致分类
        $dssssql = "SELECT *  FROM " . table ( 'shop_category' );
        $scate_result = mysqld_selectall( $dssssql );
        foreach ($scate_result as $key => $value) {
            if($value['parentid']==0){
                $n2_Scate_result[$value['industry_p2_id']][]=  $value;
            }else{
                $shop_cate[$value['parentid']][]=  $value;
            }
        }
        #1.3 输出other数据  ------- 输出后台分组数据
        $dsssssql = "SELECT *  FROM " . table ( 'goods_type_group' )." where store_id =0 and status=1 ";
        $group_data = mysqld_selectall( $dsssssql );
        
        
        $_GP = $this->request;
        $_GP['tab']= 1;
         #2.1 查询产品数据
        $swhere  =' where system_group_id = 0';//大于0说明是后台的
//        $_GP['cate_2'] &&  $swhere .= ' and sts_category_p2_id ='. intval( $_GP['cate_2'] );
//        $_GP['cate_3'] &&  $swhere .= ' and A.sts_id ='. intval( $_GP['cate_3'] );
        
        $psize =  10;
        $pindex = max(1, intval($_GP["page"]));
        $limit = ' limit '.($pindex-1)*$psize.','.$psize;
        
        $field= '*';
        $d_sql = "SELECT ".$field." FROM " . table ( 'goods_type' )." A"
            . $swhere." order by id ";

        $rdata = mysqld_selectall( $d_sql . $limit );
        
        $GoodsTypeservice = new \service\seller\goodstypeService();
       
        foreach ($rdata as $key=>$value) {
            $value['attr'] = $GoodsTypeservice->getSpecAndItemByGtypeid(  $value['id'] );
            $tab1_result [$value['id']] = $value;
        }
        $total  = mysqld_selectcolumn( str_replace($field,"count(id)",$d_sql) );//用count代替field
        $pager  = pagination($total, $pindex, $psize);
        
        include page('check_store/store_shop_manage_template');
    }

    /**
     * 审核产品
     */
    public function check_shop(){
        $_GP = $this->request;
        //获取第一级行业信息
        $indu_parent = mysqld_selectall("select gc_id,gc_name from ".table('industry')." where gc_pid=0");
        $indu_second  = array();
        if(!empty($_GP['indu_p1'])){
            $indu_second =  mysqld_selectall("select gc_id,gc_name from ".table('industry')." where gc_pid={$_GP['indu_p1']}");
        }

        $store_instry = array();
        $where = "1=1";
        if(!empty($_GP['sts_id'])){
            $where .= " and sts_id=".$_GP['sts_id'];
            $store_instry = mysqld_selectall("select sts_id,sts_name from ".table('store_shop')." where sts_category_p1_id={$_GP['indu_p1']} and  sts_category_p2_id={$_GP['indu_p2']}");
        }
        if($_GP['indu_p2'] > 0){
            $where .=  " and industry_p2_id = {$_GP['indu_p2']}";
        }
        
        $psize =  15;
        $pindex = max(1, intval($_GP["page"]));
        $limit = ' limit '.($pindex-1)*$psize.','.$psize;
        
        $field= '*';
        $d_sql = "SELECT ".$field." FROM " . table ( 'shop_dish' )." where {$where} and gid = 0 and is_already_in_shop=0 and is_contentimg = 1";
   
        $result = mysqld_selectall($d_sql.$limit);
        foreach($result as &$item){
            //获取店铺名，格式化金额
            $temp_store = member_store_getById($item['sts_id'],'sts_name,sts_category_p2_id');
            $item['store_name']           = $temp_store['sts_name'];
            $item['sts_category_p2_id']   = $temp_store['sts_category_p2_id'];
            $item['marketprice']  = FormatMoney($item['marketprice'],0);
            $item['productprice'] = FormatMoney($item['productprice'],0);
        }
        $total  = mysqld_selectcolumn( str_replace($field,"count(id)",$d_sql) );//用count代替field
        $pager  = pagination($total, $pindex, $psize);

        include page('check_store/check_shop');
    }

    /**
     * 更具第一级行业 获取第二级
     */
    public function ajaxGet_indulist(){
        $_GP = $this->request;
        $indu_list =  mysqld_selectall("select gc_id,gc_name from ".table('industry')." where gc_pid={$_GP['indu_p1']}");
        if(empty($indu_list)){
            ajaxReturnData(0,'没有对应的二级！');
        }
        ajaxReturnData(1,'',$indu_list);
    }

    /**
     * 更具第二级行业 获取商铺
     */
    public function ajaxGet_shop()
    {
        $_GP = $this->request;
        $indu_list =  mysqld_selectall("select sts_id,sts_name from ".table('store_shop')." where sts_category_p2_id={$_GP['indu_p2']}");
        if(empty($indu_list)){
            ajaxReturnData(0,'没有对应的商铺！');
        }
        ajaxReturnData(1,'',$indu_list);
    }

    /**
     * 更具第二级行业 获取一级分类
     */
    public function ajaxGet_cate()
    {
        $_GP = $this->request;
        $indu_list =  getParentCategoryByIndustry(0,$_GP['indu_p2']);
        if(empty($indu_list)){
            ajaxReturnData(0,'没有对应的商铺！');
        }
        ajaxReturnData(1,'',$indu_list);
    }
    /**
     * 更具第1级分类 获取2级分类
     */
    public function ajaxGet_cate2()
    {
        $_GP = $this->request;
        $indu_list =  getCategoryByParentid($_GP['cate_p1']);
        if(empty($indu_list)){
            ajaxReturnData(0,'没有的分类！');
        }
        ajaxReturnData(1,'',$indu_list);
    }

    /**
     * 常规配置
     */
    public function general(){
        $_GP = $this->request;
        if ($_GP['submit']){
            if($_GP['pay_rate'] > 10){
                message('支付费率过高！');
            }
            if(!is_numeric($_GP['draw_money'])){
                message('手续费必须是数字！');
            }
            $data['comment_exchange'] = $_GP['comment_exchange'];
            $data['bid_exchange'] = $_GP['bid_exchange'];
            $data['order_num_exchange'] = $_GP['order_num_exchange'];
            $data['enter_exchange'] = $_GP['enter_exchange'];
            $data['lowst_draw_limit'] = FormatMoney($_GP['lowst_draw_limit'],1);
            $data['draw_money'] = FormatMoney($_GP['draw_money'],1);
            $data['pay_rate']   = $_GP['pay_rate'];
            refreshSetting($data);
            message('保存成功', 'refresh', 'success');
        }
        $settings=globaSetting();
        include page('general');
    }
        
    public function getDishSpec() {
        $_GP = $this->request;
//        !$_GP['dish_id'] && ajaxReturnData(0, "dish_id不能为空");
        !$_GP['gtype_id'] && ajaxReturnData(0, "此商品未绑定规格gtype_id");
        
        $GoodsTypeservice = new \service\seller\goodstypeService();
        $result = $GoodsTypeservice->getSpecAndItemByGtypeid( $_GP['gtype_id']);
        
        ajaxReturnData(1, '操作成功',array('spec'=>$result));
    }


    /**
     * 加入产品库
     */
    public function postGoodsTypeToGroup() {
        $_GP = $this->request;
        $gservice = new \service\shop\goodsService();
        $_GP['types'] = intval($_GP['types']);

        if($_GP['types'] == 1)
        {
            !$_GP['dish_id'] && ajaxReturnData(0, "dish_id不能为空");
            $effect = $gservice->addDishToShopGoods($_GP['dish_id'],array('p1'=>$_GP['p1'], 'p2'    => $_GP['p2']));
        }
        else{
            !$_GP['dish_ids'] && ajaxReturnData(0, "dish_ids不能为空");
            $_GP['dish_ids'] = rtrim($_GP['dish_ids'],',');
            $effect = $gservice->addsDishToShopGoods($_GP['dish_ids'],array('p1'=>$_GP['p1'],'p2' => $_GP['p2']));
        }
        
        if($effect> 0){
            ajaxReturnData(1, "操作成功");
        }else{
            ajaxReturnData(0, $gservice->getError());
        }
    }
    
    public function postTypeToGroup() {
        $_GP = $this->request;
        
        #2.1 组织store_shop数据
        !$_GP['p2'] && ajaxReturnData(0, "分类不能为空");
        !$_GP['system_group_id'] && ajaxReturnData(0, "分组不能为空");
        
        $data = array(
            'p1'    => $_GP['p1'], 'p2'    => $_GP['p2'],
            'system_group_id'  => $_GP['system_group_id'],
        );
        $effect=    mysqld_update('goods_type',$data, array('id' => $_GP['id']));
            
        if( $effect!==false ){
            ajaxReturnData(1, "操作成功");
        }else{
            ajaxReturnData(0, "操作失败");
        }
       
        
    }

    //获取宝贝库
    /*
     * <div class="alertModal-dialog" style="width:45%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">模态框（Modal）标题</h4>
        </div>
        <div class="modal-body">在这里添加一些文本</div>
        <div class="modal-footer">
             <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <button type="button" class="btn btn-primary">提交更改</button>
        </div>
     </div>
    */
    public function getDishContent(){
        $_GP = $this->request;
        //dish_id
        $dservice    = new \service\seller\ShopDishService();
        $dishContent = $dservice->getDishContent($_GP);
        
        //获取宝贝图片
        $dservice                 = new \service\seller\ShopPicService();
        $picData                  = $dservice->getDishPic($_GP['dish_id']);
        
        $picData['picurl']        = explode(',',$picData['picurl']);
        $picData['contentpicurl'] = explode(',',$picData['contentpicurl']);
        
        include page('check_store/dish_content');
    }

    public function getQrcode()
    {
        $_GP = $this->request;
        if(empty($_GP['openid'])){
            ajaxReturnData(0,'参数有误！');
        }
        $weixin = new \WeixinTool();
        $result = $weixin->get_xcx_erweima($_GP['openid'],2);
        if($result['errno'] == 0){
            ajaxReturnData(0,$result['message']);
        }else{
            ajaxReturnData(1,'请求成功！',$result['message']);
        }
    }
    
    public function shopProhibit(){
        $_GP = $this->request;

        if(empty($_GP['openid'])){
            ajaxReturnData(0,'参数有误！');
        }
        $_GP['is_ban'] = intval($_GP['is_ban'])==1?2:1;
        
        $data = array(
            'is_ban' => $_GP['is_ban']
        );

        $prohibit = mysqld_update('store_shop',$data, array('sts_openid' => $_GP['openid']));
        if($_GP['is_ban'] == 2){
            mysqld_delete('activity_dish',array('ac_shop'=>$_GP['sts_id']));
        }
        
        ajaxReturnData(1,'请求成功');
    }
    
}
