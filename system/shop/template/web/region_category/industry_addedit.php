<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue"> 行业分类 </h3>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
                <form action="<?php if($info){
                    echo web_url($_GP['do'],array('op'=>'edit')); 
                }else{
                    echo web_url($_GP['do'],array('op'=>'add')); 
                } ?>" method="post" class="form-horizontal" >
                    <input type="hidden" name="id" value="<?php echo $info['id']; ?>" />
                    <input type="hidden" name="pid" value="<?php echo $info['gc_pid']?$info['gc_pid']:$_GP['pid']; ?>" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-left" >分类名称</label>
                        <div class="col-sm-9">
                            <input type="text" name="gc_name" class="col-xs-10 col-sm-2" value="<?php echo $info['name']; ?>" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-left" >排序</label>
                        <div class="col-sm-9">
                            <input type="number" name="gc_order" class="col-xs-10 col-sm-2" value="<?php echo $info['gc_order']; ?>" />
                        </div>
                    </div>
                    <?php if ( $info['gc_pid'] | $_GP['pid']   ) {?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-left" >商铺数量限制</label>
                            <div class="col-sm-9">
                                <input type="number" name="gc_limit" class="col-xs-10 col-sm-2" value="<?php echo $info['gc_limit']; ?>" />
                            </div>
                        </div>
                    <?php } ?>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-left" > </label>
                        <div class="col-sm-9">
                            <input name="submit" type="submit" value="提交" class="btn btn-primary span3">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
   
<?php include page('footer'); ?>