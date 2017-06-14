<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<br/>
    <ul class="nav nav-tabs" >
        <li style="width:7%" <?php  if(empty($_GP['op'])) { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('shopruler')?>">权限规则</a></li>
        <li style="width:7%" <?php  if($_GP['op'] == 'group') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('shopruler',  array('op' => 'group'))?>">权限分组</a></li>
    </ul>

    <h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">权限分组
    &nbsp;&nbsp;<a class="btn btn-md btn-info" href="<?php echo web_url('shopruler',array('op'=>'addgroup'));?>">添加分组</a>
    </h3>
    <table class="table table-striped table-bordered table-hover" style="margin-top: 15px;;">
        <thead >
        <tr>
            <th style="text-align:center;min-width:20px;">序号</th>
            <th style="text-align:center; min-width:130px;">角色名称</th>
            <th style="text-align:center; min-width:13px;">简介</th>
            <th style="text-align:center; min-width:30px;">创建时间</th>
            <th style="text-align:center; min-width:30px;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($group as $key=> $arr) {    ?>

            <tr>
                <td style="text-align: center"><?php echo ++$key;?></td>
                <td style="text-align: center">
                    <?php echo $arr['group_name'];?>
                </td>
                <td style="text-align: center">
                    <?php echo $arr['description'];?>
                </td>
                <td style="text-align: center">
                    <?php echo date("Y-m-d H:i",$arr['createtime']);?>
                </td>
                <td style="text-align: center">
                    <a href="<?php echo  web_url('shopruler',array('op'=>'editgroup','group_id'=>$arr['group_id']));?>" class="btn btn-md btn-info">编辑分组</a>
                </td>
            </tr>

        <?php } ?>
        </tbody>
    </table>

<?php  include page('footer');?>