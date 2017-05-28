<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue"><?php  if(!empty($item['id'])) { ?>编辑<?php  }else{ ?>新增<?php  } ?></h3>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
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
.time_put{
	height: 34px;
	padding: 6px 12px;
	font-size: 14px;
	line-height: 1.42857143;
	color: #555;
	background-color: #fff;
	background-image: none;
	border: 1px solid #ccc;
	border-radius: 4px;
	-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
	box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
	-webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
	-o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
	transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;

}
</style>

<form action="<?php echo web_url('dish',array('op'=>'do_post')); ?>" method="post" enctype="multipart/form-data" class="tab-content form-horizontal" role="form" onsubmit="return fillform()">
<div class="panel with-nav-tabs panel-default">
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
					<!--两个分类-->
					<input type="hidden" name="pcate" id="pcate" value="<?php echo $_GP['p1'];?>">
					<input type="hidden" name="ccate" id="ccate" value="<?php echo $_GP['p2'];?>">
					<label class="col-sm-2 control-label no-padding-left" > 修改分类：</label>

					<div class="col-sm-9 edit_cate">
						<span>
							<?php echo $cat_name1['name'];?> > <?php echo $cat_name2['name'];?>
						</span>
						<a style="margin-left: 15px;" class="btn btn-xs btn-info" href="<?php echo web_url('dish',array('op'=>'post','id'=>$_GP['id'],'p1'=>$_GP['p1'],'p2'=>$_GP['p2'])); ?>">修改分类</a>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 宝贝名称</label>
					<div class="col-sm-4">
						<input type="text" name="title" id="dishname" class="form-control span7" maxlength="100" value="<?php  echo $item['title'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 品牌：</label>

					<div class="col-sm-2">
						<select name="brand" class="js-example-responsive choose_brand form-control" id="brand">
							<option value="0">请选择品牌</option>
							<?php foreach ( $brandlist as $brand_value ){ $selected = ($brand_value['id'] == $item['brand'])?"selected":"";?>
								<option <?php echo $selected; ?> value="<?php echo $brand_value['id']; ?>"><?php echo $brand_value['brand']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="cos-sm-2">
						<span class="btn btn-info btn-xs add_the_brand" style="margin-top: 3px;">添加品牌</span>
					</div>
				</div>
				 <div class="form-group" >
						<label class="col-sm-2 control-label no-padding-left" > 是否上架：</label>
						<div class="col-sm-9">
							<input type="radio" name="status" value="1" id="isshow1" <?php  if($item['status'] ==1 ) { ?>checked="true"<?php  } ?> /> 是  &nbsp;&nbsp;
							<input type="radio" name="status" value="0" id="isshow2"  <?php  if($item['status'] == 0) { ?>checked="true"<?php  } ?> /> 否
						</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 市场价格：</label>

					<div class="col-sm-2">
						<input type="number" class="form-control" name="productprice" id="productprice"  value="<?php  echo empty($item['productprice'])?'0':$item['productprice'];?>" />
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 促销价格：</label>

					<div class="col-sm-2">
						<input type="number" class="form-control" name="marketprice" id="marketprice" value="<?php  echo empty($item['marketprice'])?'0':$item['marketprice'];?>" />
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 排序：</label>

					<div class="col-sm-2">
						<input type="text" name="displayorder" class="form-control" id='displayorder' value="<?php  echo empty($item['displayorder'])?'0':$item['displayorder'];?>" />
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 库存：</label>
					<div class="col-sm-2">

						<input type="text" name="total" class="form-control" id="total" value="<?php  echo empty($item['total'])?'0':$item['total'];?>" />
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
						<input type="checkbox" name="isfirst" value="1"  <?php  if($item['isfirst'] == 1) { ?>checked="true"<?php  } ?> /> 广告
						<input type="checkbox" name="ishot" value="1"  <?php  if($item['ishot'] == 1) { ?>checked="true"<?php  } ?> /> 热卖
						<input type="checkbox" name="isjingping" value="1" <?php  if($item['isjingping'] == 1) { ?>checked="true"<?php  } ?> /> 精品
						<input type="checkbox" name="isdiscount" value="1" <?php  if($item['isdiscount'] == 1) { ?>checked="true"<?php  } ?> /> 活动
						&nbsp;
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" >商品类型:</label>
					<div class="col-sm-2">
						<select id="J_type" name="type" class="form-control">
							<option value="0" <?php if($item['type']==0){?>selected="selected"<?php  } ?>>一般商品</option>
							<option value="1" <?php if($item['type']==1){?>selected="selected"<?php  } ?> >团购商品</option>
							<option value="2" <?php if($item['type']==2){?>selected="selected"<?php  } ?>>秒杀商品</option>
							<option value="3" <?php if($item['type']==3){?>selected="selected"<?php  } ?>>今日特价商品</option>
							<option value="4" <?php if($item['type']==4){?>selected="selected"<?php  } ?>>限时促销</option>
						</select>
					</div>
				</div>



				<div class="form-group show_time_set" style="display: none">
					<div class="">
						<label class="col-sm-2 control-label no-padding-left" > 活动时间:</label>

						<div class="col-sm-4">
							<input type="hidden" name="istime" id='istime' value="<?php intval($item['istime']); ?>" />
							<input type="text" id="datepicker_timestart" name="timestart" value="<?php if(!empty($item['timestart'])){echo date('Y-m-d H:i',$item['timestart']);}?>" readonly="readonly" class="time_put"/>
							<script type="text/javascript">
								laydate({
									elem: '#datepicker_timestart',
									istime: true,
									event: 'click',
									format: 'YYYY-MM-DD hh:mm:ss',
									istoday: true, //是否显示今天
									start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
								});
								laydate.skin("molv");
							</script> -
							<input type="text"  id="datepicker_timeend" name="timeend" value="<?php if(!empty($item['timestart'])){echo date('Y-m-d H:i',$item['timeend']);}?>" readonly="readonly" class="time_put"/>
							<script type="text/javascript">
								laydate({
									elem: '#datepicker_timeend',
									istime: true,
									event: 'click',
									format: 'YYYY-MM-DD hh:mm:ss',
									istoday: true, //是否显示今天
									start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
								});
								laydate.skin("molv");
							</script>
						</div>
					</div>

					<br/>
					<br/>
					<div style="clear: both;margin-top: 5px;">
						<label class="col-sm-2 control-label no-padding-left" >活动价格:</label>
						<div class="col-sm-2">
							<input type="number" name="timeprice" id="timeprice" value="<?php  echo empty($item['timeprice'])?'0':$item['timeprice'];?>" class="form-control"/>
							<p class="help-block">该金额只在活动时间内有效，结束恢复促销价</p>
						</div>
					</div>
				</div>

				<div id="J_team_buy_count_div" class="form-group" <?php if($item['type']!=1){?>style="display:none;"<?php  } ?> >
					<label class="col-sm-2 control-label no-padding-left" >成团人数:</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" name="team_buy_count" id="J_team_buy_count" value="<?php echo empty($item['team_buy_count'])?'0':$item['team_buy_count'];?>" />
					</div>
				</div>

				<div id="J_team_buy_draw_div" class="form-group" <?php if($item['type']!=1){?>style="display:none;"<?php  } ?> >
					<label class="col-sm-2 control-label no-padding-left" > 开启抽奖：</label>
					<div class="col-sm-7">
						<input type="radio" name="draw" value="1" id="isdraw1" <?php  if($item['draw'] ==1 ) { ?>checked="true"<?php  } ?> /> 是  &nbsp;&nbsp;
						<input type="radio" name="draw" value="0" id="isdraw2"  <?php  if($item['draw'] == 0) { ?>checked="true"<?php  } ?> /> 否
					</div>
				</div>
				<div id="J_team_buy_draw_num_div" class="form-group" <?php if($item['draw']!=1){?>style="display:none;"<?php  } ?> >
					<label class="col-sm-2 control-label no-padding-left" > 抽奖人数：</label>
					<div class="col-sm-2">
						<input type="number" class="form-control" name="team_draw_num" id="J_team_draw_num" value="<?php echo empty($item['draw_num'])?'0':$item['draw_num'];?>" />
					</div>
				</div>



				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 商品佣金比例：</label>

					<div class="col-sm-2">
						<input type="number"  class="form-control" name="commision" id="commision" value="<?php  echo $item['commision'] == 0 ?'0':$item['commision']*100;?>" />
					</div>
					<div class="col-sm-2" style="margin-top: 3px;">
						% &nbsp;&nbsp;<span style="color: #737373" id="show_commision">佣金：<?php echo $item['commision']*$item['timeprice'];?>元</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 运费模板：</label>

					<div class="col-sm-2">
						<select  style="margin-right:15px;" id="transport_id" name="transport_id"  autocomplete="off" class="form-control">
							<option value="0">请选择一级分类</option>
							<?php foreach($disharea as $row) { ?>
								<?php  if($row['parentid'] == 0) { ?>
									<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['transport_id']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name']." [{$row['displayorder']}元]";?></option>
								<?php  } ?>
							<?php  }  ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" > 免运费：</label>

					<div class="col-sm-9">
						<input type="checkbox" name="issendfree" value="1" id="isnew" <?php  if($item['issendfree'] == 1) { ?>checked="true"<?php  } ?> /> 打勾表示此不会产生运费花销，否则按照正常运费计算。
						&nbsp;
					</div>
				</div>

            </div>
            
			<div class="tab-pane fade" id="tab2primary">
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" >宝贝主图：<br/></label>

					<div class="col-sm-9">
						<div class="fileupload fileupload-new" data-provides="fileupload">
							<div class="fileupload-preview thumbnail" style="width: 145px; height: 140px;">
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


				<div class="form-group" style="">
					<label class="col-sm-2 control-label no-padding-left" > 其他图片：</label>

					<div class="col-sm-9">
						<span id="selectimage" tabindex="-1" class="btn btn-primary"><i class="icon-plus"></i> 上传照片</span><span style="color:red;">

						<div id="file_upload-queue" class="uploadify-queue"></div>
						<ul class="ipost-list ui-sortable" id="fileList">
							<?php  if(is_array($piclist)) { foreach($piclist as $v) { ?>
								<li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;   width: 150px;  height: 145px;">
									<span class="item_box">
										<img src="<?php  echo $v['picurl'];?>" style="width:130px;height:125px">
									</span>
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
						<textarea  id="container" style="min-height: 500px;" name="content" ><?php  echo $item['content'];?></textarea>

					</div>
				</div>

			</div>
			<div class="tab-pane fade" id="tab4primary">
	    			<?php  include page('dish/goodtype');?>
			</div>
            
        </div>
        
        
    </div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" ></label>

		<div class="col-sm-9">
			<button type="submit" class="btn btn-primary btn-md span2" name="submit" value="submit"><i class="icon-edit"></i>全部保存</button>
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
								html =	'<li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;  width: 150px;  height: 145px;">'+
								'<span class="item_box"> <img src="'+list[i]['url']+'" style="width:130px;height:125px"></span>'+
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



	$('#J_type').change(function() {
		if(parseInt($(this).val())==0)
		{
			$('#istime').val(0);
			$('.show_time_set').hide();
			$('#J_team_buy_count_div').hide();
			$('#J_team_buy_draw_div').hide();
			$('#J_team_buy_draw_num_div').hide();
		} else{
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

			$('#istime').val(1);
			$('.show_time_set').show();
		}
	});
	$("[name='draw']").change(function() {
		if ($("[name='draw']:checked").val()==1) {
			$('#J_team_buy_draw_num_div').show();
		}else{
			$('#J_team_buy_draw_num_div').hide();
		}
	});

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

function fillform()
{
	if(ue.queryCommandState( 'source' )==1)
	{

		document.getElementById("container").value=ue.getContent();
	}else {
		document.getElementById("container").value=ue.body.innerHTML;
	}
	if($("#productprice").val() == '' || $("#productprice").val() == 0){
		alert('请设置市场价！');
		return false;
	}
	if($("#marketprice").val() == '' || $("#marketprice").val() == 0){
		alert('请设置促销价！');
		return false;
	}
	if($("#total").val() == 0){
		alert('请设置库存！');
		return false;
	}
	if($("#transport_id").val() == 0){
		alert('请选择运费模板！');
		return false;
	}

	if(parseInt($('#J_type').val())!=0 && ($('#datepicker_timestart').val()=='' || $('#datepicker_timeend').val()==''))
	{
		alert('请设置活动时间！');
		return false;
	}
	if($('.fileupload-preview img').length < 1){
		alert('请上传宝贝主图！');
		return false;
	}

	if($(".set_marketprice").val() == ''){
		alert('规格中的促销价不能为空！');
		return false;
	}
	if($(".set_productprice").val() == ''){
		alert('规格中的市场价不能为空！');
		return false;
	}
	if($(".set_total").val() == ''){
		alert('规格中的库存不能为空！');
		return false;
	}
	return true;
}


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

$(".add_the_brand").click(function(){
	var url ="<?php echo web_url('dish',array('op'=>'addbrand')); ?>";
	$.ajaxLoad(url,'',function(){
		$('#alterModal').modal('show');
	});
})
    </script>
<?php  include page('footer');?>
