<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.css" />
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/zh-CN.js"></script>
<script src="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.js" type="text/javascript" charset="utf-8"></script>
	<style>
		.modal-body i{
			color: red;
		}
		.vip-table-list{
			width: 100%;
			border:1px solid #ddd;
		}
		.vip-table-list li{
			margin:7px 5px;
			float: left;
			list-style: none;
		}
		.vip-table-list .li-height{
		    height: 30px;
		    padding-left: 5px;
		}
		.vip-table-list li select{
			height: 30px;
		}
		.vip-table-list td{
			padding: 0!important;
		}
		.left-span{
			float: left;
		    line-height: 28px;
		    background-color: #ededed;
		    padding: 0 5px;
		    border: 1px solid #cdcdcd;
		    border-right: 0;
		    font-size: 12px;
		}
		.hide-tr{
			display: none;
		}
		.the_box{
			display: none;
		}
		.s_upload{
		height: 24px;
		width: 47px;
		border-radius: 3px;
		color: #ffffff;
		display: block;
		text-align: center;
		cursor: pointer;
		line-height: 24px;
		background-color: #31b0d5;
    	border-color: #269abc;
	}
	.upload_pic{width: 90px;height: 90px;float: left;margin-right:6px;border: 1px solid #F1F1F1;padding: 1px;background: #ffffff;position: relative}
	.upload_pic img{width: 88px;height: 88px;}
	.upload_button_close{
		position: absolute;
		top: -8px;
		right: -8px;
		width: 17px;
		height: 17px;
		background: url('images/close_icon.png') no-repeat -25px 0;
		cursor: pointer;
	}
	.webuploader-pick{
		height: 24px;
	}
	.tab-up,.tab-down{
		width: 16px;
		margin-left: 10px;
	}
	.tab-up{
		display: none;
	}
	.js-data-example-ajax{
		padding-right: 20px;
	}
	.select2-container--default .select2-selection--single{
		border-radius: 0;
		height: 30px;
	}
	</style>
<h3 class="header smaller lighter blue" style="display: inline-block">会员管理</h3> &nbsp; &nbsp;<a href="javascript:;" style="margin-top: -10px;" data-toggle="modal" data-target="#addModal" class="btn btn-md btn-info">添加会员</a>
<form action="" method="get" class="form-horizontal form-table" enctype="multipart/form-data" >
				<input type="hidden" name="act" value="module" />
				<input type="hidden" name="name" value="member" />
				<input type="hidden" name="do" value="list" />
				<input type="hidden" name="mod" value="site"/>					
				<input type="hidden" name="status" value="<?php echo isset($_GP['status'])? $_GP['status']: 1;?>"/>
				<input type="hidden" value="0" class="hidden-up-down" name="upDown">
				<table class="table vip-table-list"  align="center">
					<tbody>
						<tr>
							<td>
								<li>
									<span class="left-span">用户名</span>
									<input name="realname" class="li-height" placeholder="用户名" type="text" value="<?php  echo $_GP['realname'];?>" />
								</li>
								<li>
									<span class="left-span">手机号码</span>
									<input name="mobile" class="li-height" type="text" placeholder="手机号码" value="<?php  echo $_GP['mobile'];?>" />
								</li>
								<li >
									<span class="left-span">状态</span>
									<select  name="showstatus">
			 							<option value="1" <?php if(empty($_GP['showstatus'])||$_GP['showstatus']==1){?>selected=""<?php }?>>正常</option>
					                 	<option value="-1" <?php if($_GP['showstatus']==-1){?>selected=""<?php }?>>禁用</option>
		              	            </select>
								</li>
								<li>
									<span class="left-span">城市</span>
									<select name="rank_level">
		     							<option value="0">选择城市</option>   
		     							<?php foreach($rank_model_list as $rank_model){?>
		     				  				<option value="<?php echo $rank_model['rank_level']?>" <?php if($rank_model['rank_level']==$_GP['rank_level']){?>selected=""<?php }?>><?php echo $rank_model['rank_name']?></option> 
		     							<?php }?>
	     							</select>
								</li>
								<li >
									<span class="left-span">微信昵称</span>
									<input name="weixinname" class="li-height" placeholder="微信昵称" type="text" value="<?php  echo $_GP['weixinname'];?>" />
								</li>
								<li>
									<input type="hidden" value="<?php echo $storeInfo['sts_name'];?>" id="brand_name" name="brand_name">
									<span class="left-span">店铺名称</span>
									<select class="js-data-example-ajax" name="sts_id" id="sts_id">
                                        <option value="<?php echo $storeInfo['id'];?>" selected="selected"><?php if ($storeInfo) {echo $storeInfo['sts_name'];}else{ echo "请选择店铺名称";}?></option>
                                    </select>
								</li>
								<li style="display: none;"><span>按食堂筛选：</span></li>
								<li style="display: none;">
									<select style="margin-right:15px;" id="mess" name="mess" >
			 							<option value="" <?php  echo empty($_GP['dispatch'])?'selected':'';?>>--未选择--</option>
										<?php  if(is_array($_mess)) { foreach($_mess as $item) { ?>
	                 					<option value="<?php  echo $item["id"];?>" <?php  echo $item['id']==$_GP['mess']?'selected':'';?>><?php  echo $item['title']?></option>
				                  		<?php  } } ?>
				                   	</select>
								</li>
								<li>
									<div class="btn-group">
									  <input type="submit" name="submit" value=" 查 询 "  class="btn btn-primary btn-sm">
									  <button type="button" class="btn btn-primary btn-sm dropdown-toggle add-more-btn" data-toggle="dropdown">
									    <span class="caret"></span>
									    <span class="sr-only">Toggle Dropdown</span>
									  </button>
									</div>
								</li>
							</td>
						</tr>
						<tr class="hide-tr">
							<td>
								<li>
									<span class="left-span">起始日期</span>
									<input type="text" class="li-height" placeholder="起始日期" id="datepicker_timestart" name="timestart" value="<?php echo $_GP['timestart']; ?>" readonly="readonly" />
								</li>
								<li> - </li>
								<li>
									<span class="left-span">终止日期</span>
									<input type="text" class="li-height" placeholder="终止日期" id="datepicker_timeend" name="timeend" value="<?php echo $_GP['timeend']; ?>" readonly="readonly" />
									<script type="text/javascript">
										laydate({
									        elem: '#datepicker_timestart',
									        istime: true, 
									        event: 'click',
									        format: 'YYYY-MM-DD hh:mm:ss',
									        istoday: true, //是否显示今天
									        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
									    });
										laydate({
									        elem: '#datepicker_timeend',
									        istime: true, 
									        event: 'click',
									        format: 'YYYY-MM-DD hh:mm:ss',
									        istoday: true, //是否显示今天
									        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
									    });
									    laydate.skin("molv");
									    $(".add-more-btn").click(function(){
											$(".hide-tr").toggle();
										});
									</script>
								</li>
								
							</td>
						</tr>
					</tbody>
				</table>
			</form>

<h3 class="blue">	<span style="font-size:18px;"><strong>会员总数：<?php echo $total ?></strong></span></h3>
		<ul class="nav nav-tabs" >
	<li style="width:7%" <?php  if($vc == 1) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'member','do'=>'list','status' => 1))?>">商城会员</a></li>
	<li style="width:7%" <?php  if($vc == 2) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'member','do'=>'list','status' => 2))?>">禁用会员</a></li>
<!-- 	<li style="width:7%" <?php  if($vc == -1) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'member','do'=>'list','status' => -1))?>">全部会员</a></li> -->
			</ul>
			
			<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th style="text-align:center;">手机号码</th>
						<th style="text-align:center;">微信昵称</th>
						<th style="text-align:center;">店铺名称</th>
					<th style="text-align:center;">用户名</th>
					<th style="text-align:center;">注册时间</th>
					<th style="text-align:center;">会员等级</th>
					<th style="text-align:center;">状态</th>
					<th style="text-align:center;" class="td-price down">消费金额
					<img class="tab-up" src="<?php echo RESOURCE_ROOT;?>/addons/common/image/up.png">
					<img class="tab-down" src="<?php echo RESOURCE_ROOT;?>/addons/common/image/down.png">
					</th>

					<th style="text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
 <?php  if(is_array($list)) { 
	 foreach($list as $v) { ?>
								<tr>
										<td class="text-center">
										<?php  echo $v['mobile'];?>
									</td>
											<td class="text-center">
											<?php foreach($v['weixin'] as $wxfans) { ?>
						<?php echo $wxfans['nickname']; ?><br/>
						<?php  }?>
									</td>
										<td class="text-center">
										<?php echo $v['sts_name'];?>
									</td>
									<td class="text-center">
										<?php  echo $v['realname'];?>
									</td>
								
									<td class="text-center">
										<?php  echo date('Y-m-d H:i',$v['createtime'])?>
									</td>
									
											<td class="text-center">
												<?php   $member_rank_model=member_rank_model($v['experience']);if(empty($member_rank_model)){ echo '无';}else{echo $member_rank_model['rank_name'];}?>
									</td>
									<td class="text-center">
									<?php  if($v['status']==0) { ?>
										<span class="label label-important">已禁用</span>
									<?php  } else { ?>
										<span class="label label-success">正常</span>
									<?php  } ?>
									</td>
										<td class="text-center">
												<?php  echo $v['credit'];?>
									</td>

									<td class="text-center">
											<?php  if($v['status']==1) { ?>
									<a class="btn btn-xs btn-danger" href="<?php  echo web_url('delete',array('name'=>'member','openid' => $v['openid'],'status' => 0));?>" onclick="return confirm('确定要禁用该账户吗？');"><i class="icon-edit"></i>禁用账户</a>
										
											<?php  } else { ?>
										<a class="btn btn-xs btn-success" href="<?php  echo web_url('delete',array('name'=>'member','openid' => $v['openid'],'status' => 1));?>" onclick="return confirm('确定要恢复该账户吗？');"><i class="icon-edit"></i>恢复账户</a>
										
									<?php  } ?>
										&nbsp;<a  class="btn btn-xs btn-info" href="<?php  echo web_url('detail',array('name'=>'member','openid' => $v['openid']));?>"><i class="icon-edit"></i>账户编辑</a>&nbsp;
<!-- 										<a class="btn btn-xs btn-info" href="<?php  echo web_url('recharge',array('name'=>'member','openid' => $v['openid'],'op'=>'credit'));?>"><i class="icon-edit"></i>积分管理</a>&nbsp;
										<a class="btn btn-xs btn-info" href="<?php  echo web_url('recharge',array('name'=>'member','openid' => $v['openid'],'op'=>'gold'));?>"><i class="icon-edit"></i>余额管理</a>	 -->
									</td>
								</tr>
								<?php  } } ?>
  </tbody>
    </table>
		<?php  echo $pager;?>

	<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form action="<?php  echo web_url('purchase',array('op'=>'add'));?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">添加会员</h4>
					</div>
					<div class="modal-body" style="overflow: hidden">
							<table class="table" style="width:100%;" align="left">
								<tbody>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px"><i>*</i>手机号码：</td>
									<td>
										<input name="mobile" type="text"   value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px">用户名：</td>
									<td style="width:300px">
										<input name="realname"  type="text" value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px"><i>*</i>密码：</td>
									<td style="width:300px">
										<input name="pwd"  type="password" value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">email：</td>
									<td>
										<input name="email" type="text"   value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">分配业务员：</td>
									<td>
										<select name="relation_uid" class="purchase_roler_id" style="width: 175px;height: 30px;line-height: 28px;" onchange="show_box()">
											<option value="0">请选择</option>
											<?php
											if(!empty($user_rolers)){
												foreach($user_rolers as $row){
													echo "<option value='{$row['uid']}'>{$row['username']}</option>";
												}
											}
											?>
										</select>
									</td>
								</tr>
								<tr class="the_box">
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">会员身份：</td>
									<td >
										<select name="parent_roler_id" style="width: 175px;height: 30px;line-height: 28px;" onchange="fetchChild(this,this.options[this.selectedIndex].value)">
											<option value="0">请选择</option>
											<?php
											if(!empty($purchase)){
												foreach($purchase as $row){
													echo "<option value='{$row['id']}'>{$row['name']}</option>";
												}
											}
											?>
										</select>
										<select name="son_roler_id" style="width: 175px;height: 30px;line-height: 28px;" class="child_choose">
											<option value="0">请选择</option>
										</select>
									</td>
								</tr>
								<tr class="the_box">
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">平台名称：</td>
									<td>
										<input name="platform_name" type="text"   value="" />
									</td>
								</tr>
								<tr class="the_box">
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">平台链接：</td>
									<td>
										<input name="platform_url" type="text"   value="" />
									</td>
								</tr>
								<tr class="the_box">
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">平台主图：</td>
									<td>
										<div class="show_pic">
											<span class="s_upload">上传</span>
										</div>
										<div class="show_pic_list"></div>
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">QQ：</td>
									<td>
										<input name="QQ" type="text"   value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">微信：</td>
									<td>
										<input name="weixin" type="text"   value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">旺旺：</td>
									<td>
										<input name="wanwan" type="text"   value="" />
									</td>
								</tr>
								</tbody>
							</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认添加</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal -->
		</form>
	</div>
	<script>
	var uploader = WebUploader.create({

	// 选完文件后，是否自动上传。
	auto: true,

	swf: '__RESOURCE__/recouse/js/webuploader/Uploader.swf',

	// 文件接收服务端。
	server: 'fileupload.php?savelocal=0',

	// 选择文件的按钮。可选。
	// 内部根据当前运行是创建，可能是input元素，也可能是flash.

	pick: '.show_pic',

	//可以重复上传
	duplicate: true,

	// 只允许选择图片文件。
	accept: {
		title: 'Images',
		extensions: 'gif,jpg,jpeg,bmp,png',
		mimeTypes: 'image/jpg,image/jpeg,image/png'
	}
});
// 当有文件被添加进队列的时候
uploader.on( 'fileQueued', function( file ) {
	uploader.makeThumb(file, function(error, src) {
		if(error) {
			alter('不能预览图片',1);
			return;
		}
	}, 50, 50);
});
// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader.on('uploadSuccess', function(file, response) {
	var data = eval("(" +response._raw+ ")");
	if(data.hasOwnProperty('error')){
		alter('上传失败',1);
	}else{
		var html = "<div class='upload_pic'>"+
			"<input type='hidden' name='picurl[]' value='"+ data.name +"'>"+
			"<img src='"+data.name+"' /><span class='upload_button_close' title='删除图' onclick='del(this);'></span>"+
			"</div>";
		$('.show_pic_list').append(html);

		$('#' + file.id).addClass('upload-state-done');
	}

});
// 文件上传失败，显示上传出错。
uploader.on('uploadError', function(file) {
	alter('上传失败',1);
});
		var purchase = <?php echo json_encode($childrens);?>;
		function fetchChild(o_obj,pid){
			var html = '<option value="0">请选择</option>';
			if (!purchase || !purchase[pid]) {
				$(o_obj).parent().find('.child_choose').html(html);
				return false;
			}

			var html = '';
			for (i in purchase[pid]) {
				html += '<option value="'+purchase[pid][i]['id']+'">'+purchase[pid][i]['name']+'</option>';
			}
			$(o_obj).parent().find('.child_choose').html(html);
		}

		function show_box(num){
			if(!num){
				var val = $(".purchase_roler_id option:selected").val();
			}else{
				var val = num;
			}
			if(val == 0){
				$(".the_box").hide();
			}else{
				$(".the_box").show();
			}
		}


function del(ele){
	$(ele).parent('.upload_pic').remove();
}
// 消费金额排序
$(".td-price").on("click",function(){
	if($(this).hasClass("down")){
		$(this).removeClass("down");
		$(".tab-down").hide();
		$(".tab-up").show();
		$(".hidden-up-down").val(1);
		$("form").submit();
	}else{
		$(this).addClass("down");
		$(".tab-down").show();
		$(".tab-up").hide();
		$(".hidden-up-down").val(0);
		$("form").submit();
	}
})
	</script>
	<script>
		//select2下拉框初始化
	    $(".js-data-example-ajax").select2({
	        placeholder: '请选择店铺名称',
	        language: 'zh-CN',
	        allowClear: true,
	        ajax: {
	          url: "<?php echo web_url('list', array('op' => 'store_search'));?>",
	          dataType: 'json',
	          delay: 250,
	          data: function (params) {
	            return {
	              sts_name: params.term,// search term
	              page: params.page
	            };
	          },
	          processResults: function (data, params) {
	            params.page = params.page || 1;
	            return {
	              results: data.store,
	              pagination: {
	               more: (params.page * 30) < data.total_count
	              }
	            };
	          },
	          cache: true
	        },
	        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
			minimumInputLength: 1,
			templateResult: formatRepoDefault, // omitted for brevity, see the source of this page
        	templateSelection: formatRepoProvince // omitted for brevity, see the source of this page
	    });
		function formatRepoDefault(repo){
			if($('#brand_name').val() != ''){
                var markup = "<div>"+$('#brand_name').val()+"</div>";
            }else{
                var markup = "<div>请选择店铺名称</div>";
            }
	        return repo.sts_name;
	    }

	    function formatRepoProvince(repo) {
	    	console.log($('#brand_name').val())
	    	if(repo.sts_name != undefined){
            	var markup = "<div>"+repo.sts_name+"</div>";
        	}else{
            	if($('#brand_name').val() != ''){

                	var markup = "<div>"+$('#brand_name').val()+"</div>";
	            }else{
	                var markup = "<div>请选择店铺名称</div>";
	            }
        	}
        	return markup;
	    }
			</script>
<?php  include page('footer');?>