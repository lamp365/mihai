<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue"><?php  if(!empty($item['id'])) { ?>编辑<?php  }else{ ?>新增<?php  } ?>商品<a href="<?php  echo web_url('goods', array('op' => 'csv_post'))?>" style="float:right;font-size:14px;"><i class="icon-plus-sign-alt"></i>批量导入产品</a></h3>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<style type="text/css">
.panel.with-nav-tabs .panel-heading{
    padding: 5px 5px 0 5px;
}
.panel.with-nav-tabs .nav-tabs{
	border-bottom: none;
}
.panel.with-nav-tabs .nav-justified{
	margin-bottom: -1px;
}
/********************************************************************/
/*** PANEL DEFAULT ***/
.with-nav-tabs.panel-default .nav-tabs > li > a,
.with-nav-tabs.panel-default .nav-tabs > li > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li > a:focus {
    color: #777;
}
.with-nav-tabs.panel-default .nav-tabs > .open > a,
.with-nav-tabs.panel-default .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-default .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-default .nav-tabs > li > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li > a:focus {
    color: #777;
	background-color: #ddd;
	border-color: transparent;
}
.with-nav-tabs.panel-default .nav-tabs > li.active > a,
.with-nav-tabs.panel-default .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li.active > a:focus {
	color: #555;
	background-color: #fff;
	border-color: #ddd;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #f5f5f5;
    border-color: #ddd;
}
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #777;   
}
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #ddd;
}
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff;
    background-color: #555;
}
/********************************************************************/
/*** PANEL PRIMARY ***/
.with-nav-tabs.panel-primary .nav-tabs > li > a,
.with-nav-tabs.panel-primary .nav-tabs > li > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li > a:focus {
    color: #fff;
}
.with-nav-tabs.panel-primary .nav-tabs > .open > a,
.with-nav-tabs.panel-primary .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-primary .nav-tabs > li > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li > a:focus {
	color: #fff;
	background-color: #3071a9;
	border-color: transparent;
}
.with-nav-tabs.panel-primary .nav-tabs > li.active > a,
.with-nav-tabs.panel-primary .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li.active > a:focus {
	color: #428bca;
	background-color: #fff;
	border-color: #428bca;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #428bca;
    border-color: #3071a9;
}
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #fff;   
}
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #3071a9;
}
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    background-color: #4a9fe9;
}
/********************************************************************/
/*** PANEL SUCCESS ***/
.with-nav-tabs.panel-success .nav-tabs > li > a,
.with-nav-tabs.panel-success .nav-tabs > li > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li > a:focus {
	color: #3c763d;
}
.with-nav-tabs.panel-success .nav-tabs > .open > a,
.with-nav-tabs.panel-success .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-success .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-success .nav-tabs > li > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li > a:focus {
	color: #3c763d;
	background-color: #d6e9c6;
	border-color: transparent;
}
.with-nav-tabs.panel-success .nav-tabs > li.active > a,
.with-nav-tabs.panel-success .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
	color: #3c763d;
	background-color: #fff;
	border-color: #d6e9c6;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #dff0d8;
    border-color: #d6e9c6;
}
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #3c763d;   
}
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #d6e9c6;
}
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff;
    background-color: #3c763d;
}
/********************************************************************/
/*** PANEL INFO ***/
.with-nav-tabs.panel-info .nav-tabs > li > a,
.with-nav-tabs.panel-info .nav-tabs > li > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li > a:focus {
	color: #31708f;
}
.with-nav-tabs.panel-info .nav-tabs > .open > a,
.with-nav-tabs.panel-info .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-info .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-info .nav-tabs > li > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li > a:focus {
	color: #31708f;
	background-color: #bce8f1;
	border-color: transparent;
}
.with-nav-tabs.panel-info .nav-tabs > li.active > a,
.with-nav-tabs.panel-info .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li.active > a:focus {
	color: #31708f;
	background-color: #fff;
	border-color: #bce8f1;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #d9edf7;
    border-color: #bce8f1;
}
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #31708f;   
}
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #bce8f1;
}
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff;
    background-color: #31708f;
}
/********************************************************************/
/*** PANEL WARNING ***/
.with-nav-tabs.panel-warning .nav-tabs > li > a,
.with-nav-tabs.panel-warning .nav-tabs > li > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li > a:focus {
	color: #8a6d3b;
}
.with-nav-tabs.panel-warning .nav-tabs > .open > a,
.with-nav-tabs.panel-warning .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-warning .nav-tabs > li > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li > a:focus {
	color: #8a6d3b;
	background-color: #faebcc;
	border-color: transparent;
}
.with-nav-tabs.panel-warning .nav-tabs > li.active > a,
.with-nav-tabs.panel-warning .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li.active > a:focus {
	color: #8a6d3b;
	background-color: #fff;
	border-color: #faebcc;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #fcf8e3;
    border-color: #faebcc;
}
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #8a6d3b; 
}
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #faebcc;
}
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff;
    background-color: #8a6d3b;
}
/********************************************************************/
/*** PANEL DANGER ***/
.with-nav-tabs.panel-danger .nav-tabs > li > a,
.with-nav-tabs.panel-danger .nav-tabs > li > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li > a:focus {
	color: #a94442;
}
.with-nav-tabs.panel-danger .nav-tabs > .open > a,
.with-nav-tabs.panel-danger .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-danger .nav-tabs > li > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li > a:focus {
	color: #a94442;
	background-color: #ebccd1;
	border-color: transparent;
}
.with-nav-tabs.panel-danger .nav-tabs > li.active > a,
.with-nav-tabs.panel-danger .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li.active > a:focus {
	color: #a94442;
	background-color: #fff;
	border-color: #ebccd1;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #f2dede; /* bg color */
    border-color: #ebccd1; /* border color */
}
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #a94442; /* normal text color */  
}
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #ebccd1; /* hover bg color */
}
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff; /* active text color */
    background-color: #a94442; /* active bg color */
}
</style>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>

 <form action="" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" onsubmit="return fillform()">


<div class="panel with-nav-tabs panel-primary">
 	<div class="panel-heading">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1primary" data-toggle="tab">基础信息</a></li>
            <li><a href="#tab2primary" data-toggle="tab">图片</a></li>
            <li><a href="#tab3primary" data-toggle="tab">商品详情</a></li>
           	<li><a href="#tab4primary" data-toggle="tab">商品规格</a></li>
        </ul>
    </div>
    <div class="panel-body">
    	<div class="tab-content">
    		<div class="tab-pane fade in active" id="tab1primary">
    			<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商品名称：</label>

										<div class="col-sm-9">
											   <input type="text" name="goodsname" id="goodsname" maxlength="100" class="span7" style="width:320px;" value="<?php  echo $item['title'];?>" />
										</div>
		</div>
                            <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >品名：</label>
										<div class="col-sm-9">
												 <input type="text" name="subtitle"  value="<?php  echo $item['subtitle'];?>" style="width:320px;" /> 
										</div>
		                      </div>
							   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 组合ID(ID*NUM,ID*NUM)：</label>
										<div class="col-sm-9">
												 <input type="text" name="Supplier"  value="<?php  echo $item['Supplier'];?>" style="width:320px;" /> 
										</div>
		                      </div>
							  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 单件重量（KG）：</label>
									    <div class="col-sm-9">
												 <input type="text" name="weight"  value="<?php  echo $item['weight'];?>" /> 
										</div>
		                      </div>
							  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 运费系数：</label>
										<div class="col-sm-9">
												 <input type="text" name="coefficient"  value="<?php  echo $item['coefficient'];?>" /> 
										</div>
		                      </div>
							  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 规格型号：</label>
										<div class="col-sm-9">
												 <input type="text" name="origin"  value="<?php  echo $item['origin'];?>" /> 
										</div>
		                      </div>
							  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 单位：</label>
										<div class="col-sm-9">
												 <input type="text" name="unit"  value="<?php  echo $item['unit'];?>" /> 
										</div>
		                      </div>
 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 货号：</label>

										<div class="col-sm-9">
												 <input type="text" name="goodssn"  value="<?php  echo $item['goodssn'];?>" /> 
										</div>
		</div>
		 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 排序：</label>

										<div class="col-sm-9">
												 <input type="text" name="lists"  value="<?php  echo $item['lists'];?>" /> 
										</div>
		</div>
		                    <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 品牌：</label>

										<div class="col-sm-9">
												 <select name="brand" class="js-example-responsive" id="brand">
												        <option value="0">请选择</option>
                                                        <?php if ( is_array($brand) ){ foreach ( $brand as $brand_value ){ $selected = ($brand_value['id'] == $item['brand'])?"selected":"";?>
                                                                <option <?php echo $selected; ?> value="<?php echo $brand_value['id']; ?>"><?php echo $brand_value['brand']; ?></option>
														<?php }} ?>
                                                 </select>
										</div>
		                      </div>                 
	 					<?php if(isHasPowerOperateField('shop_goods','status')){ ?>
                          <div class="form-group">
								<label class="col-sm-2 control-label no-padding-left" > 是否上架销售：</label>

								<div class="col-sm-9">
										 <input type="radio" name="status" value="1" id="isshow1" <?php  if($item['status'] == 1) { ?>checked="true"<?php  } ?> /> 是  &nbsp;&nbsp;
										<input type="radio" name="status" value="0" id="isshow2"  <?php  if($item['status'] == 0) { ?>checked="true"<?php  } ?> /> 否
								</div>
						  </div>
	 					<?php } ?>
		
		 <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 分类：</label>
			<div class="col-sm-9">
			<select  style="margin-right:15px;" id="pcate" name="pcate" onchange="fetchChildCategory(this.options[this.selectedIndex].value)"  autocomplete="off">
                <option value="0">请选择一级分类</option>
                <?php  if(is_array($category)) { foreach($category as $row) { ?>
                <?php  if($row['parentid'] == 0) { ?>
                <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['pcate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                <?php  } ?>
                <?php  } } ?>
            </select>
            <select  id="cate_2" name="ccate" onchange="fetchChildCategory2(this.options[this.selectedIndex].value)" autocomplete="off">
                <option value="-1">请选择二级分类</option>
                <?php  if(!empty($item['ccate']) && !empty($children[$item['pcate']])) { ?>
                <?php  if(is_array($children[$item['pcate']])) { foreach($children[$item['pcate']] as $row) { ?>
                <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['ccate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
                <?php  } } ?>
                <?php  } ?>
            </select>
			<select  id="cate_3" name="ccate2" autocomplete="off">
                <option value="0">请选择三级分类</option>
                <?php 
				    if(!empty($item['ccate2']) && !empty($children[$item['ccate']])) { 
				       if(is_array($children[$item['ccate']])) { 
						   foreach($children[$item['ccate']] as $row) { 
				?>
                         <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['ccate2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
                <?php  } } } ?>
            </select>
		</div>
  </div>

	 <?php if(isHasPowerOperateField('shop_goods','marketprice',$_GP['id']) || empty($_GP['id'])){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 本店售价：</label>
			<div class="col-sm-9">
					  <input type="text" name="marketprice" id="marketprice" value="<?php  echo empty($item['marketprice'])?'0':$item['marketprice'];?>" />元
			</div>
		</div>
	<?php } ?>
	 <?php if(isHasPowerOperateField('shop_goods','productprice',$_GP['id']) || empty($_GP['id'])){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 市场售价：</label>
			<div class="col-sm-9">
					  <input type="text" name="productprice" id="productprice"  value="<?php  echo empty($item['productprice'])?'0':$item['productprice'];?>" />元
			</div>
		</div>
	 <?php } ?>
				
		
			
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 库存：</label>

										<div class="col-sm-9">
												
          	 <input type="text" name="total" id="total" value="<?php  echo empty($item['total'])?'0':$item['total'];?>" /> 
										</div>
		</div>
		
				<div class="form-group" style="display:none;">
										<label class="col-sm-2 control-label no-padding-left" > 减库存方式：</label>

										<div class="col-sm-9">
												
          	<input type="radio" name="totalcnf" value="0" id="totalcnf1" <?php  if(empty($item) || $item['totalcnf'] == 0) { ?>checked="true"<?php  } ?> /> 拍下减库存
            &nbsp;&nbsp;
          <input type="radio" name="totalcnf" value="1" id="totalcnf2"  <?php  if(!empty($item) && $item['totalcnf'] == 1) { ?>checked="true"<?php  } ?> /> 永不减库存
       
										</div>
		</div>
		
		
				<div class="form-group" style="display:none;">
										<label class="col-sm-2 control-label no-padding-left" > 商品属性：</label>

										<div class="col-sm-9">
				 <input type="checkbox" name="isrecommand" value="1" id="isrecommand" <?php  if($item['isrecommand'] == 1) { ?>checked="true"<?php  } ?> /> 首页推荐
				 		 <input type="checkbox" name="isnew" value="1" <?php  if($item['isnew'] == 1) { ?>checked="true"<?php  } ?> /> 新品
				 		 		 <input type="checkbox" name="isfirst" value="1"  <?php  if($item['isfirst'] == 1) { ?>checked="true"<?php  } ?> /> 首发
				 		 		 		 <input type="checkbox" name="ishot" value="1"  <?php  if($item['ishot'] == 1) { ?>checked="true"<?php  } ?> /> 特价
				 		 		 		  <input type="checkbox" name="isjingping" value="1"<?php  if($item['isjingping'] == 1) { ?>checked="true"<?php  } ?> /> 精品
                    &nbsp;   
										</div>
		</div>
		
					<div class="form-group" style="display:none;" >
										<label class="col-sm-2 control-label no-padding-left" > 免运费商品：</label>

										<div class="col-sm-9">
				 <input type="checkbox" name="issendfree" value="1" id="isnew" <?php  if($item['issendfree'] == 1) { ?>checked="true"<?php  } ?> /> 打勾表示此商品不会产生运费花销，否则按照正常运费计算。
           &nbsp;   
										</div>
		</div>
		
			<div class="form-group" style="display:none;">
										<label class="col-sm-2 control-label no-padding-left" > 限时促销：</label>

										<div class="col-sm-9">
				 
<input type="checkbox" name="istime" id='istime' value="1" id="isnew" <?php  if($item['istime'] == 1) { ?>checked="true"<?php  } ?> /> 开启限时促销
         <input type="text" id="datepicker_timestart" name="timestart" value="<?php if(!empty($item['timestart'])){echo date('Y-m-d H:i',$item['timestart']);}?>" readonly="readonly" />
	<script type="text/javascript">
		$("#datepicker_timestart").datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			minView: "0",
			//pickerPosition: "top-right",
			autoclose: true
		});
	</script> - 
	<input type="text"  id="datepicker_timeend" name="timeend" value="<?php if(!empty($item['timestart'])){echo date('Y-m-d H:i',$item['timeend']);}?>" readonly="readonly" />
	<script type="text/javascript">
		$("#datepicker_timeend").datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			minView: "0",
			//pickerPosition: "top-right",
			autoclose: true
		});
	</script>  
										</div>
		</div>
		
		
					<div class="form-group" style="display:none;">
										<label class="col-sm-2 control-label no-padding-left" >奖励积分：</label>

										<div class="col-sm-9">
				<input type="text" name="credit" id="credit" value="<?php  echo empty($item['credit'])?'0':$item['credit'];?>" />
           
            <p class="help-block">会员购买商品赠送的积分, 如果不填写，则默认为不奖励积分</p>
          
										</div>
		</div>
		
    		</div>
    		<div class="tab-pane fade" id="tab2primary">
    							<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >商品主图：<br/>（建议640*640）</label>

										<div class="col-sm-9">
				  <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                        	 <?php  if(!empty($item['thumb'])) { ?>
                            <img src="<?php  echo $item['thumb'];?>" alt="" onerror="$(this).remove();">
                              <?php  } ?>
                            </div>
                        <div>
                         <input name="thumb" id="thumb" type="file" />
                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                        </div>
                    </div>
										</div>
		</div>
		
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 其他图片：</label>

										<div class="col-sm-9">
				         <span id="selectimage" tabindex="-1" class="btn btn-primary"><i class="icon-plus"></i> 上传照片</span><span style="color:red;">
                    <input name="piclist" type="hidden" value="<?php  echo $item['piclist'];?>" /></span>
                <div id="file_upload-queue" class="uploadify-queue"></div>
                <ul class="ipost-list ui-sortable" id="fileList">
                    <?php  if(is_array($piclist)) { foreach($piclist as $v) { ?> 
                    <li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;   width: 125px;  height: 130px;">
                        <span class="item_box">
                            <img src="<?php  echo $v['picurl'];?>" style="width:50px;height:50px">    </span>
                       		 <a  href="javascript:;" onclick="deletepic(this, <?php  echo !empty($v['id'])?$v['id']:0;?>);" title="删除">删除</a>
                    
                        <input type="hidden" value="<?php  echo $v['picurl'];?>" name="attachment[]">
                    </li>
                    <?php  } } ?>
                </ul>
										</div>
		</div>
    		</div>
    		<div class="tab-pane fade" id="tab3primary">
    				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >商品简单描述：</label>

										<div class="col-sm-9">
				   <textarea style="height:150px;"  id="description" name="description" cols="70"><?php  echo $item['description'];?></textarea>
             
										</div>
		</div>
		
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >商品详细描述：<br/><span style="font-size:12px">(建议图片宽不超过640px)</span></label>

										<div class="col-sm-9">
                  <textarea  id="container" name="content" style="height:400px; width:800px;"><?php  echo $item['content'];?></textarea>
             
										</div>
		</div>
    		</div>
    		<div class="tab-pane fade" id="tab4primary">
    			<?php  include page('goods_option');?>
    		</div>
    	</div>
    </div>
</div>
   
      	<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" ></label>

										<div class="col-sm-9">
				    <button type="submit" class="btn btn-primary span2" name="submit" value="submit"><i class="icon-edit"></i>保存商品信息</button>    
										</div>
		</div>
		
 </form>


		
		
		
         
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/themes/default/default.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/lang/zh_CN.js"></script>    
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
<script type="text/javascript">var ue = UE.getEditor('container');</script>
    
    
<script language="javascript">
 var category = <?php  echo json_encode($children)?>;
 function fetchChildCategory(cid) {
	var html = '<option value="0">请选择二级分类</option>';

	if (!category || !category[cid]) {
		$('#cate_2').html(html);
			fetchChildCategory2(document.getElementById("cate_2").options[document.getElementById("cate_2").selectedIndex].value);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
    	fetchChildCategory2(document.getElementById("cate_2").options[document.getElementById("cate_2").selectedIndex].value);

 }
  function fetchChildCategory2(cid) {
	var html = '<option value="0">请选择三级分类</option>';
	if (!category || !category[cid]) {
		$('#cate_3').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cate_3').html(html);
 }



<?php if(!empty($item['ccate'])){?>
   document.getElementById("cate_2").value="<?php echo $item['ccate']?>";
 <?php }?>
 <?php if(!empty($item['ccate2'])){?>
   document.getElementById("cate_3").value="<?php echo $item['ccate2']?>";
 <?php }?>
$(function(){
	var i = 0;
	$('#selectimage').click(function() {
		var editor = KindEditor.editor({
			allowFileManager : false,
			imageSizeLimit : '10MB',
			uploadJson : '<?php  echo mobile_url('upload')?>'
		});
		editor.loadPlugin('multiimage', function() {
			editor.plugin.multiImageDialog({
				clickFn : function(list) {
					if (list && list.length > 0) {
						for (i in list) {
							if (list[i]) {
								html =	'<li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;  width: 125px;  height: 130px;">'+
								'<span class="item_box"> <img src="'+list[i]['url']+'" style="width:50px;height:50px"></span>'+
								'<a href="javascript:;" onclick="deletepic(this,0);" title="删除">删除</a>'+
								'<input type="hidden" name="attachment-new[]" value="'+list[i]['filename']+'" />'+
								'</li>';
								$('#fileList').append(html);
								i++;
							}
						}
						editor.hideDialog();
					} else {
						alert('请先选择要上传的图片！');
					}
				}
			});
		});
	});
	//select2下拉框初始化
	$("#brand").select2();
});
function deletepic(obj,oid){
	if (confirm("确认要删除？")) {
		
		var $thisob=$(obj);
		var $liobj=$thisob.parent();
		var picurl=$liobj.children('input').val();
		if (oid == 0)
		{
            $liobj.remove();
			alert("删除成功");
			return;
		}
		$.post('<?php  echo create_url('site',array('name' => 'shop','do' => 'picdelete'))?>',{ pic:picurl,ids:oid},function(m){
			if(m==1) {
				$liobj.remove();
			} else {
				alert("删除失败");
			}
		},"html");	
	}
}
function fillform()
{
    if(ue.queryCommandState( 'source' )==1){		
	   document.getElementById("container").value=ue.getContent();	
    }else{	
	   document.getElementById("container").value=ue.body.innerHTML;	
	}
	return true;
}
   
    </script>
<?php  include page('footer');?>
