<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
    <style>
        .full_scree{
            position: absolute;
            width:100%;
            height: 1600px;
            top:0px;
            left: 0px;
            background:#000;
            opacity: 0.3;
            filter:alpha(opacity=0.3);
            z-index: 99000;
            display: none;
        }
        .full_scree img{
            margin: 200px auto;
            display: block;
        }
    </style>
    <h3 class="header smaller lighter blue">数据库列表&nbsp;&nbsp;&nbsp;
        <a href="javascript:;" class="btn btn-primary back_sql" data-name="">备份数据库</a>&nbsp;&nbsp;
        <a href="<?php  echo web_url('database',array('op' =>'huanyuan'))?>" class="btn btn-primary">还原数据库</a>
    </h3>

    <table class="table table-striped table-bordered table-hover">
        <thead >
        <tr>
            <th  style="text-align:center;width:30px">ID</th>
            <th  style="text-align:center;">表名</th>
            <th  style="text-align:center;">数据量</th>
            <th  style="text-align:center;">数据大小</th>
            <th  style="text-align:center;">数据索引</th>
            <th  style="text-align:center;">创建时间</th>
            <th  style="text-align:center;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php $index=1; if(is_array($tables)) { foreach($tables as $row) { ?>
            <tr style="text-align:center;">
                <td><?php  echo $index++;?></td>
                <td><?php  echo $row['Name'];?></td>
                <td> <?php  echo $row['Rows'];?>条</td>
                <td> <?php  echo tosize($row['Data_length']);?></td>
                <td> <?php  echo $row['Auto_increment'];?></td>
                <td> <?php  echo $row['Create_time'];?></td>
                <td><span class="btn btn-warning btn-xs back_sql" data-name="<?php  echo $row['Name'];?>">备份该数据</span></td>
            </tr>
        <?php  } } ?>
        </tbody>
    </table>
<div class="full_scree">
    <img src="images/load.gif" width="32" height="32">
</div>
<script>
    $(".back_sql").click(function(){
        var sqlname = $(this).data('name');
        var url ="<?php  echo web_url('database',array('op' =>'back'))?>";
        if(sqlname.length >0){
            url += "&sqlname="+sqlname;
        }
        $('.full_scree').show();
        $.get(url,function(data){
            $('.full_scree').hide();
            alert(data.message);
        },'json')
    })
</script>
<?php  echo $pager;?>
<?php  include page('footer');?>