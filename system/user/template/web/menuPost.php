<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
 <form action="<?php echo web_url('user',array('act'=>'postData','op'=>'menu'))?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
        <input type="hidden" name="id" value="<?php  echo $_GP['id'];?>" />
		<h3 class="header smaller lighter blue">
			<?php
			if(!empty($_GP['parent_id'])){
				echo "【{$parentMenu['moddescription']}】>";
			}
			if(empty($_GP['id'])){
				echo "新增菜单";
			}else{
				echo $editMenu['moddescription'];
			}?>
		</h3>

	 <p style="margin: 10px;background: #fcfcfc;border: 1px solid #e5e5e5;padding: 10px;color:red;font-size: 12px;">注：对于添加编辑删除的操作，最好选择上操作类型，统一规范。</p>

	 <?php if(empty($_GP['id'])){  ?>
	 <div class="form-group">
		 <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 选择分类：</label>

		 <div class="col-sm-9">
			 <select name="cat_id" id="" class="checkSelect">
				 <option value="0">选择分类</option>
				 <?php foreach(MenuEnum::$getMenuEnumValues as $key => $val){ ?>
					 <option value="<?php echo $key;?>"  <?php if($key == $editMenu['cat_id'] || $parentMenu['cat_id'] == $key || $key == $_GP['cat_id']){ echo "selected";} ?>><?php echo $val;?></option>
				 <?php } ?>
			 </select>
		 </div>
	 </div>
	<?php } ?>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 菜单名：</label>

			<div class="col-sm-9">
				 <input type="text" name="moddescription"  class="col-xs-10 col-sm-2" value="<?php echo $editMenu['moddescription']; ?>"/>
			</div>
		</div>

		  <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> modname：</label>

			<div class="col-sm-9">
				   <input type="text"  name="modname"  class="col-xs-10 col-sm-2" value="<?php if(!empty($editMenu)){ echo $editMenu['modname'];}else if(!empty($parentMenu)){ echo $parentMenu['modname'];} ?>"/>
			</div>
		</div>

		  <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> moddo：</label>

			<div class="col-sm-9">
				<input type="text"  name="moddo" class="col-xs-10 col-sm-2"  value="<?php if(!empty($editMenu)){echo $editMenu['moddo'];}else if(!empty($parentMenu)){ echo  $parentMenu['moddo'];} ?>"/>
			</div>
		</div>

		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> modop：</label>

			 <div class="col-sm-9">
				 <input type="text"  name="modop" class="col-xs-10 col-sm-2"  value="<?php echo $editMenu['modop']; ?>"/>
			 </div>
		 </div>

		<?php if(empty($_GP['id'])){ ?>
		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 上级菜单：</label>

			 <div class="col-sm-9">
				 <select name="pid" id="">
					 <option value="0">顶级菜单</option>
					 <?php foreach($menu as $row){ ?>
						<option value="<?php echo $row['id'];?>"  <?php if($row['id'] == $_GP['parent_id']){ echo "selected";} ?>><?php echo $row['moddescription'];?></option>
					 <?php } ?>
				 </select>
			 </div>
		 </div>
		<?php } ?>

		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 操作类型：</label>

			 <div class="col-sm-9">
				 <select name="act_type" id="">
					 <option value="0">选择操作</option>
					 <option value="add" <?php if($editMenu['act_type']=='add') echo "selected";?>>add</option>
					 <option value="edit" <?php if($editMenu['act_type']=='edit') echo "selected";?>>edit</option>
					 <option value="delete" <?php if($editMenu['act_type']=='delete') echo "selected";?>>delete</option>
				 </select>
			 </div>
		 </div>

		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 排序：</label>

			 <div class="col-sm-9">
				 <input type="text"  name="sort" class="col-xs-10 col-sm-2"  value="<?php echo $editMenu['sort']; ?>"/>
			 </div>
		 </div>


	  <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>
		  <input type="hidden" name="parent_id" value="<?php echo $_GP['parent_id'];?>">
			<div class="col-sm-9">
			<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
			<span class="btn btn-info" onclick="window.history.back(-1)">&nbsp;返&nbsp;回&nbsp;</span>
			</div>
		</div>

    </form>

<?php  include page('footer');?>

<script>
	$(function(){
		var parent_id = $("input[name='parent_id']").val();
		if(parent_id.length != 0){
			var cat_id = $(".checkSelect").val();
			var url = $("form").attr('action');
			url += "&cat_id="+cat_id;
			$("form").attr('action',url);
			$(".checkSelect").attr('disabled',true);
		}
	})
</script>
