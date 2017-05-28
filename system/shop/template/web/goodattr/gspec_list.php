<div class="alertModal-dialog-sm" style="width: 56%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">规格操作 <span style="margin-left: 25px;"></span></h4>
    </div>

    <div class="modal-body">
        <p style="font-size: 16px"><b>[<?php echo $gtype['gtype_name']; ?>]</b> 模型规格列表 &nbsp;&nbsp;
            <span class="btn-md btn-info btn" onclick="add_spec(this)">添加规格</span>
            <span style="margin-left: 10px;color: red;display: none" class="error_tip">最多只能建两个</span>
        </p>
        <div class="table_list">
            <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                <thead id='table_head'>
                <tr>
                    <th width="15%" class="text-center" >规格名称</th>
                    <th class="text-center" >规格项</th>
                    <th class="text-center" >已禁用</th>
                    <th width="110px;" class="text-center" >操作</th>
                </tr>
                </thead>
                <tbody class="spec_main">
                <?php foreach($speclist as $key=> $list){ ?>
                    <tr>
                        <td class="text-center" spec_id="<?php echo $list['spec_id']; ?>"><?php echo $list['spec_name'];?></td>
                        <td class="text-center" >
                            <?php
                            foreach($list['spec_item'] as $spec_row){
                                if($spec_row['status'] == 1){
                                    echo "<span class='btn btn-success btn-xs' title='点击禁用' onclick='change_status(this,{$spec_row['id']},0)'>{$spec_row['item_name']}</span>  ";
                                }
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php
                            foreach($list['spec_item'] as $spec_row2){
                                if($spec_row2['status'] == 0){
                                    echo "<span class='btn btn-default btn-xs' title='点击启用' onclick='change_status(this,{$spec_row['id']},1)'>{$spec_row2['item_name']}</span>  ";
                                }
                            }
                            ?>
                        </td>
                        <td class="text-center" >
                            <a href="javascript:;" class="btn btn-info" onclick="add_specitem(this)" >添加规格项</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <input type="hidden" id="gtype_id" name="gtype_id" value="<?php echo $gtype['id']; ?>">
</div>

<script>
    function add_spec(obj){
        var isok = true;
        $(".spec_main tr").each(function(){
            console.log($(this).find('td').eq(3).html());
           if($(this).find('td').eq(3).html() == '&nbsp;'){
               isok = false;
           }
        });
        if(!isok){
            return false;
        }
       //追加一条html
        var html = "<tr class='text-center'>" +
                    "<td spec_id='0' class='text-center'><input type='text' onblur='add_new_sepc(this)' style='width: 80px;' placeholder='如内存'></td>"+
                    "<td class='text-center'>&nbsp;</td>"+
                    "<td class='text-center'>&nbsp;</td>"+
                    "<td class='text-center'>&nbsp;</td>"+
                    "</tr>";
        $(".spec_main").append(html);
    }

    function add_new_sepc(obj){
        var url = "<?php  echo web_url('goodstype',array('op'=>'do_add_spec'));?>";
        var spec_id = $(obj).closest("td").attr('spec_id');
        var gtype_id = $("#gtype_id").val();
        var spec_name = $(obj).val();
        if($.trim(spec_name) == '' || $.trim(spec_name) == 'null'){
            return false;
        }

        //获取tr中长度
        var length =  $(".spec_main tr").length;
        //是否规格名一样
        var isok = true;
        $(".spec_main tr").each(function(index,ele){
            var num = index +1 ;
            var the_name = $(this).find('td').eq(0).html();
            the_name = $.trim(the_name);
            if(num != length){
                //不是最后一个做比对
                if(the_name == spec_name){
                    $(obj).val(' ');
                    $(obj).focus();
                    isok = false;
                    $(".error_tip").html('规格名不能一样！');
                    $(".error_tip").show();
                }
            }

        });
        if(!isok){
            return false;
        }
        $.post(url,{spec_id:spec_id,'gtype_id':gtype_id,'spec_name':spec_name},function(data){
                if(data.errno == 1){
                    var spec_id = data.message;
                    $(obj).closest("td").attr('spec_id',spec_id);
                    var html = ' <a href="javascript:;" class="btn btn-info" onclick="add_specitem(this)" >添加规格项</a>';
                    $(obj).closest("tr").find("td").last().html(html);
                    $(obj).closest("td").html(spec_name);
                }
        });
    }

    function add_specitem(obj){
        var html = "<input type='text' onblur='add_new_sepcitem(this)' style='width: 80px;' placeholder='如4G'>";
        $(obj).closest("tr").find('td').eq(1).append(html);
    }

    function add_new_sepcitem(obj){
        var item_name = $(obj).val();
        if(item_name == '' || item_name == null){
            return false;
        }
        //是否规格名一样
        var isok = true;
        $(obj).closest('tr').find('td').eq(1).find('span').each(function(){
            var the_name = $(this).html();
            the_name = $.trim(the_name);
            if(the_name == item_name){
                $(obj).val(' ');
                $(obj).focus();
                isok = false;
            }
        });
        if(!isok){
            return false;
        }
        //是否规格名一样
        isok = true;
        $(obj).closest('tr').find('td').eq(2).find('span').each(function(){
            var the_name = $(this).html();
            the_name = $.trim(the_name);
            if(the_name == item_name){
                $(obj).val(' ');
                $(obj).focus();
                isok = false;
            }
        });
        if(!isok){
            return false;
        }
        var url = "<?php  echo web_url('goodstype',array('op'=>'do_add_specitem'));?>";
        var spec_id = $(obj).closest('tr').find('td').eq(0).attr('spec_id');
        $.post(url,{spec_id:spec_id,'item_name':item_name},function(data){
            if(data.errno == 1){
                var item_id = data.message;
                var html = '<span class="btn btn-success btn-xs" title="点击禁用" onclick="change_status(this,'+item_id+',0)">'+item_name+'</span> ';
                $(obj).closest("tr").find("td").eq(1).append(html);
                $(obj).remove();
            }
        });
    }

    function change_status(obj,item_id,status){
        var item_name = $(obj).html();
        var url = "<?php  echo web_url('goodstype',array('op'=>'setitem_status'));?>";
        $.post(url,{item_id:item_id,'status':status},function(data){
            if(data.errno == 1){
                if(status == 0 ){
                    //自身是 启用的状态 现在要变成已禁用
                    var html = '<span class="btn btn-default btn-xs" title="点击启用" onclick="change_status(this,'+item_id+',1)">'+item_name+'</span> ';
                    $(obj).closest("tr").find("td").eq(2).append(html);
                }else{
                    var html = '<span class="btn btn-success btn-xs" title="点击禁用" onclick="change_status(this,'+item_id+',0)">'+item_name+'</span> ';
                    $(obj).closest("tr").find("td").eq(1).append(html);
                }
                $(obj).remove();
            }
        });
    }
</script>
