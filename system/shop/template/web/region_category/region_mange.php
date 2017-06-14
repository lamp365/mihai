<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<style>
    .shop-list-tr li{
        float:left;
        width:300px;
    }
    .nav-tabs li a{
        padding-left: 18px;
        padding-right: 18px;
        text-align: center;
    }
</style>
<h3 class="header smaller lighter blue">区域管理&nbsp;&nbsp;&nbsp;</h3>
<form action="<?php echo web_url('region',array('op'=>'batchSetLimit')) ?>"  class="form-horizontal" method="post">
    <table class="table table-striped table-bordered table-hover">
        <tbody>
            <tr class="shop-list-tr">
            <td>
<!--                <li>
                  <span class="left-span">城市名称</span>
                    <input style="margin-right:5px;width: 200px; height:30px; line-height:28px; padding:2px 0" name="reg_name" type="text" value="<?php  echo $_GP['reg_name'];?>" placeholder="城市名称"/> 
                </li>	
                <li>
                    <span class="left-span">区域名称</span>
                    <input style="margin-right:5px;width: 200px; height:30px; line-height:28px; padding:2px 0" name="region_name" type="text" value="<?php  echo $_GP['region_name'];?>" placeholder="区域名称"/> 
                </li>	-->
                <li>
                    <span class="left-span">省份：</span>
                    <select   id="cate_1" style="margin-right:15px;"  name="province_id" class="pcates"  onchange="fetchChildCategory(this, this.options[this.selectedIndex].value)"  autocomplete="off">
                            <option value="0">请选择一级城市</option>
                            <?php if (is_array( $region )) {foreach ($region as $row) {?>
                                <?php if ( $row['parent_id'] == 0 ) { ?>
                                    <option value="<?php echo $row['region_id']; ?>" <?php if ($row['region_id'] == $extend_arr['p1']) { ?> selected="selected"<?php } ?>><?php echo $row['region_name']; ?></option>
                            <?php }}} ?>
                    </select>
                </li>
                
                <li>
                    <span class="left-span">省份：</span>
                    <select  id="cate_2" name="city_id" class="cates_2" onchange="changeDisplayData(this, this.options[this.selectedIndex].value)" autocomplete="off">
                        <option value="-1">请选择二级城市</option>
                        <?php if (!empty($extend_arr['p2']) && !empty($childrens[$extend_arr['p1']])) { ?>
                            <?php
                            if (is_array($childrens[$extend_arr['p1']])) {
                                foreach ($childrens[$extend_arr['p1']] as $row) {
                                    ?>
                                    <option  value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $extend_arr['p2']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
                                <?php }
                            }
                            ?>
                        <?php } ?>
                        </select>
                     </li>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<table id="table_form" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <!--<th class="text-center" ></th>-->
            <!--<th class="text-center" >限制浮动</th>-->
            <th class="text-center" >城市</th>
            <th class="text-center" >操作</th>
        </tr>
    </thead>
    <tbody>
<?php
$index = 0;
if (is_array($result)) {
    foreach ($result as $index => $item) {
        ?>
            <tr>
<!--                <td class="text-center"><?php echo $item['reg_cst_id']; ?></td>
                <td class="text-center"><?php echo $item['reg_name']; ?></td>-->
                <td class="text-center"><?php echo $item['region_name']; ?></td>
                <td class="text-center">
                    <a class="btn btn-xs btn-info"  href="
                        <?php echo create_url('site', array('name' => $_GP['name'], 'do' => $_GP['do'], 'op' =>'edit', 'id' => $item['reg_cst_id'])) ?>
                       "><i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> 
                    &nbsp;&nbsp;	<a class="btn btn-xs btn-info" onclick="return confirm('此操作不可恢复，确认删除？');return false;"  href="
                        <?php echo create_url('site', array('name' => $_GP['name'], 'do' => $_GP['do'], 'op' => 'delete', 'id' => $item['reg_cst_id'])) ?>
                                    "><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a> </td>
                </td>
            </tr>
        <?php
    }
}
?>
    </tbody>
</table>
<script language="javascript">
    var category = <?php echo json_encode($childrens) ?>;
    
    function fetchChildCategory(o_obj, cid) {
        var html = '<option value="0">请选择二级分类</option>';

        var obj = $(o_obj).parent().find('.cates_2').get(0);
        if (!category || !category[cid]) {
            $('#cate_2').html(html);

            fetchChildCategory2(o_obj, obj.options[obj.selectedIndex].value);
            return false;
        }
        for (i in category[cid]) {
            html += '<option value="' + category[cid][i][0] + '">' + category[cid][i][1] + '</option>';
        }
        $('#cate_2').html(html);
        $('#table_form tbody').html('');
    }
    function changeDisplayData(o_obj, cid) {
        var html = "";
        if(cid>0){
                if (!category || !category[cid]) {
                $('#table_form tbody').html(html);
                return false;
            }
        }
        for (i in category[cid]) {
            //console.log(category[cid][i]);return false;
            var is_default = '';
            if(category[cid][i][3]==1){is_default ='<i class="icon-home">';}
            html += '<tr>';
                html += '<td class="text-center" id=home_td_'+category[cid][i][0]+'>'+is_default+category[cid][i][1]+'</td>';
                html += '<td class="text-center">';
                html += '<a class="btn btn-xs btn-info"href="javascript:;" onclick="displayModal('+category[cid][i][2]+');">';
                html += '<i class="icon-edit"></i>设置区域限制 </a> ';
            if(category[cid][i][3]!=1){
                html += '<a class="btn btn-xs btn-success"href="javascript:;" onclick="setDefault('+category[cid][i][0]+');">';
                html += '<i class="icon-flag"></i>设为默认区</a> ';
            }
                html += '</td> ';
            html += '<tr>';
        }
//        console.log(html);return false;
        $('#table_form tbody').html(html);
    };
    
     function displayModal(code) {
        //var url = $(obj).data('url');
        var url = '<?php echo web_url('region',array('op'=>'limit_setting'))?>';
        $.ajaxLoad(url, {region_code:code}, function () {
            $('#alterModal').modal('show');
           // iFrame();
        });
        
    };
    function setDefault(id) {
        //var url = $(obj).data('url');
        var url = '<?php echo web_url('region',array('op'=>'setDefault'))?>';
        $.post(url, {id:id}, function (ret) {
            if(ret.errno==1){
                $(".icon-home").toggleClass("icon-home");
                var txt = $("#home_td_"+id).text();
                $("#home_td_"+id).html('<i class="icon-home"><i>'+txt);
            }else{
                alert(ret.message);
            }
           
           // iFrame();
        });
        
    };
    
    
</script>

<?php include page('footer'); ?>
								