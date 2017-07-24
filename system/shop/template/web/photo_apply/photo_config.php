<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT; ?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/lang/zh_CN.js"></script> 
<style type="text/css">
    .ipost-list{
        padding:0; 
    }
</style>

<form action="<?php  echo web_url('photo_apply', array('op' => 'photo_config_sub'))?>" method="post" enctype="multipart/form-data" class="form-horizontal" style="margin-top: 20px;">
    
    
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 物料类型：</label>
        <div class="col-sm-2">
            <select class="form-control" name="ms_category">
                <option value="1" <?php echo $photoApplyInfo['ms_category']==1?'selected':'';?>>小型</option>
                <option value="2" <?php echo $photoApplyInfo['ms_category']==2?'selected':'';?>>中型</option>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 物料尺寸：</label>
        <!-- <input type="text" name="ms_size" class="form-control" placeholder="" value="<?php echo $photoApplyInfo['ms_size'];?>"> -->
        <div class="col-sm-1" style="position:relative">
            <input type="text" name="ms_size_w" class="form-control" placeholder="" value="80">
            <span style="position:absolute;right: -3px;top: 20%;font-size: 20px;">*</span>
        </div>
        <div class="col-sm-1">
            <input type="text" name="ms_size_h" class="form-control" placeholder="" value="100">
        </div>
    </div>
    
    <div class="row" style="margin-bottom: 15px;">
        <label class="col-sm-2 control-label no-padding-left" > 区域：</label>
        <div class="row">
            <div id="province" class="col-xs-2">
                <select class="form-control" name="ms_province" id="ms_province">
                    <option value="0">请选择省份</option>
                    <?php
                      foreach($provinceData as $v){
                    ?>
                    <option value="<?php echo $v['region_id'];?>|<?php echo $v['region_name'];?>|<?php echo $v['region_code'];?>" <?php echo $v['region_id']==$photoApplyInfo['ms_province_id']?'selected':'';?>><?php echo $v['region_name'];?></option>
                    <?php
                      }
                    ?>
                </select>
            </div>
            
            <div id="div_city" class="col-xs-2">
               <select class="form-control" name="ms_city" id="ms_city"><option value="">请选择市级</option>
               <?php
                 foreach($cityData as $v){
               ?>
                <option value="<?php echo $v['region_id'];?>|<?php echo $v['region_name'];?>|<?php echo $v['region_code'];?>" <?php echo $v['region_id']==$photoApplyInfo['ms_city_id']?'selected':'';?>><?php echo $v['region_name'];?></option>
              <?php
                 }
               ?>
               </select>
            </div>
            
            <div id="div_county" class="col-xs-2">
                <select class="form-control" name="ms_county" id="ms_county"><option value="">请选择区级</option>
               <?php
                 foreach($countyData as $v){
               ?>
                <option value="<?php echo $v['region_id'];?>|<?php echo $v['region_name'];?>|<?php echo $v['region_code'];?>" <?php echo $v['region_id']==$photoApplyInfo['ms_county_id']?'selected':'';?>><?php echo $v['region_name'];?></option>
              <?php
                 }
               ?>
               </select>
            </div>
            
        </div>
    </div>
    
    <div class="tab-pane" id="tab2primary">
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-left" > 模板图片：</label>

            <div class="col-sm-9">
                <span id="selectimage" tabindex="-1" class="btn btn-primary"><i class="icon-plus"></i> 上传照片</span><span style="color:red;">
                    <input name="piclist" type="hidden" value="<?php echo $item['piclist']; ?>" /></span>
                <div id="file_upload-queue" class="uploadify-queue"></div>
                <ul class="ipost-list ui-sortable" id="fileList">
                    <?php if (is_array($piclist)) {
                        foreach ($piclist as $v_pic) { ?>
                            <li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;   width: 125px;  height: 130px;">
                                <span class="item_box">
                                    <img src="<?php echo$v_pic; ?>" style="width:50px;height:50px">    </span>
                                <a  href="javascript:;" onclick="deletepic(this, 0);" title="删除">删除</a>

                                <input type="hidden" value="<?php echo $v_pic; ?>" name="attachment-new[]">
                            </li>
    <?php }
} ?>
                </ul>
            </div>
        </div>
    </div>
    
    
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > 物料类型状态：</label>
        <div class="col-sm-2">
            <div class="radio">
              <label>
                <input type="radio" name="ms_status" id="ms_status" value="1" <?php echo $photoApplyInfo['ms_status']>=0?'checked':'';?>>启用
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="ms_status" id="ms_status" value="2"  <?php echo $photoApplyInfo['ms_status']>1?'checked':'';?>>禁用
              </label>
            </div>
        </div>
    </div> 
    
    <input type="hidden" value="<?php echo $id;?>" name="id">
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>
        <div class="col-sm-9">
            <br/><input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
        </div>
    </div>
    
</form>




<script type="text/javascript">
    $(function () {
        
        
        var i = 0;
        $('#selectimage').click(function () {
            var editor = KindEditor.editor({
                allowFileManager: false,
                imageSizeLimit: '10MB',
                uploadJson: '<?php echo mobile_url('upload') ?>'
            });
            editor.loadPlugin('multiimage', function () {
                editor.plugin.multiImageDialog({
                    clickFn: function (list) {
                        if (list && list.length > 0) {
                            for (i in list) {
                                if (list[i]) {
                                    html = '<li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;  width: 125px;  height: 130px;">' +
                                            '<span class="item_box"> <img src="' + list[i]['url'] + '" style="width:50px;height:50px"></span>' +
                                            '<a href="javascript:;" onclick="deletepic(this,0);" title="删除">删除</a>' +
                                            '<input type="hidden" name="attachment-new[]" value="' + list[i]['filename'] + '" />' +
                                            '</li>';
                                    $('#fileList').append(html);
                                    i++;
                                }
                            }
                            editor.hideDialog();
                        } else {
                            alert('请先选择要上传的图片！');
                        }
                    }
                });
            });
        });
        
        $('#ms_province').on('change',function(){
            var url = "<?php  echo web_url('photo_apply', array('op' => 'photo_city'))?>";
            $.ajax({
                type: "POST",
                url: url,
                data: "parentid="+$(this).val(),
                success:function(data){
                    $('#div_city').html(data);
                    $('#div_county').html('');
                }
            });
        });
        
        $('body').on('change',"#ms_city",function(){
            var url = "<?php  echo web_url('photo_apply', array('op' => 'photo_county'))?>";
            $.ajax({
                type: "POST",
                url: url,
                data: "parentid="+$(this).val(),
                success:function(data){
                    $('#div_county').html(data);
                }
            });
        });
        
    })

    function deletepic(obj, oid) {
        if (confirm("确认要删除？")) {

            var $thisob = $(obj);
            var $liobj = $thisob.parent();
            var picurl = $liobj.children('input').val();
            $liobj.remove();
        }
    }
</script>

<?php include page('footer'); ?>