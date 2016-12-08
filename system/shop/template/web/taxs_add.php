<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue"> 税率录入 </h3>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
            <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" >
                <div class="mb10">
                        <!-- <a href="{:U('Warnadmin/index', array('type' => 1)) }" class="btn btn-primary">
                            查看已读
                        </a>
                        <a href="{:U('Warnadmin/index', array('type' => 0)) }" class="btn btn-primary">
                            查看未读
                        </a>
                        <a href="{:U('Warnadmin/index', array('type' => 2)) }" class="btn btn-primary">
                            查看所有
                        </a> -->
                        <br/>
                        <br/>
                        <label class="col-sm-2 control-label no-padding-left" for="input-search">商品类型：</label>&nbsp
                        <input name="use_type" type="text" id="input-search" value="<?php echo $this_tax['type']?>">
                        <br><br>
                        <label class="col-sm-2 control-label no-padding-left" for="input-search">&nbsp&nbsp税率：</label>&nbsp
                        <input name="use_tax" type="text" id="input-search" value="<?php echo $this_tax['tax']?>">(0-1)
                        <br><br>
                        <label class="col-sm-2 control-label no-padding-left" ></label>&nbsp;
                        <input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
                </div>
            </form>
            </div>
        </div>
    </div>
<?php  include page('footer');?>