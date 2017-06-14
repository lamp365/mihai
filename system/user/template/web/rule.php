<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		.show_roles .each_role{
			display: inline-block;
			margin-right:10px;
		}
		.show_roles>div{
			line-height:30px;
		}
		.show_roles .role_title{
			//border-bottom: 1px solid #f5f2e5;
		    float: left;
		    width: 150px;
		}
		.show_roles{
			border-bottom: 1px solid #f5f2e5;
		}
		.field{max-height: 320px;overflow: hidden;}
		.field>div{
			border: 1px solid #e5e5e5;
			border-radius: 3px;
			overflow-y: auto;
			max-height: 300px;
		}
		.field .tit{
			padding-left: 15px;
			background: #F9F9F9;
			border-bottom:1px solid  #e5e5e5;
			height:28px;
			line-height:28px;
		}
		.field p{
			border-bottom:1px solid  #e5e5e5;
			height:28px;
			line-height:28px;
			padding-left:15px;
			margin-bottom:0px;
			cursor: pointer;
		}
		.field p:hover{
			background: #F9F9F9;
		}
		.z_none{
			height:28px;
			line-height:28px;
			padding-left:15px;
		}
		.pre_level{
			float: left;
			overflow: auto;
		}

		.showlist{
			overflow: hidden;
		}
		.second-nav{
			float: left;
		}
		.third-nav{
			float: left;
		}
		.levle-area{
			float:left;
			max-width: 1537px;
		}
		.pre_level input,.role_title input{
			margin-right: 5px;
			margin-top: -2px;
			vertical-align: middle;
		}
		label{
			font-weight: 400!important;
		}
		.second-nav label{
			width: 250px;
		}
		.third-nav label{
			float: left;
			width: 250px;
		}
	</style>
    <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" >
    	<input type="hidden" value="<?php echo $id ?>"  name="id"  />
		<h3 class="header smaller lighter blue">权限设置</h3>
		<p class="alert alert-info" ><i class="icon-lightbulb"></i>注：权限这里是逆向思维，打钩后，表示不允许被操作的。</p>

		<div class="form-group">
			<div class="col-sm-12 control-label no-padding-left" style="text-align: left"> 
				 角色名：<?php echo $roler_name; ?>
				 权限：<strong><a href="javascript:;" onclick="checkrule(true)">全选</a>，<a href="javascript:;"  onclick="checkrule(false)">全否</a></strong> &nbsp;&nbsp;&nbsp;&nbsp;<span onclick="getAjaxFiledData()" class="btn btn-sm btn-info">高级权限</span>
				 <input name="submit" type="submit" value=" 提 交 " class="btn btn-sm btn-info tijiao"/>
			</div>

		</div>
									
									  <div class="form-group">
										

										  <div class="col-sm-12 " >
											  

											  <?php  foreach($parent as $cat_id => $arr){ ?>
											  <div class="showlist show_roles">
												  <div class="role_title"><label><input type="checkbox" class="cat"><strong><?php echo $arr[0]['cat_name']?></strong></label></div>
												  <div class="levle-area">
												  <?php  foreach($arr as $row){ ?>
													  
														  <div class="pre_level">
															  <div class="second-nav"><label><input type="checkbox" value="<?php echo $row['id'];?>" class="parent son" <?php if(!empty($row['check'])){ echo "checked='checked'";} ?> name="role_ids[]" ><?php echo $row['moddescription'];?></label></div>

														  <?php if(is_array($children[$row['id']])){ ?>
															  <div class="third-nav">
																  <?php foreach($children[$row['id']] as $val){ ?>
																	  <label><input type="checkbox" class="son" <?php if(!empty($val['check'])){ echo "checked='checked'";} ?> value="<?php echo $val['id'];?>" name="role_ids[]"><span class="each_role"><?php echo $row['moddescription'];?>--<?php echo $val['moddescription'];?></span></label>
																  <?php } ?>
															  </div>
														  <?php } ?>
														  </div>
													  
												  <?php } ?>
												  </div>
											  </div>
											  <?php } ?>
											  <script>
											  function checkrule(ischecked)
											  {
												  $("input[type='checkbox']").each(function(){
													  this.checked = ischecked;
												  })
											  }

											  $(function(){
												  $(".cat").each(function(){
													  var obj = this;
													  $(this).closest(".showlist").find(".son").each(function(){
														  if(this.checked){
															  obj.checked = true;
														  }
													  })
												  })

												  $(".son").click(function(){
													  if(this.checked){
														  var obj = $(this).closest('.pre_level').find(".parent")[0];
														  var obj2 = $(this).closest('.show_roles').find('.cat')[0];
														  obj.checked = true;
														  obj2.checked = true;
													  }
												  })
												  $(".parent").click(function(){
													  if(this.checked){
														  var obj2 = $(this).closest('.show_roles').find('.cat')[0];
														  obj2.checked = true;
													  }
													  var isCheck = this.checked;
													  $(this).closest(".pre_level").find('.son').each(function(){
														  this.checked = isCheck;
													  })
												  })
												  $(".cat").click(function(){
													  var isCheck = this.checked;
													  $(this).closest('.show_roles').find("input[type='checkbox']").each(function(){
														  this.checked = isCheck;
													  })
												  })

											  })
											  </script>
										</div>
									</div>
									
								
		<input type="hidden" class="hide_total_filed" value='<?php echo $DbFiledListJson;?>'>
		<input type="hidden" class="hide_user_filed" name="hide_user_filed" value='<?php echo $userHasDbRuleJson;?>'>

    </form>


	<!-- 模态框（Modal） -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title" id="myModalLabel">高级权限</h3>
				</div>
				<div class="modal-body">
					<select name="" class="DbFiledList" onchange="getFiledInfo(this)">
						<option value="0">请选择模型</option>
						<?php foreach($DbFiledList as $table=>$data){ ?>
							<option value="<?php echo $table;?>"><?php echo MenuEnum::$dbFiledValue[$table];?></option>
						<?php } ?>
					</select>
					<div class="field" style="margin-top: 15px;">
						<div class="pull-left" style="width: 45%">
							<div class="tit">模型属性</div>
							<div class="z_none">暂无</div>
						</div>
						<div class="pull-right" style="width: 45%">
							<div class="tit"><span style="font-weight: bolder">限制</span>操作属性</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					<button type="button" class="btn btn-primary field_sure" >确定</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal -->
	</div>

<?php  include page('footer');?>

<script>
	function getFiledInfo(obj){
		var table = $(obj).val();
		var hide_total_filed = $(".hide_total_filed").val();
		hide_total_filed = eval("("+ hide_total_filed +")");

		var html = '';
		var fieldArr = '';
		if(table == 0){
			html = '<div class="z_none">暂无</div>';
		}else{
			for(var key in hide_total_filed){
				if(key == table){
					fieldArr = hide_total_filed[key];
				}
			}
			for(var keyFiled in fieldArr){
				html += "<p data-field='"+ keyFiled +"'>"+ fieldArr[keyFiled] +"</p>"
			}
		}

		$(".field .pull-left .z_none").remove();
		$(".field .pull-left p").remove();
		$(html).appendTo($(".field .pull-left"))
	}

	function getAjaxFiledData(){
		//先恢复右侧已经确定好的字段数据
		var hide_user_filed  = $(".hide_user_filed").val();
		var hide_total_filed = $(".hide_total_filed").val();
		hide_user_filed      = hide_user_filed == '' ? '' : eval("("+ hide_user_filed +")");
		hide_total_filed     = eval("("+ hide_total_filed +")");
		var html = '';
		var valStr = {shop_dish:'出售中宝贝',shop_goods:'产品库管理'};
		if(hide_user_filed != ''){
			for(var table in hide_user_filed){
				if(hide_user_filed[table] != ''){
					for(var key in hide_user_filed[table]){
						var keyFile = hide_user_filed[table][key];
						var str      = valStr[table]+"|"+hide_total_filed[table][keyFile];
						var strField = table+"|"+keyFile;
						html += "<p data-field='"+ strField +"'>"+ str +"</p>"
					}
				}
			}
		}
		html = html == ''? '<div class="z_none">暂无</div>' : html;
		$(".field .pull-right .z_none").remove();
		$(".field .pull-right p").remove();
		$(html).appendTo($(".field .pull-right"));
		$("#myModal").modal('show');
	}

	$(document).delegate(".field .pull-left p","click",function(){
		var table    = $(".DbFiledList").find("option:selected").val();
		var text     = $(".DbFiledList").find("option:selected").text();
		var str      = text+"|" + $(this).html();
		var strField = table+"|" + $(this).data('field');
		var isContinue = true;
		$(".field .pull-right p").each(function(){   //已经存在的不用再次添加
			if($(this).html() == str){
				isContinue = false;
			}
		});
		if(!isContinue){
			return;
		}
		var html = "<p data-field='"+ strField +"'>"+ str + "</p>";
		$(".field .pull-right .z_none").remove();
		$(html).appendTo($(".field .pull-right"));
	})
	$(document).delegate(".field .pull-right p",'click',function(){
		$(this).remove();
	})

	$(".field_sure").click(function(){
		var info = {};
		$(".DbFiledList").find("option").each(function(){
			if($(this).val() != 0){
				var table = $(this).val().replace('squdian_','');
				info[table] = [];
			}
		})

		if($(".field .pull-right p").length > 0){
			$(".field .pull-right p").each(function(){
				var htmlArr = $(this).data('field').split('|');
				var table = htmlArr[0];
				var filed = htmlArr[1];
				info[table].push(filed);

			})
		}

		info_string = JSON.stringify(info);
		$("input[name='hide_user_filed']").val(info_string);
		var id='<?php echo $id ?>';
		var url = "<?php echo web_url('user',array('op'=>'rule_field'));?>"
		url = url +"&id="+id;
		$.post(url,info,function(data){
			$("#myModal").modal('hide');
			alert(data.message);
			window.location.reload();
		},'json')
	})

	$(".gundon div:first").click(function(){
		parent.scrollTo(0,0);
	})
	$(".gundon div:last").click(function(){
		parent.animate({scrollTop:$('.tijiao').offset().top});
	})
</script>
