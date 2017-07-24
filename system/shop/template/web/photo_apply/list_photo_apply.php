<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT; ?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/lang/zh_CN.js"></script> 
<h3 class="header smaller lighter blue">物料设置</h3>
<style>
    .good_line_table{

        width:100%;
    }
    .choose_kefu span{margin-right: 10px;cursor: pointer}
    .nav-tabs li a{
        padding: 6px 22px;
    }
</style>
<ul class="nav nav-tabs" >
    <li style="" <?php if ($_GP['op'] == 'general') { ?> class="active"<?php } ?>><a href="<?php echo web_url('store_shop_manage', array('op' => 'general')) ?>">常规设置</a></li>
    <li style="" <?php if ($_GP['op'] == 'list_photo_apply') { ?> class="active"<?php } ?>><a href="<?php echo web_url('photo_apply', array('op' => 'list_photo_apply')) ?>">物料列表</a></li>
</ul><br/>

<h3 class="header smaller lighter blue">
        物料列表&nbsp;&nbsp;&nbsp;
        <a href="<?php  echo web_url('photo_apply',array('op'=>'photo_config'))?>" class="btn btn-primary">添加物料类型</a>
    </h3>

    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th style="text-align: center; width: 120px">物料类型ID</th>
            <th style="text-align: center;">类型名称</th>
            <th style="text-align: center;">状态</th>
            <th style="text-align: center;">缩略图</th>    
            <th style="text-align: center;">区域</th>     
            <th style="text-align: center;">尺寸</th>    
            <th style="text-align: center;">是否默认</th>   
            <th style="text-align: center;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
            <tr style="text-align: center;">
                <td><?php echo $value['ms_type_id'];?></td>
                <td><?php echo $value['ms_type'];?></td>
                <td><a href="javascript:void(0);" class="ms_status" style="text-decoration:none;" data-status="<?php echo $value['ms_status']==1?2:1;?>" data-id="<?php echo $value['ms_type_id'];?>"><?php echo $value['ms_status']==1?'启用':'禁用';?></a></td>
                <td>
                   <?php
                     $picList = array();
                     $picList = explode(',', $value['ms_type_url']);
                     foreach($picList as $v){
                   ?> 
                    <img src="<?php echo $v;?>" alt="" class="img-thumbnail"  style="width:135px; height: 135px;">
                    <?php
                     }
                    ?>
                
                </td>
                <td><?php echo $value['ms_province_name'].'-'.$value['ms_city_name'].'-'.$value['ms_county_name'];?></td>
                <td><?php echo $value['ms_size'];?></td>
                <td><a href="javascript:void(0);" class="ms_is_default"  style="text-decoration:none;" data-type="<?php echo $value['ms_category'];?>"  data-id="<?php echo $value['ms_type_id'];?>" data-default="<?php echo $value['ms_is_default']==0?1:0;?>"><?php echo $value['ms_is_default']==1?'默认':'非默认';?></a></td>
                <td style="text-align: center;" >
                    <a class="btn btn-xs btn-info" href="<?php echo web_url('photo_apply', array('op' => 'photo_config','id'=>$value['ms_type_id']))?>">
                        <i class="icon-edit"></i>修改
                    </a>
                </td>
            </tr>
        <?php  } } ?>
        </tbody>
    </table>
<div>
    <?php echo $pager;?>
</div>
<script>
$(function(){
    $('.ms_status').on("click",function(){
        var url    = "<?php echo web_url('photo_apply',array('op'=>'materialTypeStatus'));?>";
        var ms_status = parseInt($(this).data("status"));
        var id = parseInt($(this).data("id"));

        var _this  = $(this);
        $.post(url,{ms_status:ms_status,id:id},function(res){
            if(ms_status == 1)
            {
                _this.data("status",2);
                _this.html("启用");
            }
            else{
                _this.data("status",1);
                _this.html("禁用");
            }
        },"json");
    }); 
    
    
    
    $('.ms_is_default').on("click",function(){
        var url    = "<?php echo web_url('photo_apply',array('op'=>'materialTypeDefault'));?>";
        var ms_is_default = parseInt($(this).data("default"));
        var id = parseInt($(this).data("id"));
        var type = parseInt($(this).data("type"));
        var _this  = $(this);
        
        $.post(url,{ms_is_default:ms_is_default,id:id,type:type},function(res){
            if(ms_is_default == 1)
            {
                _this.data("default",0);
                _this.html("默认");
            }
            else{
                _this.data("default",1);
                _this.html("非默认");
            }
        },"json");
    }); 
    
});
</script>
<?php include page('footer'); ?>