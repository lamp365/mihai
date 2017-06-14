<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.css" />
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/style.css" />

<script src="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/clipboard.min.js"></script>
<style>
	#uploader .placeholder .webuploader-pick{
		line-height: 120px;
	}
	#uploader .placeholder{
		padding-bottom: 20px;
	}
	#filePicker2 .webuploader-pick{
		width: 96px;
		height: 43px;
		line-height: 43px;
	}
</style>
<div style="margin: 15px;">
    文件目录：
    <select name="dir" id="" class="dir" onchange="set_dir(this)">
        <option value="0">按最新时间目录</option>
        <?php if(!empty($dir_arr)){
            foreach($dir_arr as $dir_one){
                $dir_one = rtrim($dir_one,'/');
                echo "<option value='{$dir_one}'>{$dir_one}</option>";
            }
        } ?>
    </select>
    文件命名：
    <select name="rename_type" style="height: 28px;line-height: 28px;" class="rename_type" onchange="set_savename(this)">
        <option value="0">系统随机命名</option>
        <option value="1">按照文件原名</option>
    </select>
</div>
<div id="wrapper">
    <div id="container">
        <!--头部，相册选择和格式选择-->
        <div id="uploader">
            <div class="queueList">
                <div id="dndArea" class="placeholder">
                    <div id="filePicker"></div>
                    <p>或将照片拖到这里，单次最多可选300张</p>
                </div>
            </div>
            <div class="statusBar" style="display:none;">
                <div class="progress">
                    <span class="text">0%</span>
                    <span class="percentage"></span>
                </div><div class="info"></div>
                <div class="btns">
                    <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/upload.js" type="text/javascript" charset="utf-8"></script>
<script>
    var dir = 0;
    var save_oldname = 0;
    var server_url = "";
    function set_dir(obj){
        server_url = "fileupload.php?savelocal=0";
        dir = $(obj).val();
        server_url = server_url + "&dir="+dir+"&save_oldname="+save_oldname;
        uploader.option( 'server', server_url);
    }
    function set_savename(obj){
        server_url = "fileupload.php?savelocal=0";
        save_oldname = $(obj).val();
        server_url = server_url + "&save_oldname="+save_oldname+ "&dir="+dir;
        uploader.option( 'server', server_url);
    }
</script>

