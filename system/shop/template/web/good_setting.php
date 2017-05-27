<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<h3 class="header smaller lighter blue">设置商品通用详情</h3>
<div class="wrap jj">
    <div class="well form-search">
        <div class="search_type cc mb10">
        <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return fillform()">
            <div class="mb10">
                    <br>
                    <label class="col-sm-2 control-label no-padding-left">详情头部(app&web)：</label>
                    <div class="col-sm-9">
                        <textarea  id="container" name="content" style="height:400px; width:800px;"><?php  echo $head['value'];?></textarea>
                    </div>
                    <div class="col-sm-12">
                    </br></br></br>
                    </div>
                    <label class="col-sm-2 control-label no-padding-left">详情头部(仅web)：</label>
                    <div class="col-sm-9">
                        <textarea  id="container3" name="content3" style="height:400px; width:800px;"><?php  echo $pc_head['value'];?></textarea>
                    </div>
                    <div class="col-sm-12">
                    </br></br></br>
                    </div>
                    <label class="col-sm-2 control-label no-padding-left">详情尾部：</label>
                    <div class="col-sm-9">
                        <textarea  id="container2" name="content2" style="height:400px; width:800px;"><?php  echo $foot['value'];?></textarea>
                    </div>
                    <div class="col-sm-12">
                    </br></br>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10">
                            <label class="col-sm-2 control-label no-padding-left" ></label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="submit" class="btn btn-primary span2" name="submit" value="submit"><i class="icon-edit"></i> 提 交 </button>
                        </div>
                    </div>
            </div>
        </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript">var ue = UE.getEditor('container');</script>
<script type="text/javascript">var ue = UE.getEditor('container2');</script>
<script type="text/javascript">var ue = UE.getEditor('container3');</script>

<script language="javascript">
function fillform()
{
    if(ue.queryCommandState( 'source' )==1){        
        document.getElementById("container").value=ue.getContent();  
        document.getElementById("container2").value=ue.getContent();
        document.getElementById("container3").value=ue.getContent();
    }else{  
        document.getElementById("container").value=ue.body.innerHTML;    
        document.getElementById("container2").value=ue.body.innerHTML; 
        document.getElementById("container3").value=ue.body.innerHTML; 
    }
    return true;
}
</script>

<?php  include page('footer');?>