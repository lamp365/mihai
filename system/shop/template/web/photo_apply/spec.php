<?php  include page('header');?>
<style type="text/css">
	.form-group{
		overflow: hidden;
	}
	.no-padding-left{
		text-align: right;
	}
	.fileupload-exists{
		margin-top: 20px;
	}
	.template{
		
	}
	.template-list{
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 15px 15px 0 15px;
	}
	.template-list ul{
		margin: 0;
		padding: 0;
		overflow: hidden;
	}
	.template-list ul li{
		position: relative;
		cursor: pointer;
		float: left;
		margin-right: 15px;
		margin-bottom: 15px;
	}
	.template-list ul img{
		width: 200px;
    	height: 300px;
	}
	.template-list ul .havecheck{
		position: absolute;
		top: 5px;
		right: 5px;
		width: 20px;
		height: 20px;
	}
	.new-upload{
		display: none;
	}
	.template1 ul img{
		width: 66px;
    	height: 100px;
	}
	.template2 ul img{
		width: 100px;
    	height: 150px;
	}
</style>
<h3 class="header smaller lighter blue" style="margin-bottom: 30px; padding-left: 50px;">型号选择</h3>
<form class="" method="post" action="<?php  echo web_url('photo_apply', array('op' => 'spec_sub'))?>" name="myform" id="myform">
        <?php
          foreach($list as $k=>$v){
              $picList = array();
              $picList = explode(',', $v['ms_type_url']);
        ?>
    <input type="hidden" name="ms[<?php echo $v['ms_type_id'];?>][ms_type]" value="<?php echo $v['ms_type'];?>">
    <input type="hidden" name="ms[<?php echo $v['ms_type_id'];?>][ms_type_id]" value="<?php echo $v['ms_type_id'];?>">
	<div class="form-group">
		<div class="checkbox col-sm-1 no-padding-left">
		    <label>
                        <input type="checkbox" name="ms[<?php echo $v['ms_type_id'];?>][ms_type_radio]" <?php if($materialManagementInfo['detialArray'][$v['ms_type_id']]['ms_type_radio'] == 'on'){echo 'checked';}?>> <?php echo $v['ms_type'];?>
		    </label>
	  	</div>
		<div class="col-sm-2">
                    <input type="text" name="ms[<?php echo $v['ms_type_id'];?>][ms_nums]" class="form-control" placeholder="请输入物料数量" value="<?php echo $materialManagementInfo['detialArray'][$v['ms_type_id']]['ms_nums'];?>">
		</div>
	</div>
	
	<div class="template form-group template3">
		<label class="col-sm-1 control-label no-padding-left"> 模板照片</label>
		<div class="col-sm-10">
                    <input type="hidden" name="ms[<?php echo $v['ms_type_id'];?>][ms_pic]" class="template-val" value="<?php echo $materialManagementInfo['detialArray'][$v['ms_type_id']]['ms_pic'];?>">
			<div class="template-list">
				<ul>
                                    <?php
                                      foreach($picList as $vv)
                                      {
                                    ?>
					<li>
                                            <img src="<?php echo $vv;?>">
                                            <?php
                                              if($materialManagementInfo['detialArray'][$v['ms_type_id']]['ms_pic'] == $vv){
                                            ?>
                                            <img class="havecheck" src="http://local.otoshop.com:801//themes/wap/__RESOURCE__/recouse/images/shopping_habits_ok.png">
                                            <?php
                                              }
                                            ?>
					</li>
                                    <?php
                                      }
                                    ?>
				</ul>
			</div>
		</div>
	</div>
        <?php
          }
        ?>
    
    
    
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-left">备注</label>
		<div class="col-sm-10">
                    <textarea class="form-control" rows="3" placeholder="备注信息" name="audit_detial" ><?php echo $materialManagementInfo['audit_detial'];?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-left">&nbsp;</label>
		<div class="col-sm-2">
			<a href="javascript:;" class="btn btn-primary" onclick="javascript:$('#myform').submit();">保存</a>
		</div>
	</div>
    <input type='hidden' value="<?php echo $_GP['sts_id'];?>" name="sts_id" id="sts_id">
    <input type='hidden' value="<?php echo $materialManagementInfo['id'];?>" name="id" id="id">
</form>
<script type="text/javascript">
	$(function(){
		$(".template-list li img").on("click",function(){
			var imgurl = $(this).attr("src");
			var html = '<img class="havecheck" src="<?php echo WEBSITE_ROOT;?>/themes/wap/__RESOURCE__/recouse/images/shopping_habits_ok.png">';
			$(this).parent("li").siblings().find(".havecheck").remove();
			$(this).parent("li").append(html);
			$(this).parents(".template-list").siblings(".template-val").val(imgurl);
		});
	})

</script>
<?php  include page('footer');?>
