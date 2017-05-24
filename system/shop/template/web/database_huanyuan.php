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
        <a href="<?php  echo web_url('database',array('op' =>'list'))?>" class="btn btn-primary back_sql" data-name="">&nbsp;返&nbsp;回&nbsp;</a>&nbsp;&nbsp;
    </h3>

    <table class="table table-striped table-bordered table-hover">
        <thead >
        <tr>
            <th  style="text-align:center;width:30px">ID</th>
            <th  style="text-align:center;">数据库名</th>
            <th  style="text-align:center;">数据大小</th>
            <th  style="text-align:center;">备份时间</th>
            <th  style="text-align:center;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php $index=1; if(is_array($datalist)) { foreach($datalist as $row) { ?>
            <tr style="text-align:center;">
                <td><?php  echo $index++;?></td>
                <td><?php  echo $row['name'];?></td>
                <td> <?php  echo tosize($row['size']);?></td>
                <td> <?php  echo $row['date'].' '.$row['time'];?></td>
                <td>
                    <span class="btn btn-warning btn-xs back_huanyuan" data-dbname="<?php  echo $row['name'];?>">还原数据</span>
                    <span class="btn btn-danger btn-xs back_del" data-dbname="<?php  echo $row['name'];?>">删除数据</span>
                </td>
            </tr>
        <?php  } } ?>
        </tbody>
    </table>
<div class="full_scree">
    <img src="images/load.gif" width="32" height="32">
</div>
<script>

$(".back_del").click(function(){
    var dbname = $(this).data('dbname');
    var url ="<?php  echo web_url('database',array('op' =>'deldb'))?>";
    $.post(url,{'dbname':dbname},function(data){
        if(data.errno != 200){
            alert('删除失败！');
        }else{
            window.location.reload();
        }
    },'json')
})
$(".back_huanyuan").click(function(){
   if(confirm("确认还原！")){
       alert('还在完善中！');
       return ;
       var url ="<?php  echo web_url('database',array('op' =>'import'))?>";
       var sqlfile = $(this).data('dbname');
       $.post(url,{'sqlfile':sqlfile},function(data){
           if(data.errno != 200){
               alert(data.message);
           }else{
               window.location.reload();
           }
       },'json')
   }
})
</script>
<?php  echo $pager;?>
<?php  include page('footer');?>