<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue"><?php if(empty($priviel)){ echo '添加';}else{ echo '编辑';}?>会员特权&nbsp;&nbsp;&nbsp;</h3>
<form action="<?php echo web_url('rank',array('op'=>'post_priviel')) ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="id" class="col-xs-10 col-sm-2" value="<?php echo $priviel['id'];?>" />


    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >特权名称 </label>

        <div class="col-sm-9">
            <input type="text" name="priviel_name" class="col-xs-10 col-sm-3" value="<?php echo $priviel['name']?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left"> 图片：</label>

        <div class="col-sm-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                    <?php  if(!empty($priviel['icon'])) { ?>
                        <img style="width: 100%" src="<?php echo $priviel['icon'];?>" >
                    <?php  } ?>
                </div>
                <div>
                    <input name="icon" id="icon" type="file" />
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>
        <input type="hidden" value="1" name="insert_data">
        <div class="col-sm-9">
            <input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>

        </div>
    </div>
</form>

<?php  include page('footer');?>
