<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<style>
    .shop-list-tr li{
        float:left;
        width:300px;
    }
    .icon-resize-full{
        cursor: pointer;
    }
</style>
<h3 class="header smaller lighter blue">区域管理&nbsp;&nbsp;&nbsp;
<span class="btn btn-md btn-info" data-url="<?php echo web_url('region', array('op' => 'open_region')) ?>" onclick="open_region(this)">开通区域</span>
</h3>
<table class="table table-striped table-bordered table-hover">
    <tbody>
    <tr class="shop-list-tr">
        <td>
            <li>
                <select class="form-control" onchange="ajax_show_city(this)" >
                    <option value="0">请选择省份</option>
                    <?php foreach ($result as $row) {?>
                        <option value="<?php echo $row['region_id']; ?>" ><?php echo $row['region_name']; ?></option>
                    <?php } ?>
                </select>
            </li>
        </td>
    </tr>
    </tbody>
</table>
<table id="region_list_table" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th>已开通区域</th>
            <th class="text-center" >操作</th>
        </tr>
    </thead>
    <tbody class="open_city_table">

    </tbody>
</table>
<script>
function open_region(obj){
    var url  = $(obj).data('url');
    $.ajaxLoad(url,{},function(){
        $('#alterModal').modal('show');
    });

}

function ajax_show_city(obj){
    var region_id = $(obj).val();
    var table_html = "";
    var url = "<?php echo web_url('region',array('op'=>'get_hasOpenCity')); ?>";
    $.post(url,{region_id:region_id},function(data){
        if(data.errno != 1){
            //清空table的数据
            $(".open_city_table").html("");
        }else{
            //重写table的数据
            var data_obj = data.data;
            $.each(data_obj,function(index,ele){
                table_html +="<tr>"+
                    "<td region_id="+ele.region_id+">" +
                    "<span onclick='get_child_city(this,"+ele.region_id+")'><i class='icon-resize-full'></i></span>&nbsp;&nbsp;<b>"+ele.region_name+"</b>" +
                    "</td>"+
                    "<td>"+
                    "&nbsp;"+
                    "</td>"+
                    "</tr>";
            });
            $(".open_city_table").html(table_html);
        }
    },'json');
}

function get_child_city(obj,regionId){
    var tr_html = "";
    var url = "<?php echo web_url('region',array('op'=>'get_hasOpenCity')); ?>";
    $("#region_list_table .childs").hide();
    //已打开城市下的区域，则关闭
    if( $(obj).hasClass("isopen") ){
        $("#region_list_table .child-"+regionId+"").fadeOut();
        return false;
    }
    //已请求过，不在重复请求
    if( $("#region_list_table .child-"+regionId+"").length > 0 ){
        $("#region_list_table .child-"+regionId+"").fadeIn();
    }else{
        $.post(url,{region_id:regionId},function(data){
            if(data.errno != 1){

            }else{
                var data_obj = data.data;
                $.each(data_obj,function(index,ele){
                    tr_html +=  "<tr class='childs child-"+regionId+"'>"+
                        "<td>"+
                        "&nbsp;&nbsp;&nbsp;&nbsp;|----"+ele.region_name+
                        "</td>"+
                        "<td class='text-center'> " +
                        "<span class='btn btn-xs btn-info' onclick='displayModal("+ele.region_code+")'><i class='icon-edit'></i>设置区域限制</span>"+
                        "&nbsp;&nbsp;&nbsp;&nbsp;<span class='btn btn-xs btn-info' onclick='sure_close("+ele.region_id+")'><i class='icon-edi'></i>&nbsp;关闭区域&nbsp;</span>"+
                        "</td>"+
                        "</tr>";
                });

                $(obj).parents("tr").after(tr_html);
            }
        },"json")
    }
}

function sure_close(region_id)
{
    var url = "<?php echo web_url('region', array('op' => 'close_region'));?>";
    url = url + "&region_id="+region_id;
    $.post(url,{},function(data){
        if(data.errno != 1){
            alert(data.message);
        }else{
            alert(data.message,'',function(){
                window.location.reload();
            });
        }
    });
}

function displayModal(code) {
    var url = '<?php echo web_url('region',array('op'=>'limit_setting'))?>';
    $.ajaxLoad(url, {region_code:code}, function () {
        $('#alterModal').modal('show');
    });

};
</script>

<?php include page('footer'); ?>
								