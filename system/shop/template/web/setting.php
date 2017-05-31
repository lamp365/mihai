<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<style>
	.good_line_table{
		
		width:100%;
		}
	.choose_kefu span{margin-right: 10px;cursor: pointer}
	.nav-tabs li a{
		padding: 6px 15px;
	}
</style>
<br/>
<ul class="nav nav-tabs" >
	<li style="" <?php  if($_GP['op'] == 'index') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('config',  array('op' => 'index'))?>">基础设置</a></li>
	<li style="" <?php  if($_GP['op'] == 'generl') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('config',  array('op' => 'generl'))?>">佣金比例</a></li>
</ul>
<br/>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 商店名称：</label>

		<div class="col-sm-3">
			<input type="text" name="shop_title" class="form-control" value="<?php  echo $settings['shop_title'];?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 备案号：</label>
		<div class="col-sm-3">
			<input type="text" name="shop_icp" class="form-control" value="<?php  echo $settings['shop_icp'];?>" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 注册赠送积分：</label>
		<div class="col-sm-3">
			<input type="number" name="shop_regcredit" class="form-control" value="<?php  echo $settings['shop_regcredit'];?>" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 商店描述：</label>

		<div class="col-sm-5">
			<input type="text" name="shop_description" class="form-control" value="<?php  echo $settings['shop_description'];?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 商店关键字：</label>

		<div class="col-sm-5">
			<input type="text" name="shop_keyword" class="form-control" value="<?php  echo $settings['shop_keyword'];?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 商店 Logo：<br/>(建议160*30)</label>

		<div class="col-sm-9">

			<div class="fileupload fileupload-new" data-provides="fileupload">
				<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
					<?php  if(!empty($settings['shop_logo'])) { ?>
						<img style="width:100%" src="<?php  echo $settings['shop_logo'];?>" alt="" onerror="$(this).remove();">
					<?php  } ?>
				</div>
				<div>
					<input name="shop_logo" id="shop_logo" type="file"  />
					<a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
				</div>
			</div>

		</div>
	</div>


	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 是否开启注册：</label>

		<div class="col-sm-9">
			<input type="radio" name="shop_openreg" value="0" id="shop_closereg" <?php  if($settings['shop_openreg'] == 0) { ?>checked="true"<?php  } ?> /> 关闭  &nbsp;&nbsp;

			<input type="radio" name="shop_openreg" value="1" id="shop_closereg"  <?php  if($settings['shop_openreg'] == 1) { ?>checked="true"<?php  } ?> /> 开启

		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 调用第三方统计代码</label>

		<div class="col-sm-4">
			<textarea name="shop_tongjicode"  cols="60" rows="8" class="form-control"><?php  echo $settings['shop_tongjicode'];?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >客服电话：</label>

		<div class="col-sm-3">
			<input type="text" name="shop_tel" class="form-control" value="<?php  echo $settings['shop_tel'];?>" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >地址：</label>

		<div class="col-sm-3">
			<input type="text" name="shop_address" class="form-control" value="<?php  echo $settings['shop_address'];?>" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >客服QQ：</label>

		<div class="col-sm-5">
			<div class="show_kefu" style="display: block;overflow: hidden;width: 100%;">
				<div style="float: left;width: 60%">
					<input type="text" class="form-control" value="" placeholder="请输入QQ">
				</div>
				<div style="float: left;width: 35%;margin-top: 5px;">
					&nbsp;&nbsp;<span class="btn btn-xs btn-info sure_qq">确定</span>&nbsp;&nbsp;双击QQ可删除
				</div>
			</div>
			<div class="choose_kefu" style="display: block;overflow: hidden;width: 100%;margin-top: 10px;">
				<?php if(!empty($qq_info)){ ?>
					<?php foreach($qq_info as $qq => $num){ ?>
						<input type="checkbox" <?php if($num ==1){ echo "checked";}?> class="each_kefu"/><span class="remove_qq"><?php echo $qq;?></span>
					<?php }} ?>
			</div>
			<input name="shop_kfcode"  type="hidden" value='<?php  echo $settings['shop_kfcode'];?>'/>
		</div>
	</div>


	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 帮助说明：</label>

		<div class="col-sm-9">
			<textarea name="help" id="help" cols="60" rows="8" class="form-control"><?php  echo $settings['help'];?></textarea>
		</div>
	</div>




	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

		<div class="col-sm-9">
			<br/><input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>

		</div>
	</div>
				
</form>

		
<script>


			KindEditor.ready(function(K) {
				var editor;
			
					if (editor) {
						editor.remove();
						editor = null;
					}
					editor = K.create('textarea[name="help"]', {
						allowFileManager : false,
						height:'400px',
						 filterMode: false,
						 
						 formatUploadUrl:false,
						uploadJson : "<?php echo WEBSITE_ROOT.mobile_url('keupload');?>",
						newlineTag : 'br',
					items : [
						'source','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
						'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'emoticons', 'image',  'multiimage','insertfile','link']
					});
			
				
			});


$(document).delegate(".sure_qq",'click',function(){
	var qq = $(this).closest('.show_kefu').find('input').val();
	if(qq.length <= 0){
		alert('不能为空！');return;
	}
	if(isNaN(qq)){
		alert('请输入QQ数字！');
		return;
	}
	if(!check_this_qq(qq)){
		alert('该QQ已经存在');
		return;
	}
	var html='<input type="checkbox" class="each_kefu" checked/><span class="remove_qq">'+qq+'</span>';
	$(html).appendTo($('.choose_kefu'));
	count_kefu();
})

function count_kefu(){
	$("input[name='shop_kfcode']").val('');
	var data ={};
	$(".choose_kefu .each_kefu").each(function(){
		var qq = $(this).next().html();
		if(this.checked){
			data[qq] = 1;
		}else{
			data[qq] = 0;
		}
	})

	if(!$.isEmptyObject(data)) {
		var str = JSON.stringify(data);
		$("input[name='shop_kfcode']").val(str);
	}else
		$("input[name='shop_kfcode']").val('');
}
function check_this_qq(qq){
	var iscontinue = true;
	$(".choose_kefu .each_kefu").each(function(){
		var qq2 = $(this).next().html();
		if(qq2 == qq){
			iscontinue = false;
		}
	})
	return iscontinue;
}
$(document).delegate('.remove_qq','dblclick',function(){
	$(this).prev().remove();
	$(this).remove();
	count_kefu();
})
$(document).delegate('.each_kefu','click',function(){
	count_kefu();
})
</script>

<?php  include page('footer');?>