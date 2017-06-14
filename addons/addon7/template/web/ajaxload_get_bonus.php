<div class="alertModal-dialog-bg">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">请选择优惠卷&nbsp;&nbsp;
            <select onchange="change_the_bonus(this)" style="font-size: 14px;padding: 2px;">
                <?php foreach($bonus_enum_arr as $key => $val){
                    echo "<option value='{$key}'>{$val}</option>";
                } ?>
            </select>
        </h4>
    </div>
    <div class="modal-body" id="">
        <?php if(empty($all_bonus)){
            echo "<p>暂无优惠卷</p>";
        }else{ ?>
        <table class="table table-striped">
        <thead>
        <tr>
            <th>选择</th>
            <th>名称</th>
            <th>金额</th>
            <th>最小订单</th>
            <th>发放结束时间</th>
        </tr>
        </thead>
        <tbody id="show_bonus_box">
        <?php foreach($all_bonus as $this_one){ ?>
            <tr>
                <td><input type="radio" name="bonus" class="radio_bonus" value="<?php echo $this_one['type_id'];?>"></td>
                <td><?php echo $this_one['type_name'];?></td>
                <td><?php echo $this_one['type_money'];?></td>
                <td><?php echo $this_one['min_goods_amount'];?></td>
                <td><?php echo date("Y-m-d H:i",$this_one['send_end_date']);?></td>
            </tr>
        <?php } ?>
        </tbody>
        </table>
        <?php } ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary sure_click">确认选择</button>
    </div>
</div>

<script>
    function change_the_bonus(obj){
        var send_type = $(obj).val();
        var url = "<?php  echo web_url('addaward',array('op'=>'get_bonus','showajax'=>1));?>";
        $.post(url,{'send_type':send_type},function(data){
            $("#show_bonus_box").html(' ');
            if(data.errno == 200){
                var info = data.message;
                var html = '';
                for(var i=0;i<info.length;i++){
                    var row = info[i];
                    html +="<tr>" +
                                "<td><input type='radio' name='bonus' class='radio_bonus' value='"+ row.type_id +"'/></td>"+
                                "<td>"+ row.type_name +"</td>"+
                                "<td>"+ row.type_money +"</td>"+
                                "<td>"+ row.min_goods_amount +"</td>"+
                                "<td>"+ totime(row.send_end_date) +"</td>"+
                        "</tr>";
                }
            }else{
                var html = "<p>"+data.message+"</p>";
            }
            $("#show_bonus_box").html(html);
        },'json')
    }

    function totime(time){
        time = time *1000;
        var datetime = new Date();
        datetime.setTime(time);
        var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;
        var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();
        var hours = datetime.getDate() < 10 ? "0" + datetime.getHours() : datetime.getHours();
        var minute = datetime.getDate() < 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();
        return month + "-" + date + " " + hours + ":" + minute;
    }

    $(".sure_click").click(function(){
        $(".radio_bonus").each(function(){
            if(this.checked){
                var bonus_id = $(this).val();
                var name = $(this).parent().parent().find("td").eq(1).html();
                var price = $(this).parent().parent().find("td").eq(2).html();
                $("#gid").val(bonus_id);
                $("#title").val(name);
                $("#names").val(name);
                $("#price").val(price);
                $("#alterModal").modal('hide');
            }
        })
    })
</script>