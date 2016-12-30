<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue"><?php  if(!empty($item['id'])) { ?>编辑<?php  }else{ ?>新增<?php  } ?></h3>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
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
<form role="form" class="form-horizontal">
 	<div class="form-group">
       	<label class="col-sm-2 control-label no-padding-left" > 查询产品：</label>
		<div class="col-sm-9">
		<select  style="margin-right:15px;" id="pcates" name="pcates" class="pcates" onchange="fetchChildCategory(this,this.options[this.selectedIndex].value)"  autocomplete="off">
            <option value="0">请选择一级分类</option>
            <?php  if(is_array($category)) { foreach($category as $row) { ?>
            <?php  if($row['parentid'] == 0) { ?>
            <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['p1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
            <?php  } ?>
            <?php  } } ?>
        </select>
        <select  id="cates_2" name="ccates" class="cates_2" onchange="fetchChildCategory2(this,this.options[this.selectedIndex].value)" autocomplete="off">
            <option value="0">请选择二级分类</option>
            <?php  if(!empty($item['p2']) && !empty($childrens[$item['p1']])) { ?>
            <?php  if(is_array($childrens[$item['p1']])) { foreach($childrens[$item['p1']] as $row) { ?>
            <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['p2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
            <?php  } } ?>
            <?php  } ?>
        </select>
		<select  id="cate_3" name="ccate2" class="cate_3" autocomplete="off">
            <option value="0">请选择三级分类</option>
            <?php 
			    if(!empty($item['p3']) && !empty($childrens[$item['p3']])) { 
			       if(is_array($childrens[$item['p3']])) { 
					   foreach($childrens[$item['p3']] as $row) { 
			?>
                     <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['p3']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
            <?php  } } } ?>
        </select>
	</div>
  </div>
  <div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" ></label>
		<div class="col-sm-9">
	    <a href="javascript:void(0)" onclick="findgoods()" class="btn btn-primary span2" name="submit" ><i class="icon-edit"></i>查找产品</a>    
		</div>
	</div>
  </form>
          <form action="" method="post" enctype="multipart/form-data" class="tab-content form-horizontal" role="form" onsubmit="return fillform()">
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
		<label class="col-sm-2 control-label no-padding-left" > 选择产品：</label>
		<div class="col-sm-9">
			  <select name="c_goods" class="js-example-responsive" id="c_goods" style="width: 50%">
			       <?php if (!empty($item['gname'])){ ?>
                   <option value='<?php echo $item['gid']; ?>'><?php echo $item['gname']; ?></option>
				   <?php }else{ ?>
			       <option value='0'>未选择产品</option>
				   <?php } ?>
			  </select>
		</div>
</div>
 <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 自定义名称（默认为产品库名称）：</label>

			<div class="col-sm-9">
						 <input type="text" name="dishname" id="dishname" maxlength="100" class="span7"  value="<?php  echo $item['title'];?>" />
			</div>
</div>


 <?php foreach($extend_category as $key => $extend_arr){  if($key==0){ $show_zi = "扩展分类：";}else{ $show_zi=' '; } ?>

 <div class="form-group kuozhan_fenlei">
	 <label class="col-sm-2 control-label no-padding-left" > <?php echo $show_zi; ?></label>
	 <div class="col-sm-4" style="margin-top: 6px;">
		 <input type="hidden" name="extendids_kuozhan[]" value="<?php echo $extend_arr['id'];?>" class="extendids_kuozhan">
		 <select  style="margin-right:15px;"  name="pcates_kuozhan[]" class="pcates" onchange="fetchChildCategory(this,this.options[this.selectedIndex].value)"  autocomplete="off">
			 <option value="0">请选择一级分类</option>
			 <?php  if(is_array($category)) { foreach($category as $row) { ?>
				 <?php  if($row['parentid'] == 0) { ?>
					 <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $extend_arr['p1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
				 <?php  } ?>
			 <?php  } } ?>
		 </select>
		 <select  name="ccates_kuozhan[]" class="cates_2" onchange="fetchChildCategory2(this,this.options[this.selectedIndex].value)" autocomplete="off">
			 <option value="-1">请选择二级分类</option>
			 <?php  if(!empty($item['p2']) && !empty($childrens[$extend_arr['p1']])) { ?>
				 <?php  if(is_array($childrens[$extend_arr['p1']])) { foreach($childrens[$extend_arr['p1']] as $row) { ?>
					 <option  value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $extend_arr['p2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
				 <?php  } } ?>
			 <?php  } ?>
		 </select>
		 <select   name="ccate2_kuozhan[]" class="cate_3" autocomplete="off">
			 <option value="0">请选择三级分类</option>
			 <?php
			 if(!empty($item['p3']) && !empty($childrens[$extend_arr['p3']])) {
				 if(is_array($childrens[$extend_arr['p3']])) {
					 foreach($childrens[$extend_arr['p3']] as $row) {
						 ?>
						 <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $extend_arr['p3']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
					 <?php  } } } ?>
		 </select>
	 </div>
	 <div class="col-sm-6">
		 <a href="javascript:void(0);" class="btn btn-danger btn-xs remove_fenlei" style="display: " onclick="remove_kuozhan_fenlei(this)">移除</a>
	 </div>

 </div>
<?php } ?>

 <div class="form-group">
	 <label class="col-sm-2 control-label no-padding-left"></label>
	 <div class="col-sm-9">
		 <a href="javascript:void(0)" onclick="add_kuozhan_fenlei()" class="btn btn-primary span2" name="button"><i class="icon-plus"></i>添加分类</a>
	 </div>
 </div>
	 <input type="hidden" class="delete_extend_ids" name="delete_extend_ids" value="">


 <?php if(isHasPowerOperateField('shop_dish','status')){ ?>
 <div class="form-group" >
		<label class="col-sm-2 control-label no-padding-left" > 是否销售：</label>
		<div class="col-sm-9">
		    <input type="radio" name="status" value="1" id="isshow1" <?php  if($item['status'] ==1 ) { ?>checked="true"<?php  } ?> /> 是  &nbsp;&nbsp;
			<input type="radio" name="status" value="0" id="isshow2"  <?php  if($item['status'] == 0) { ?>checked="true"<?php  } ?> /> 否
		</div>
</div>
<?php } ?>
		 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 仓库：</label>

										<div class="col-sm-9">
												  <select  style="margin-right:15px;" id="pcate" name="pcate" onchange="fetchChildarea(this.options[this.selectedIndex].value)"  autocomplete="off">
                <option value="0">请选择一级分类</option>
                <?php  if(is_array($area)) { foreach($area as $row) { ?>
                <?php  if($row['parentid'] == 0) { ?>
                <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['pcate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                <?php  } ?>
                <?php  } } ?>
            </select>
            <select  id="cate_2" name="ccate" autocomplete="off">
                <option value="0">请选择二级分类</option>
                <?php  if(!empty($item['ccate']) && !empty($children[$item['pcate']])) { ?>
                <?php  if(is_array($children[$item['pcate']])) { foreach($children[$item['pcate']] as $row) { ?>
                <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['ccate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
                <?php  } } ?>
                <?php  } ?>
            </select>
										</div>
		</div>

	 <?php if(isHasPowerOperateField('shop_dish','taxid') || empty($_GP['id'])){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 税率设置：</label>
		    <div class="col-sm-9">	
			<select  id="taxid" name="taxid" autocomplete="off">
                   <option value="0">请选择税率</option>
				     <?php  if(is_array($taxlist)) { foreach($taxlist as $row) { ?>
                         <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['taxid']) { ?> selected="selected"<?php  } ?>><?php  echo $row['type'];?></option>
					<?php  } }?>
			 </select>
			</div>
		</div>
	 <?php } ?>

	 <?php if(isHasPowerOperateField('shop_dish','marketprice') || empty($_GP['id'])){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 促销价格：</label>

			<div class="col-sm-9">
					  <input type="text" name="marketprice" id="marketprice" value="<?php  echo empty($item['marketprice'])?'0':$item['marketprice'];?>" />
			</div>
		</div>
	 <?php } ?>
	 <?php if(isHasPowerOperateField('shop_dish','productprice') || empty($_GP['id'])){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 参考价格：</label>

			<div class="col-sm-9">
					  <input type="text" name="productprice" id="productprice"  value="<?php  echo empty($item['productprice'])?'0':$item['productprice'];?>" />
			</div>
		</div>
	 <?php } ?>
		   <input type="hidden" class="vip-number"   value="<?php echo max(1,count($vip_list)); ?>">

	 <?php if(isHasPowerOperateField('shop_dish','vip_price') || empty($_GP['id'])){ ?>
		   <?php if ( is_array($dish_vip_list) && !empty($dish_vip_list) ){ foreach ( $dish_vip_list as $key=>$dish_vip_list_value ){ ?>
	 	   <div class="form-group form-inline vip-form">
	 	   		<label class="col-sm-2 control-label no-padding-left" > <?php if ( $key == 0 ){ echo '会员价格：'; }?></label>
	 	   		<div class="col-sm-4">
					  <select name="v2[]" class="form-control vip-select" onchange='changeFun(this)'>
					  		<option value="-1">--请选择--</option>
					  		<?php if ( is_array($vip_list) && !empty($vip_list) ){  foreach ( $vip_list as $vip_list_value ){?>
					  		<!-- //currency属性用来控制价格符号，1代表￥，2代表$ -->
                                   <option currency="1" value='<?php echo $vip_list_value['id'] ?>' <?php echo $dish_vip_list_value['v2'] == $vip_list_value['id']?'selected':'';?> ><?php echo $vip_list_value['name']; ?></option>
							<?php }} ?>
					  </select>
                      <div class="input-group">
                      <span class="input-group-addon">$</span>
					  <input type="text" name="vip_price[]" class="form-control vip_price" value="<?php echo $dish_vip_list_value['vip_price']; ?>" placeholder="请输入价格"/>
					  </div>
				</div>
				<div class="col-sm-6">
					<a href="javascript:void(0);" class="btn btn-danger remove_vip" >移除</a>
				</div>
	 	   </div>
		   <?php }}else{ ?>
            <div class="form-group form-inline vip-form">
	 	   		<label class="col-sm-2 control-label no-padding-left" > <?php if ( $key == 0 ){ echo '会员价格：'; }?></label>
	 	   		<div class="col-sm-4">
					  <select name="v2[]" class="form-control" onchange='changeFun(this)'>
					  		<option value="-1">--请选择--</option>
					  		<?php if ( is_array($vip_list) && !empty($vip_list) ){  foreach ( $vip_list as $vip_list_value ){?>
                                   <option currency="1" value='<?php echo $vip_list_value['id'] ?>'><?php echo $vip_list_value['name']; ?></option>
							<?php }} ?>
					  </select>
					  <input type="text" name="vip_price[]" class="form-control vip_price" value="" placeholder="请输入价格"/>
				</div>
				<div class="col-sm-6">
					<a href="javascript:void(0);" class="btn btn-danger remove_vip" >移除</a>
				</div>
	 	   </div>
		   <?php } ?>

		<?php } ?>

	 	   <div class="form-group">
	 	   		<label class="col-sm-2 control-label no-padding-left" ></label>
	 	   		<div class="col-sm-10">
					<a href="javascript:void(0)" class="btn btn-primary addvip" name="button"><i class="icon-plus"></i>添加会员</a>
				</div>

	 	   </div>
	 	   <div class="form-group">
	 	   		<label class="col-sm-2 control-label no-padding-left" ></label>
	 	   		<div class="col-sm-10">
					
				</div>

	 	   </div>
           <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 排序：</label>

										<div class="col-sm-9">
												<input type="text" name="displayorder" id='displayorder' value="<?php  echo empty($item['displayorder'])?'0':$item['displayorder'];?>" />
										</div>
		       </div>

				<div class="form-group" style="display:none">
										<label class="col-sm-2 control-label no-padding-left" > 表显里程：</label>

										<div class="col-sm-9">
												<input type="text" name="weight" id='weight' value="<?php  echo empty($item['weight'])?'0':$item['weight'];?>" />
										</div>
		       </div>
		       <div class="form-group" style="display:none">
										<label class="col-sm-2 control-label no-padding-left" > 车牌属地：</label>

										<div class="col-sm-9">
												<input type="text" name="dishsn" id='dishsn' value="<?php  echo empty($item['dishsn'])?'':$item['dishsn'];?>" />
										</div>
		       </div>
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 库存：</label>
										<div class="col-sm-9">
												
          	 <input type="text" name="total" id="total" value="<?php  echo empty($item['total'])?'0':$item['total'];?>" /> 
										</div>
		</div>
		
		
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 单笔最大购买数量：</label>
			<div class="col-sm-9">
          	 <input type="text" name="max_buy_quantity" value="<?php  echo empty($item['max_buy_quantity'])?'0':$item['max_buy_quantity'];?>" /> 
          	 <p class="help-block">0为不限制</p>
			</div>
		</div>
		        
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 减库存方式：</label>

										<div class="col-sm-9">
												
          	<input type="radio" name="totalcnf" value="0" id="totalcnf1" <?php  if(empty($item) || $item['totalcnf'] == 0) { ?>checked="true"<?php  } ?> /> 拍下减库存
            &nbsp;&nbsp;
          <input type="radio" name="totalcnf" value="1" id="totalcnf2"  <?php  if(!empty($item) && $item['totalcnf'] == 1) { ?>checked="true"<?php  } ?> /> 永不减库存
       
										</div>
		</div>
		
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 属性：</label>

										<div class="col-sm-9">
				                   <input type="checkbox" name="isrecommand" value="1" id="isrecommand" <?php  if($item['isrecommand'] == 1) { ?>checked="true"<?php  } ?> /> 首页推荐
				 		                <input type="checkbox" name="isnew" value="1" <?php  if($item['isnew'] == 1) { ?>checked="true"<?php  } ?> /> 新品
				 		 		       <input type="checkbox" name="isfirst" value="1"  <?php  if($item['isfirst'] == 1) { ?>checked="true"<?php  } ?> /> 首发
				 		 		 		 <input type="checkbox" name="ishot" value="1"  <?php  if($item['ishot'] == 1) { ?>checked="true"<?php  } ?> /> 热卖
				 		 		 		  <input type="checkbox" name="isjingping" value="1"<?php  if($item['isjingping'] == 1) { ?>checked="true"<?php  } ?> /> 精品
										   <input type="checkbox" name="isdiscount" value="1"<?php  if($item['isdiscount'] == 1) { ?>checked="true"<?php  } ?> /> 活动
                    &nbsp;   
										</div>
		</div>
		
					<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 免运费：</label>

										<div class="col-sm-9">
				 <input type="checkbox" name="issendfree" value="1" id="isnew" <?php  if($item['issendfree'] == 1) { ?>checked="true"<?php  } ?> /> 打勾表示此不会产生运费花销，否则按照正常运费计算。
           &nbsp;   
										</div>
		</div>
	 <?php if(isHasPowerOperateField('shop_dish','type') || empty($_GP['id'])){ ?>
		<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >促销类型:</label>
				<div class="col-sm-9">
					<select id="J_type" name="type">
                		<option value="0" <?php if($item['type']==0){?>selected="selected"<?php  } ?>>一般商品</option>
                        <option value="1" <?php if($item['type']==1){?>selected="selected"<?php  } ?> >团购商品</option>
                        <option value="2" <?php if($item['type']==2){?>selected="selected"<?php  } ?>>秒杀商品</option>
                        <option value="3" <?php if($item['type']==3){?>selected="selected"<?php  } ?>>今日特价商品</option>
						<option value="4" <?php if($item['type']==4){?>selected="selected"<?php  } ?>>限时促销</option>
                    </select>
				</div>
		</div>
	 <?php } ?>

	 <?php if(isHasPowerOperateField('shop_dish','istime') || empty($_GP['id'])){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 促销时间：</label>

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
	 <?php } ?>

	 <?php if(isHasPowerOperateField('shop_dish','team_buy_count') || empty($_GP['id'])){ ?>
		<div id="J_team_buy_count_div" class="form-group" <?php if($item['type']!=1){?>style="display:none;"<?php  } ?> >
				<label class="col-sm-2 control-label no-padding-left" >成团人数:</label>
				<div class="col-sm-9">
					<input type="text" name="team_buy_count" id="J_team_buy_count" value="<?php echo empty($item['team_buy_count'])?'0':$item['team_buy_count'];?>" />
				</div>
		</div>
	 <?php } ?>
	 <?php if(isHasPowerOperateField('shop_dish','draw') || empty($_GP['id'])){ ?>
		<div id="J_team_buy_draw_div" class="form-group" <?php if($item['type']!=1){?>style="display:none;"<?php  } ?> >
				<label class="col-sm-2 control-label no-padding-left" > 开启抽奖：</label>
				<div class="col-sm-7">
				    <input type="radio" name="draw" value="1" id="isdraw1" <?php  if($item['draw'] ==1 ) { ?>checked="true"<?php  } ?> /> 是  &nbsp;&nbsp;
					<input type="radio" name="draw" value="0" id="isdraw2"  <?php  if($item['draw'] == 0) { ?>checked="true"<?php  } ?> /> 否
				</div>
		</div>
		<div id="J_team_buy_draw_num_div" class="form-group" <?php if($item['draw']!=1){?>style="display:none;"<?php  } ?> >
				<label class="col-sm-2 control-label no-padding-left" > 抽奖人数：</label>
				<div class="col-sm-9">
					<input type="text" name="team_draw_num" id="J_team_draw_num" value="<?php echo empty($item['draw_num'])?'0':$item['draw_num'];?>" />
				</div>
		</div>
	 <?php } ?>
	 <?php if(isHasPowerOperateField('shop_dish','timeprice')|| empty($_GP['id'])){ ?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" >限时促销金额:</label>
			<div class="col-sm-9">
				<input type="text" name="timeprice" id="timeprice" value="<?php  echo empty($item['timeprice'])?'0':$item['timeprice'];?>" />
				<p class="help-block">该金额只有开启限时促销，并在结束时间内设置有效</p>
			</div>
		</div>
	 <?php } ?>
		<div class="form-group" style="display:none">
			<label class="col-sm-2 control-label no-padding-left" >奖励积分：</label>
			<div class="col-sm-9">
				<input type="text" name="credit" id="credit" value="<?php  echo empty($item['credit'])?'0':$item['credit'];?>" />
				<p class="help-block">会员购买赠送的积分, 如果不填写，则默认为不奖励积分</p>
          
			</div>
		</div>
	 <?php if(isHasPowerOperateField('shop_dish','commision') || empty($_GP['id'])){ ?>
	 <div class="form-group">
		 <label class="col-sm-2 control-label no-padding-left" > 商品佣金比例：</label>

		 <div class="col-sm-9">
			 <input type="text"  name="commision" id="commision" value="<?php  echo $item['commision'] == 0 ?'0':$item['commision']*100;?>" /> % &nbsp;&nbsp;<span style="color: #737373" id="show_commision">佣金：<?php echo $item['commision']*$item['timeprice'];?>元</span>
		 </div>
	 </div>
	 <?php } ?>

            </div>
            
	            <div class="tab-pane fade" id="tab2primary">
   <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" >广告图：<br/>（建议640*640）</label>

										<div class="col-sm-9">
				  <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-preview thumbnail" style="width: 150px; height: 100px;">
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
		
		
				<div class="form-group" style="display:none;">
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
                       		 <a  href="javascript:;" onclick="deletepic(this);" title="删除">删除</a>
                    
                        <input type="hidden" value="<?php  echo $v['picurl'];?>" name="attachment[]">
                    </li>
                    <?php  } } ?>
                </ul>
										</div>
		</div>
	            </div>
	            <div class="tab-pane fade" id="tab3primary">
	   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >简单描述：</label>
										<div class="col-sm-9">
				   <textarea style="height:100px;"  id="description" name="description" cols="70"><?php  echo $item['description'];?></textarea>           
										</div>
	        	</div>
		
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >详细描述：<br/><span style="font-size:12px">(建议图片宽不超过640px)</span></label>

										<div class="col-sm-9">
                  <textarea  id="container" name="content" ><?php  echo $item['content'];?></textarea>
             
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
				    <button type="submit" class="btn btn-primary span2" name="submit" value="submit"><i class="icon-edit"></i>保存信息</button>    
										</div>
		</div>
</div>
</form>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/themes/default/default.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/lang/zh_CN.js"></script>    
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js?x=201508021"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.min.js?x=141"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
<script type="text/javascript">var ue = UE.getEditor('container');</script>
    
    
<script language="javascript">
		var area = <?php  echo json_encode($children)?>;
   function fetchChildarea(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!area || !area[cid]) {
		$('#cate_2').html(html);
		return false;
	}
	for (i in area[cid]) {
		html += '<option value="'+area[cid][i][0]+'">'+area[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
}
fetchChildarea(document.getElementById("pcate").options[document.getElementById("pcate").selectedIndex].value);
<?php if(!empty( $item['ccate'])){?>
   document.getElementById("cate_2").value="<?php echo $item['ccate']?>";
 <?php }?>
  var category = <?php  echo json_encode($childrens)?>;
 function fetchChildCategory(o_obj,cid) {
	var html = '<option value="0">请选择二级分类</option>';

	var obj = $(o_obj).parent().find('.cates_2').get(0);
	if (!category || !category[cid]) {
		$(o_obj).parent().find('.cates_2').html(html);

			fetchChildCategory2(o_obj,obj.options[obj.selectedIndex].value);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$(o_obj).parent().find('.cates_2').html(html);
    	fetchChildCategory2(o_obj,obj.options[obj.selectedIndex].value);

 }
  function fetchChildCategory2(o_obj,cid) {
	var html = '<option value="0">请选择三级分类</option>';
	if (!category || !category[cid]) {
		$(o_obj).parent().find('.cate_3').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	  $(o_obj).parent().find('.cate_3').html(html);
 }
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
								'<a href="javascript:;" onclick="deletepic(this);" title="删除">删除</a>'+
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
	$("#c_goods,#taxid").select2();

	$('#J_type').change(function() {
		if(parseInt($(this).val())==0)
		{
			$('#istime').prop('checked',false);
			$('#J_team_buy_count_div').hide();
			$('#J_team_buy_draw_div').hide();
			$('#J_team_buy_draw_num_div').hide();
		}
		else{
			if(parseInt($(this).val())==1)
			{
				$('#J_team_buy_count_div').show();
				$('#J_team_buy_draw_div').show();
				if ($("[name='draw']:checked").val()==1) {
					$('#J_team_buy_draw_num_div').show();
				}else{
					$('#J_team_buy_draw_num_div').hide();
				}
			}
			else{
				$('#J_team_buy_count_div').hide();
				$('#J_team_buy_draw_div').hide();
				$('#J_team_buy_draw_num_div').hide();
			}

			$('#istime').prop('checked',true);
		}
	});
	$("[name='draw']").change(function() {
		if ($("[name='draw']:checked").val()==1) {
			$('#J_team_buy_draw_num_div').show();
		}else{
			$('#J_team_buy_draw_num_div').hide();
		}
	});
	//会员价格
	var vipNum = parseInt($(".vip-number").val());
	var vip_i = 1 ;
	$(".addvip").on("click",function(){
		var addHtml = $(".vip-form:last").clone();
		if ( vip_i < vipNum){
			vip_i++;
			$(".vip-form:last").after(addHtml);
			$(".vip-form:last").find(".no-padding-left").text("");
			$(".vip-form:last").find(".vip_price").val("");
		}
	});
	$("body").on("click",".remove_vip",function(){
		var vipLength = $(".vip-form").length;
		if( vipLength == 1 ){
			$(".vip-select").val(-1);
			$(".vip_price").val("");
			return false;
		}else{
			vip_i--;
			$(this).parents(".vip-form").remove();
		}
		$(".vip-form:first").find(".no-padding-left").text(" 会员价格：");
	});

	$("body").on("blur",".vip_price",function(){
		var regEx = /^(([1-9]\d*)|\d)(\.\d{1,2})?$/;
		if( !regEx.test($(this).val()) ){
			var price = parseFloat($(this).val());
			if ( isNaN(price) )
			{
				price = 0;
			}
			$(this).val(price);
		}
	})
});
function deletepic(obj){
	if (confirm("确认要删除？")) {
		var $thisob=$(obj);
		var $liobj=$thisob.parent();
		var picurl=$liobj.children('input').val();
		$.post('<?php  echo create_url('site',array('name' => 'shop','do' => 'picdelete'))?>',{ pic:picurl},function(m){
			if(m=='1') {
				$liobj.remove();
			} else {
				alert("删除失败");
			}
		},"html");	
	}
}
function findgoods(){
    var pcate = $('#pcates').val();
	var ccate = $('#cates_2').val();
	var ccate2 = $('#cate_3').val();
    $.post('<?php  echo create_url('site',array('name' => 'shop','do' => 'dish','op' => 'query'))?>',{pcate:pcate,ccate:ccate,ccate2:ccate2},function(m){
	    $('#c_goods').html(m);
	},"html");	
}
function fillform()
{
		if(ue.queryCommandState( 'source' )==1)
		{
			
	document.getElementById("container").value=ue.getContent();	
		}else
			{
			
	document.getElementById("container").value=ue.body.innerHTML;	
			}
	if ( $('#c_goods').val() == 0)
	{   
		alert('请选择产品');
		return false;
	}
	else if(parseInt($('#J_type').val())!=0 && ($('#datepicker_timestart').val()=='' || $('#datepicker_timeend').val()==''))
	{
		alert('请设置促销时间！');
		return false;
	}

	
	return true;
}

//添加扩展分类
function add_kuozhan_fenlei(){
	$(".kuozhan_fenlei:last").after($(".kuozhan_fenlei:last").clone())
	$(".kuozhan_fenlei:last").find(".no-padding-left").html('');
	$(".kuozhan_fenlei:last").find('.extendids_kuozhan').val('');

	//清除已经选择的下拉
	$(".kuozhan_fenlei:last .pcates>option").attr('selected',false);
	$(".kuozhan_fenlei:last .cates_2>option").attr("selected",false);
	$(".kuozhan_fenlei:last .cates_3>option").attr("selected",false);

}
function remove_kuozhan_fenlei(obj){
	if($(obj).parent().parent().find('.extendids_kuozhan').val().length != 0){
		var kuozhan_id = $(obj).parent().parent().find('.extendids_kuozhan').val();
		if($('.delete_extend_ids').val() != ''){  //不为空就直接拼接
			var kuozhan_id = $('.delete_extend_ids').val() + "," + kuozhan_id;
			$('.delete_extend_ids').val(kuozhan_id);
		}else{  //为空则直接赋值
			$('.delete_extend_ids').val(kuozhan_id);
		}
	}
	if($(".kuozhan_fenlei").length == 1){
		//清除已经选择的下拉
		$(".kuozhan_fenlei:last .pcates>option").attr('selected',false);
		$(".kuozhan_fenlei:last .cates_2>option").attr("selected",false);
		$(".kuozhan_fenlei:last .cates_3>option").attr("selected",false);
		$(obj).parent().parent().find('.extendids_kuozhan').val('');

	}else{
		$(obj).parent().parent().remove();
		$(".kuozhan_fenlei").eq(0).find('.no-padding-left').html('扩展分类：');
	}

}

//输入佣金比例改变佣金
//$("#commision").change(function(){
//	var commision = $(this).val();
//	commision = commision /100;
//	var price = $("#timeprice").val();
//	var result = (commision * price).toFixed(2);
//	$("#show_commision").html("佣金："+ result + '元');
//})

//输入佣金比例改变佣金
$("#commision").on("input propertychange",function(){
	var commision = $(this).val();
	commision = commision /100;
	var price = $("#timeprice").val();
	var result = (commision * price).toFixed(2);
	$("#show_commision").html("佣金："+ result + '元');
})

//输入促销金额改变佣金
$("#timeprice").on("input propertychange",function(){
	var commision = $("#commision").val();
	commision = commision /100;
	var price = $("#timeprice").val();
	var result = (commision * price).toFixed(2);
	$("#show_commision").html("佣金："+ result + '元');
})

//批发价格修改下拉框值变化，修改对应的货币符号

function changeFun(obj){
	var currency_val = $(obj).find("option:selected").attr("currency");
	if( currency_val ==1 ){
		$(obj).siblings('.input-group').find(".input-group-addon").text("￥");
	}else if( currency_val ==2 ){
		$(obj).siblings('.input-group').find(".input-group-addon").text("$");
	}
}

    </script>
<?php  include page('footer');?>
