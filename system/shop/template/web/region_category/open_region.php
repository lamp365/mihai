<style type="text/css">
    .more-city{
        color: #919191;
        margin-right: 10px;
        cursor: pointer;
    }
    .check-all{
        float: left;
        margin-right: 10px!important;
    }
    .check-child{
        margin-right: 10px!important;
        margin-left: 24px!important;
    }
</style>
<div class="alertModal-dialog-bg" style="width:45%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">开通区域</h4>
    </div>
    <form action="<?php echo web_url('region',array('op'=>'do_open')) ?>" method="post" id="region_form">
    <div class="modal-body">
        <div style="overflow: hidden"  >
            <div class="form-group col-xs-3">
                <label for="name"></label>
                <select class="form-control show_list_select" onchange="show_list(this)" name="region_id[]">
                    <option value="0">请选择省份</option>
                    <?php
                    foreach($parent as $one){
                        echo "<option value='{$one['region_id']}'>{$one['region_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-xs-3" style="float: right">
                <button type="submit" class="btn btn-md btn-info" style="float: right;margin-top: 6px;">确认开通</button>
            </div>
        </div>
        <div class="show_list">
            <table class="table table-striped table-bordered table-hover" id="open_region_table">
                <thead>
                <tr>
                    <th>城市</th>
                    <th>状态</th>
                    <th>默认</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody class="city_table">
                </tbody>
            </table>
        </div>
    </div>
    </form>
</div>
<script>
    $(function(){
        $("body").on("click",".check-child",function(){
            var ischeck = $(this).prop("checked");
            var allclass= $(this).attr("allclass");
            var check_num = 0;
            $("#open_region_table .check-child").each(function(index,ele){
                if($(ele).prop("checked")){
                    check_num++;
                }
            })
            if( check_num > 0 ){
                $("."+allclass).prop("checked",true);
            }else{
                $("."+allclass).prop("checked",false);
            }
        });
        $("body").on("click",".check-all",function(){
            var childclass = $(this).attr("childclass");
            var ischeck = $(this).prop("checked");
            var regionId = $(this).val();
            if( ischeck ){
                $("."+childclass).find(".check-child").prop("checked",true);
            }else{
                $("."+childclass).find(".check-child").prop("checked",false);
            }
            if( $("."+childclass).find(".check-child").length == 0 ){
                moreCity($(this).siblings(".more-city"),regionId,"checkall");
            }
            
        });
    })
    function show_list(obj){
        var region_id = $(obj).val();
        var table_html = "";
        var url = "<?php echo web_url('goodscommon',array('op'=>'getNextRegion')); ?>";
        $.post(url,{region_id:region_id},function(data){
            if(data.errno != 200){
                //清空table的数据
                $(".city_table").html("");
            }else{
                //重写table的数据
                var data_obj = data.message;
                var state_val = "";
                var default_val = "";
                $.each(data_obj,function(index,ele){
                    if( ele.is_open == 0 ){
                        state_val = "未开通"
                    }else if( ele.is_open == 1 ){
                        state_val = "<font color='red'>已开通</font>"
                    }
                    if( ele.region_is_default_qu == 0 ){
                        default_val = "非默认"
                    }else if( ele.region_is_default_qu == 1 ){
                        default_val = "<font color='red'>默认</font>";
                    }
                    table_html +="<tr>"+
                                    "<td region_id="+ele.region_id+"><input type='checkbox' name='region_id[]' value='"+ele.region_id+"' childclass='child-"+ele.region_id+"' class='check-all check-all-"+ele.region_id+"'><i onclick='moreCity(this,"+ele.region_id+")' class='more-city icon-plus-sign'></i><b>"+ele.region_name+"</b></td>"+
                                    "<td>"+state_val+"</td>"+
                                    "<td class='default'>"+default_val+"</td>"+
                                    "<td>"+
                                        "<span class='btn btn-xs btn-info' onclick='setDefault(this,"+ele.region_id+")'>设为默认城市</span>"+
                                    "</td>"+
                                "</tr>";
                });
                $(".city_table").html(table_html);
            }
        },'json');
    }
    function moreCity(obj,regionId,checkall){
        var tr_html = "";
        var url = "<?php echo web_url('goodscommon',array('op'=>'getNextRegion')); ?>";
        $("#open_region_table .childs").hide();
        $(obj).parents("tr").siblings().find(".more-city").removeClass("isopen icon-minus-sign").addClass("icon-plus-sign");
        //已打开城市下的区域，则关闭
        if( $(obj).hasClass("isopen") ){
            $("#open_region_table .child-"+regionId+"").fadeOut();
            $(obj).removeClass("isopen icon-minus-sign").addClass("icon-plus-sign");
            return false;
        }
        //已请求过，不在重复请求
        if( $("#open_region_table .child-"+regionId+"").length > 0 ){
            $("#open_region_table .child-"+regionId+"").fadeIn();
            $(obj).addClass("isopen icon-minus-sign").removeClass("icon-plus-sign");
        }else{
            $.post(url,{region_id:regionId},function(data){
                if(data.errno != 200){

                }else{
                    $(obj).addClass("isopen icon-minus-sign").removeClass("icon-plus-sign");
                    var data_obj = data.message;
                    var state_val = "";
                    $.each(data_obj,function(index,ele){
                        if( ele.is_open == 0 ){
                            state_val = "未开通"
                        }else if( ele.is_open == 1 ){
                            state_val = "<font color='red'>已开通</font>"
                        }
                        tr_html +=  "<tr class='childs child-"+regionId+"'>"+
                                        "<td>"+
                                            "<input type='checkbox' name='region_id[]' value='"+ele.region_id+"' allclass='check-all-"+regionId+"' class='check-child'>"+ele.region_name+
                                        "</td>"+
                                        "<td>"+state_val+"</td>"+
                                        "<td> </td>"+
                                        "<td> </td>"+
                                    "</tr>";
                    });
                    $(obj).parents("tr").after(tr_html);
                }
                if(checkall){
                    $("#open_region_table .child-"+regionId+"").find(".check-child").prop("checked",true)
                }
            },"json");
        }
    }

function setDefault(obj,region_id){
    var url = '<?php echo web_url('region',array('op'=>'setDefault'))?>';
    $.post(url, {region_id:region_id}, function (ret) {
        if(ret.errno==1){
            $(".default").html("非默认");
            $(obj).parents('tr').find(".default").html("<font color='red'>默认</font>");
        }
    },'json');
}
</script>