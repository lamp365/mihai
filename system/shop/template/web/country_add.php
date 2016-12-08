<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue"> 税率录入 </h3>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
            <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" >
                <div class="mb10">
                        <br/>
                        <br/>
                        <label class="col-sm-2 control-label no-padding-left" for="input-search">国家：</label>
                        <input name="country_name" type="text" id="input-search" value="<?php echo $this_country['name']?>">
                        <br><br>
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-left" for="input-search">图标(46*46)：</label>
                            <div class="col-sm-9">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-preview thumbnail" style="width: 60px; height: 60px;">
                            <?php  if(!empty($this_country['icon'])) { ?>
                                <img src="<?php  echo $this_country['icon'];?>" alt="" onerror="$(this).remove();">
                            <?php  } ?>
                            </div>
                            <div>
                            <input name="thumb" id="thumb" type="file" />
                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                            </div>
                            <br><br>
                            <input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
                        </div>
                </div>
            </form>
            </div>
        </div>
    </div>
<?php  include page('footer');?>