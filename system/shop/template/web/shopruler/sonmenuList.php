<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">【<?php echo $this_menu['rule_name']; ?>】子菜单节点</h3>
<span data-url="<?php echo web_url('shopruler',array('op'=>'add_sonmenu','pid'=>$this_menu['rule_id']))?>" class="btn btn-primary" onclick="add_menu(this)">添加菜单</span>
&nbsp;&nbsp;<span class="btn btn-danger delete_menu">&nbsp;删&nbsp;除&nbsp;</span> &nbsp;&nbsp;
<a class="btn btn-info" href="<?php echo web_url('shopruler'); ?>">&nbsp;返&nbsp;回&nbsp;</a>

<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;;">
    <thead >
    <tr>
        <th style="text-align:center;min-width:20px;"><input type="checkbox" class="btn btn-xs btn-info choose_all"><span class="box_zi">全选</span></th>
        <th style="text-align:center;min-width:20px;">ID</th>
        <th style="text-align:center; min-width:130px;">菜单名</th>
        <th style="text-align:center; min-width:13px;">规则</th>
<!--        <th style="text-align:center; min-width:30px;">排序</th>-->
        <th style="text-align:center; min-width:30px;">操作类型</th>
        <th style="text-align:center; min-width:130px;">操作</th>
    </tr>
    </thead>
    <form method="post" action="<?php echo web_url('shopruler',array('op'=>'delmenu'));?>" class="del_form">
    <tbody>
    <?php  if(!empty($sonmenu)) { foreach($sonmenu as $item) { ?>
        <tr>
            <td style="text-align:center;"><input type="checkbox"  name="rule_id[]" value="<?php echo $item['rule_id'];?>" class="child_box"></td>
            <td style="text-align:center;"><?php echo $item['rule_id'];?></td>

            <td style="text-align:center;"><?php echo $item['rule_name'];?></td>
            <td style="text-align:center;"><?php echo rtrim($item['url'],'/');?></td>
<!--            <td style="text-align:center;"><input type="text" size="10" value="--><?php //echo $item['sort'];?><!--" name="sort"></td>-->
            <td style="text-align:center;"><?php if($item['act_type'] == 1){ echo 'add';}else if($item['act_type'] == 2){ echo 'edit';};?></td>
            <td style="text-align:center;">
                <span class="btn btn-xs btn-info" onclick="add_menu(this)" data-url="<?php  echo web_url('shopruler', array('op'=>'add_sonmenu','rule_id' => $item['rule_id']))?>"><i class="icon-edit"></i>编辑菜单</span>&nbsp;&nbsp;
                <a class="btn btn-xs btn-danger" href="<?php  echo web_url('shopruler', array('op'=>'delmenu','rule_id' => $item['rule_id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
            </td>
        </tr>
    <?php  } } ?>
    </tbody>
    </form>
</table>

<?php  include page('footer');?>
<script>
    $(".choose_all").click(function(){
        if(this.checked){
            $(".child_box").each(function(){
                this.checked = true;
            })
        }else{
            $(".child_box").each(function(){
                this.checked = false;
            })
        }

    })

    $(".delete_menu").click(function(){
        if(!confirm("确认删除！")){
            return '';
        }
        var i = 0;
        $(".child_box").each(function(){
            if(this.checked){
                i++;
            }
        })
        if(i == 0 ){
            alert('请先选择要删除的菜单选项');
        }else{
            $(".del_form").submit();
        }
    });

    function add_menu(obj){
        var url = $(obj).data('url');
        $.ajaxLoad(url,{},function(){
            $('#alterModal').modal('show');
        })
    }
</script>