<?php
namespace seller\controller;
use  seller\controller;

class product extends base
{
    private $shopPic            = array();
    private $ShopBrand          = array();
    private $goodsTypeGroup     = array();
    private $goodstype          = array();
    private $goods              = array();
    public function __construct()
    {
        parent::__construct();
        
        $this->shopPic              = new \service\seller\ShopPicService();     //宝贝图片
        $this->ShopBrand            = new \service\seller\ShopBrandService();     //品牌
        $this->goodsTypeGroup       = new \service\seller\goodsTypeGroupService();    //分组操作对象
        $this->goodstype            = new \service\seller\goodstypeService();    //规格操作对象
        $this->goods                = new \service\seller\goodsService();     //
        $this->shopdish             = new \service\seller\ShopDishService();
        $this->shopltc             = new \service\seller\limitedTimepurChaseService();  //限时购
    }
    
	//发布商品
	public function postadd()
	{
            $_GP = $this->request;
            $isEdit = 0;
            $product = array();
            
            $goodService = new \service\seller\goodsService();
            
            if(!empty($_GP['do_add'])){
                if($_GP['xcimg'][0] == ''){
                     message("相册图片不能为空",refresh(),'error');
                }

                $dish_id = $goodService->addGoods($_GP);
                if($dish_id){
                    $url = mobile_url('product',array('op'=>'productlist','dish_id'=>$dish_id));
                    message("发布成功",$url,'success'); 
                }else{
                    message($goodService->getError(),refresh(),'error');
                }
            }
            if(!empty($_GP['goodid'])){
                //从产品库添加 
                $product = mysqld_select('select * from '.table('shop_goods')." where id={$_GP['goodid']}");
                unset($product['id']);
                $contentArr = changeWebImgToAli($product['content']);
                
                 foreach($contentArr['img'] as $k=>$v)
                {
                    $xqPicArr[$k]['filename'] = $v;
                }
                $xqImgJson = json_encode($xqPicArr);
                
                $product['content'] = $contentArr['content'];
                
                //squdian_shop_goods_piclist 
                $goodPicData = $goodService->getGoodsPic($_GP, '*');
                
                if($goodPicData['picurl'] != ''){
                    $picArr  = explode(',',$goodPicData['picurl']);

                    foreach($picArr as $k=>$v)
                    {
                        $xcPicArr[$k]['filename'] = $v;
                    }
                    $picJson = json_encode($xcPicArr);
                }
                //通过品牌ID获取品牌名称和ID $product['brand']
                $brandData = '';
                $brandData['ids'] = intval($product['brand']);
                $brand = $this->ShopBrand->getBrandTitle($brandData);
                $brand = $brand[0];
                unset($brand[0]);
                $isEdit = 1;
            }
            //统计店铺首页推荐数量
            $indexCount = $this->shopdish->getIndexDishCount();
            
            include page('dish/postadd');
	}

	//编辑产品
	public function editproduct()
	{
		$_GP = $this->request;
                $xcPicArr = array();
		if(empty($_GP['dish_id'])){
			message('参数有误！',refresh(),'error');
		}
                $isEdit = 1;
		if(!empty($_GP['do_add'])){
                    if(count($_GP['xcimg']) <= 0)
                    {
                        $url = mobile_url('product',array('op'=>'editproduct','dish_id'=>$dish_id));
                        message("相册图片不能为空",$url,'error');
                    }
                    $goodService = new \service\seller\goodsService();
                    $dish_id = $goodService->addGoods($_GP);
                    if($dish_id){
                        $url = mobile_url('product',array('op'=>'editproduct','dish_id'=>$dish_id));
                        message("发布成功",$url,'success');
                    }else{
                        message($goodService->getError(),refresh(),'error');
                    }
		}

		$memInfo    = get_member_account();
		$product    = mysqld_select("select * from ".table('shop_dish')." where id={$_GP['dish_id']}");
                $product['dish_id'] = $product['id'];
		if(empty($product) || $product['sts_id'] != $memInfo['store_sts_id']){
			message('商品不存在！',refresh(),'error');
		}
		$shop_cate1 = mysqld_select("select id,name from ".table('store_shop_category')." where id={$product['store_p1']}");
		$shop_cate2 = mysqld_select("select id,name from ".table('store_shop_category')." where id={$product['store_p2']}");
		//获取规格
		if(!empty($product['gtype_id'])){
			//如果有规格说明有 规格值
			$goodService = new \service\seller\goodsService();
			$spec_result = $goodService->getSpecInfoOnEdit($product['id']);
			if(empty($spec_result['spec_info'])){
				$spec_info_jsonstring = '';
			}else{
				$spec_info_jsonstring = json_encode_ex($spec_result['spec_info']);
			}
			if(empty($spec_result['item_arr'])){
				$item_info_jsonstring = '';
			}else{
				$item_info_jsonstring = json_encode_ex($spec_result['item_arr']);
			}
			//获取模型名
			$edit_gtype = mysqld_select("select name from ".table('goods_type')." where id={$product['gtype_id']}");
		}else{
			$spec_info_jsonstring = '';
			$item_info_jsonstring = '';
			$edit_gtype = '';
		}
                
                
                //通过品牌ID获取品牌名称和ID $product['brand']
                $brandData = '';
                $brandData['ids'] = intval($product['brand']);
                $brand = $this->ShopBrand->getBrandTitle($brandData);
                $brand = $brand[0];
                unset($brand[0]);
                
                //获取宝贝图片
                $picData = $this->shopPic->getDishPic($_GP['dish_id'], '*');
                
                if($picData['id'] > 0)
                {
                    $picArr  = explode(',',$picData['picurl']);
                    $contentpicurlArr  = explode(',',$picData['contentpicurl']);
                    
                    foreach($contentpicurlArr as $k=>$v)
                    {
                        $xqPicArr[$k]['filename'] = $v;
                    }
                    $xqImgJson = json_encode($xqPicArr);
                    
                    foreach($picArr as $k=>$v)
                    {
                        $xcPicArr[$k]['filename'] = $v;
                    }
                    $picJson = json_encode($xcPicArr);
                }else{
                    $picJson = '';
                    $xqImgJson = '';
                }
                
                //统计店铺首页推荐数量
                $indexCount = $this->shopdish->getIndexDishCount();
                
                include page('dish/postadd');
	}

	//ajax 提交添加模型和规格
	public function addGoodsSpec()
	{
		$_GP = $this->request;
		$gtypeService = new \service\seller\goodstypeService();
		$respecitemId = $gtypeService->addspecAndItemOnAddGood($_GP);

		if(!$respecitemId){
                    ajaxReturnData(0,$gtypeService->getError());
		}
		ajaxReturnData(1,'操作成功',$respecitemId);
	}

        
	//选择分类
	public function choose_type()
	{
		$_GP = $this->request;
		$storyShopClass = array();

		$memberData = get_member_account();
		$category = mysqld_selectall("SELECT id,name,pid as parentid  FROM " . table('store_shop_category') . "  where store_shop_id = {$memberData['store_sts_id']} ORDER BY id ASC");
		shopCategoryTree($storyShopClass,$category);
		include page('dish/choosetype');
	}
	//选择规格 可以带入 模型id
	public function choose_spec()
	{
            $_GP = $this->request;
            $gtypeService = new \service\seller\goodstypeService();
            //获得店铺分组信息
            $groupArr      = $gtypeService->getGtypeGroups();

            $group_id      = '';
            $gtype_info    = array();
            $speclist_data = array();
            $self_ku       = 1;  //初始时不为0
            if(!empty($_GP['gtype_id'])){
                //获取当前分组
                $gtypedata  = mysqld_select("select group_id,store_id from ".table('goods_type')." where id={$_GP['gtype_id']}");
                $group_id   = $gtypedata['group_id'];
                $self_ku    = $gtypedata['store_id'];   //系统的是0  不是系统的 非0
                //获取 该分组下的所有模型
                $filed = "id,name,p1,p2,group_id";
  
                $gtype_info = mysqld_selectall("select {$filed} from ".table('goods_type')." where group_id={$group_id} and status=1");

                //获取该模型下的商品规格 和对应的项
                $speclist_data = $gtypeService->getSpecAndItemByGtypeid($_GP['gtype_id'],$_GP['dish_id']);
            }



            include page('dish/choosespec');
	}

	//从产品库选择
	public function choose_good()
	{
		//根据用户行业获取 分类
		$member = get_member_account();
		$industry_p2 = $member['sts_category_p2_id'];
		$category = getParentCategoryByIndustry('',$industry_p2);
		include page('dish/choosegood');
	}
	/**
	 * 通过ajax 获取某个组下的模型
	 */
	public function ajaxGtypeBygroupid()
	{
		$_GP = $this->request;
		if(empty($_GP['group_id'])){
			ajaxReturnData(0,'参数有误！');
		}
		//获取 该分组下的所有模型
		$gtypeService = new \service\seller\goodstypeService();
		$gtype_info   = $gtypeService->getGtypelistsByoneGroup($_GP['group_id']);
		if(empty($gtype_info))
                {
                    //ajaxReturnData(0,'无对应的分组',$gtype_info);
                }
		else{
                    ajaxReturnData(1,'获取成功',$gtype_info);
                }
	}

	//发布商品的时候 进行保存分类
	public function savecate()
	{
		$_GP = $this->request;
		$cateService = new \service\seller\ShopCategoryService();
		$res = $cateService->formValidateBeforeAddCate($_GP);
		if(!$res){
			ajaxReturnData(0,  $cateService->getError());
		}

		$cate_id = $cateService->do_addCate($_GP);
		if(!$cate_id){
			ajaxReturnData(0,  $cateService->getError());
		}else{
			ajaxReturnData(1,  $cate_id);
		}
	}


	//商品列表
	public function productlist()
	{
            $_GP = $this->request;

            $memberData = get_member_account();

            //获取一级店铺商品分类
            $dishCategoryFields = 'id,`name`';
            $dishCategoryData    = getStoreCategoryAllparent($memberData['store_sts_id'],$dishCategoryFields);

            $condition = '';

            if($_GP['oneCategory'] > 0 )
            {
                    $condition .= ' and store_p1 = '.$_GP['oneCategory'].'';
            }
            
            
            if($_GP['is_ltc'] == 1){
                $condition .= ' and ac_dish_id > 0';
            }
            elseif($_GP['is_ltc'] == 2){
                $condition .= ' and ac_dish_id = 0';
            }

            if($_GP['twoCategory'] > 0 )
            {
                    $condition .= ' and store_p2 = '.$_GP['twoCategory'].'';
                    $storeCategory = getStoreCategory($_GP['twoCategory'],'pid');
                    $dishCategoryTwoFields = 'id,`name`';
                    $dishCategoryTwoData    = getStoreCategoryChild($storeCategory[0]['pid'],$dishCategoryFields);
            }
            if($_GP['status'] != null && $_GP['status']!='-1'){
                    $condition .= " and status=".$_GP['status'];
            }
            if($_GP['title'] != '')
            {
                    $condition .= ' and title like "%'.$_GP['title'].'%"';
            }

            $pindex = max(1, intval($_GP['page']));
            $psize = 10;
            $limit = " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            $fields = '*';
            $list  = mysqld_selectall("SELECT {$fields} FROM " . table('shop_dish') . " WHERE sts_id = {$memberData['store_sts_id']}  {$condition} ORDER BY sort asc,id DESC ".$limit);
            foreach($list as $k=>$v){
                $list[$k]['isnewc']       = $v['isnew']==0?1:0;
                $list[$k]['isrecommandc'] = $v['isrecommand']==0?1:0;
                $list[$k]['isstatusc']    = $v['status']==0?1:0;
                //判断已参与限时购活动 有的话，返回数组，没有返回空数组
                $active_info  = getDishIsOnActive($v['id']);
                
                if (!empty($active_info)){//同步库存
                    synchroscope_store_count($v['store_count'],$active_info['ac_dish_total'],$active_info['ac_dish_id']);
                }
                $list[$k]['active_info'] = $active_info;
            }
            $total = mysqld_selectcolumn('SELECT COUNT(id) FROM ' . table('shop_dish') . " WHERE sts_id = {$memberData['store_sts_id']}  {$condition}");
            
            $pager = pagination($total, $pindex, $psize);
            include page('dish/productlist');
	}


	//获取二级分类
	public function parCategory(){
            $_GP = $this->request;

            //获取二级店铺商品分类
            $dishCategoryFields = 'id,`name`';
            $dishCategoryData    = getStoreCategoryChild($_GP['pid'],$dishCategoryFields);

            echo json_encode($dishCategoryData);
            exit;
	}
        

	/***********************************************************/
	/***********************商品模型管理**************************/
	/***********************************************************/
	//商品模型管理
	public function goodstype()
	{
            $_GP = $this->request;
            $goodsService = new \service\seller\goodstypeService();
            $groups = $goodsService->getGtypeGroups();
            $selfgroup    = $groups['selfgroup'];
            $pingtaigroup = $groups['pingtaigroup'];

            $member = get_member_account();
            $selfData    = $selfgroup;
            $pingtaiData = $pingtaigroup;
            if(!empty($_GP['group_id'])) {
                    //如果有根据  某一个分组查询的话，只显示该分组的
                    if ($_GP['type'] == 'self') {
                            $selfData      = array();
                            $selfData[]    = array('group_id'=>$_GP['group_id'],'store_id'=>$member['store_sts_id']);
                    }else{
                            $pingtaiData   = array();
                            $pingtaiData[] = array('group_id'=>$_GP['group_id'],'store_id'=>0);
                    }
            }

            $selfgtype_list    = $goodsService->getGtypelists($selfData);
            $pingtaigtype_list = $goodsService->getGtypelists($pingtaiData);

            $this_selfgroup = $this_pingtaigroup = array();
            if(!empty($_GP['group_id'])){
                    if($_GP['type'] == 'self'){
                            $this_selfgroup    = mysqld_select("select group_id,group_name from ".table('goods_type_group')." where group_id={$_GP['group_id']}");
                    }else{
                            $this_pingtaigroup =  mysqld_select("select group_id,group_name from ".table('goods_type_group')." where group_id={$_GP['group_id']}");
                    }
            }
            include page('dish/goodstype');
	}

	//添加模型分组
	public function addgtype_group()
	{
            $_GP = $this->request;
            if(empty($_GP['group_name'])){
                    message(LANG('COMMON_NAME_NOTNULL','common','分类名'),refresh(),'error');
            }
            $member = get_member_account();
            $data   = array(
                    'store_id'    => $member['store_sts_id'],
                    'group_name'  => $_GP['group_name'],
                    'createtime'  => time(),
                    'modifytime'  => time()
            );
            mysqld_insert('goods_type_group',$data);
            if(mysqld_insertid()){
                    message(LANG('COMMON_OPERATION_SUCCESS'),refresh(),'success');
            }else{
                    message(LANG('COMMON_OPERATION_FAIL'),refresh(),'error');
            }
	}

	//添加修改商品 模型
	public function add_goodstype()
	{
		$_GP = $this->request;
		$goodsService = new \service\seller\goodstypeService();
		$groups       = $goodsService->getGtypeGroups();
		$selfgroup    = $groups['selfgroup'];
		$gtype        = array();
		if(!empty($_GP['id'])){
			$gtype = mysqld_select("select name,id,group_id from ".table('goods_type')." where id={$_GP['id']} and status=1");
		}
		include page('dish/add_goodstype');

	}

	//表单提交添加或修改商品模型
	public function do_addgtype()
	{
		$_GP = $this->request;
		$gtypeService = new \service\seller\goodstypeService();
		//创建模型前，先确认是否真的没有分组  没有则默认创建一个分组
		$group_id = $gtypeService->checkGroupBeforeAddtype($_GP);
		if(!$group_id){
			message($gtypeService->getError(),refresh(),'error');
		}

		//创建该模型  或者编辑
		$res = $gtypeService->add_goodstype($_GP,$group_id);
		if($res){
			message(LANG('COMMON_OPERATION_SUCCESS'),refresh(),'success');
		}else{
			message($gtypeService->getError(),refresh(),'error');
		}
	}
	//商品规格操作
	public function speclist()
	{
            $_GP = $this->request;
            if(empty($_GP['id'])){
                    message("参数有误!",refresh(),'error');
            }
            $member = get_member_account();
            //获取所有分分组
            $goodsService = new \service\seller\goodstypeService();
            $groups       = $goodsService->getGtypeGroups(3);
            $selfgroup    = $groups['selfgroup'];

            //获取当前的模型
            $current_gtype = mysqld_select("select id,name,group_id from ".table('goods_type')." where id={$_GP['id']} and store_id={$member['store_sts_id']}");
            if(empty($current_gtype)){
                    message(LANG('COMMON_NAME_NOTEXIST','common','该模型'),refresh(),'error');
            }
            //获取该组下面的所有模型
            $gtype_list    = mysqld_selectall("select id,name from ".table('goods_type')." where group_id={$current_gtype['group_id']} order by status desc");

            //获取当前模型下的 规格 以及规格项
            $specAndItem   = $goodsService->getSpecAndItemByGtypeid($current_gtype['id']);

            include page('dish/speclist_self');
	}

	//查看系统的规格
	public function showspec()
	{
            $_GP = $this->request;
            if(empty($_GP['id'])){
                    message("参数有误!",refresh(),'error');
            }
            
            //获取系统所有分分组
            $goodsService = new \service\seller\goodstypeService();
            $groups         = $goodsService->getGtypeGroups(2);
            $system_group    = $groups['pingtaigroup'];
            
            $goodsTypeData = $goodsService->getTemplateSpecItem($_GP['id']);

            include page('dish/speclist_pingtai');
	}
        
	//删除规格
	public function del_gtype()
	{
            $_GP = $this->request;
            $_GP['gtype_id'] = intval($_GP['id']);
            //ppd($_GP['id']);
            //删除 
            $delNum = $this->goodstype->del_true_gtype($_GP); 
            $delNum = 1;
            if($delNum > 0){
                //删除配置项 以 配置项对应的所有扩展项 
                //获取
                $specData = $this->goodstype->getSpecData(intval($_GP['id']),'spec_id');
                $spec_ids = '';
                foreach($specData as $k=>$v)
                {
                    $spec_ids .= $v['spec_id'].',';
                }
                $spec_ids = rtrim($spec_ids,',');
                
                if($spec_ids != '')
                {
                    $specItemData = $this->goodstype->getSpecItemDatas($spec_ids,'id');
                    $spec_item_ids = '';
                    foreach($specItemData as $k=>$v)
                    {
                        $spec_item_ids .= $v['id'].',';
                    }
                    $spec_item_ids = rtrim($spec_item_ids,',');
                }
                
                //获取使用这个模型的宝贝
                $dishIds = '';
                $dishData = $this->shopdish->getGtypeCount($_GP);
                foreach($dishData as $k=>$v)
                {
                    $dishIds .= $v['dish_id'].',';
                }
                $dishIds = rtrim($dishIds,',');
                
                //删除配置项 goodstype_spec
                if($spec_ids != '')
                {
                    //删除配置扩展项 goodstype_spec_item
                    $delSpecStatus = $this->goodstype->delSpec($spec_ids);
                    
                    //delSpecItem
                    if($spec_item_ids != '')
                    {
                        $delSpecItemStatus = $this->goodstype->delSpecItem($spec_item_ids);
                    }
                }
                
                if($dishIds != '')
                {
                    //删除宝贝价格 dish_spec_price
                    $delDishSpecPriceStatus = $this->goodstype->delSpecPrice($dishIds);
                }
            }
            
            message("删除成功",refresh(),'error');
	}

	/**
	 * 给规格对应属性设置 状态可见或者不可见
	 */
	public function setitem_status()
	{
            $_GP = $this->request;
            if(empty($_GP['item_id'])){
                ajaxReturnData(0,LANG('COMMON_PARAME_ERR'));
            }
            
            $member = get_member_account();
            /*
            $status = intval($_GP['status']);
            mysqld_update('goodstype_spec_item',array('status'=>$status),array('id'=>$_GP['item_id'],'store_id'=>$member['store_sts_id']));
             * 
             */
            
            
            
            ajaxReturnData(1,LANG('COMMON_OPERATION_SUCCESS'));
	}
        
        public function delete_completely(){
            $_GP = $this->request;
            error_reporting(E_ALL);
            if($_GP['item_id'] <= 0 || $_GP['gtype_id'] <= 0){
                 ajaxReturnData(0,'必要参数必须存在');
            }
            
            $shopPriceService = new \service\seller\shopPriceService();
            $prices         = $shopPriceService->deletePrice($_GP);
            
             ajaxReturnData(1,'删除成功');
        }
        
	/**
	 * 添加规格
	 */
	public function addspec()
	{
            $_GP = $this->request;
            $gtypeService = new \service\seller\goodstypeService();
            $last_id = $gtypeService->addspec($_GP);
            if($last_id){
                    ajaxReturnData(1,$last_id);
            }else{
                    ajaxReturnData(0,$gtypeService->getError());
            }
	}
	/**
	 * 添加规格具体的项
	 */
	public function addspecitem()
	{
            $_GP = $this->request;
            $gtypeService = new \service\seller\goodstypeService();
            $last_id = $gtypeService->addspecitem($_GP);
            if($last_id){
                    ajaxReturnData(1,$last_id);
            }else{
                    ajaxReturnData(0,$gtypeService->getError());
            }
	}
        
        
        //获取商品品牌
        public function searcgbrand(){
            $_GP = $this->request;
            $brandService = new \service\seller\ShopBrandService();
            
            $brand = $brandService->getBrandSearch($_GP);
            
            echo json_encode($brand);
            exit;
        }
        
        //isnew isrecommand
        public function changeDishRecommand(){
            $_GP = $this->request;
            
            //changeDishRecommand
            $dishRecommand = new \service\seller\ShopDishService();
            $dishRecommand->changeDishRecommand($_GP);
            
            $data = array();
            $data = intval($_GP['isrecommand']);
            ajaxReturnData(1,'更新成功',$data);
        }
        
        
        public function changeDishIsNew(){
            $_GP = $this->request;
            
            //changeDishRecommand
            $dishNew = new \service\seller\ShopDishService();
            $dishNew->changeDishIsNew($_GP);
            $data = array();
            $data = intval($_GP['isnew']);
            ajaxReturnData(1,'更新成功',$data);
        }
        
        public function changeDishStatus(){
            $_GP = $this->request;
            $dishid = intval($_GP['dish_id']);
            $status = intval($_GP['status']);
            if (empty($dishid)) ajaxReturnData(0,'更新失败');
            $dishStatus = new \service\seller\ShopDishService();
            $flag = $dishStatus->changeDishStatus(array('dish_id'=>$dishid,'status'=>$status));
            $data = array();
            $data = intval($_GP['status']);
            if ($flag == -1){
                ajaxReturnData(0,'抱歉，您存在已上线限时购的商品，不能下架',$data);
            }else{
                ajaxReturnData(1,'更新成功',$data);
            }
        }
        
        public function deleteImg(){
            $_GP = $this->request;
            
            
            
            $imgArr = explode(',',$_GP['xcimg']);
            
            foreach($imgArr as $k=>$v)
            {
                if($v == $_GP['data_pic'])
                {
                    unset($imgArr[$k]);
                    break;
                }
            }
            $imgStr = implode(',',$imgArr);

            $data = array();
            $data['dish_id'] = $_GP['dish_id'];
            $data['picurl']  = $imgStr;
            
            $upstatus = $this->shopPic->upPic($data);
            
            ajaxReturnData(1,'删除成功');
        }
        
        //获取宝贝的相册图片
        public function getXcThumb(){
            $_GP = $this->request;
            
            $dishPicData = $this->shopPic->getDishPic($_GP['dish_id']);
            
            $dishPicJson = json_encode(explode(',', $dishPicData['picurl']));
            
            echo $dishPicJson;
            exit;
        }
        
        public function upChangeOrder(){
            $_GP = $this->request;
            
            $upstatus = $this->shopdish->upChangeOrder($_GP);
            
            exit;
        }
        
        //宝贝参与限时购
        public function addLtc(){
            $_GP = $this->request;
            $ltcObj = new \service\seller\limitedTimepurChaseService();
            
            $url = mobile_url('product',array('op'=>'productlist'));
            $dish_id    = intval($_GP['id']);
            $ac_dish_id = intval($_GP['ac_dish_id']);
            
            //如果存在ac_dish_id
            if($ac_dish_id > 0){
                $ltcInfo    = $ltcObj->getLtcDish($ac_dish_id);
                $ac_dish_id = $ltcInfo['ac_dish_id'];
                $dish_id    = $ltcInfo['ac_shop_dish'];
                $ltcInfo['ac_dish_price'] = FormatMoney($ltcInfo['ac_dish_price'],0);
                
                $sysCate = new \service\seller\ShopCateService();
                $twoCategory = $sysCate->twoShopCategory($ltcInfo['ac_p1_id']);  
                
                //获取区域详情信息
                $ltcObj = new \service\seller\limitedTimepurChaseService();
                
                $actiData = $ltcObj->getAreaList($ltcInfo['ac_action_id']);

                $activGroup = $ltcObj->getAreaGroupList($actiData);
                
            }else{
                //取商品信息
                $shopDishM = new \model\shop_dish_model();
                $product = $shopDishM->getOneShopDish(array('id'=>$dish_id),'store_p1,store_p2,marketprice,store_count');
                if (!empty($product)){
                    //根据栏目id取出栏目名称
                    $shopCatM = new \model\shop_category_model();
                    $oneCatArr = $shopCatM->getOneShopCategory(array('id'=>$product['store_p1']),'id,name,parentid');
                    if (!empty($oneCatArr)){
                        $twoCatArr = $shopCatM->getAllShopCategory(array('parentid'=>$oneCatArr['id']),'id,name');
                    }
                }
                //取当前活动
                $currentAct = getCurrentAct();
            }
            //获取当前正在进行的活动
            
            $areaGroup = $ltcObj->getAreaGroup();
            $areaList  = $ltcObj->getListAll();
            
            //查询系统一级分类 二级分类
            $shopCateObj = new \service\seller\ShopCateService();
            $oneCate = $shopCateObj->oneShopCategory();
            
            include page('dish/addltc');
        }
        
        //获取宝贝内容
        public function ltcAreaDeti(){
             $_GP = $this->request;
            if($_GP['aid'] <= 0)
            {
                echo '';
                exit;
            }
            $ltcObj = new \service\seller\limitedTimepurChaseService();
            
            $areaGroup = $ltcObj->getAreaGroupList($_GP['aid']);
            $time_html = '';
            foreach($areaGroup as $v){
                $time_html .= '开始时段:'.date('H:i:s',$v['ac_area_time_str']);
                $time_html .= '结束时段:'.date('H:i:s',$v['ac_area_time_end']).'<br>';
            }
            
            echo $time_html;
            exit;
        }
        
        public function addLtcSub(){
            $_GP = $this->request;
            
            $ltcObj = new \service\seller\limitedTimepurChaseService();
            $addActiDish = $ltcObj->addActivityDish1($_GP);
            
            $url = mobile_url('product',array('op'=>'productlist'));
            if($addActiDish && $addActiDish['status'] == 1)
            {
                message($addActiDish['mes'],$url,'success');
            }
            else{
                message($addActiDish['mes'],$url,'error');
            }
        }
        
        //获取系统二级分类
        public function getSystemCate(){
            $_GP = $this->request;
            
            $sysCate = new \service\seller\ShopCateService();
            $twoCategory = $sysCate->twoShopCategory($_GP['pid']);
            
            echo json_encode($twoCategory);
            exit;
        }
        
        //获取活动对应的时段
        public function getActivityAreaList(){
            $_GP = $this->request;

            //squdian_activity_list
            $ltcObj = new \service\seller\limitedTimepurChaseService();
            $actiData = $ltcObj->getAreaList($_GP['ac_id']);

            $activGroup = $ltcObj->getAreaGroupList($actiData);
            
            if(count($activGroup) > 0)
            {
                echo json_encode($activGroup);
            }
            else{
                $activGroup = array();
                echo json_encode($activGroup);
            }
            exit;
        }
        
        //ajaxReturnData(1,'删除成功');
        public function delLtc(){
            $_GP = $this->request;
            $ac_dish_id = intval($_GP['ac_dish_id']);
            if (empty($ac_dish_id)) echo 0;
            $ltcObj = new \service\seller\limitedTimepurChaseService();
            $addActiDish = $ltcObj->delActivityDish($ac_dish_id);
            $url = mobile_url('product',array('op'=>'productlist'));
            echo 1;
            exit;
        }
                
        
	/***********************************************************/
	/**************************xxxx***********************/
	/***********************************************************/
}