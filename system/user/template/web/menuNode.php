<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<form action="<?php echo web_url('user',array('op'=>'menu','act'=>'delete'));?>" method="post" class="del_form">
<h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">菜单节点</h3><a href="<?php echo web_url('user',array('op'=>'menu','act'=>'post'))?>" class="btn btn-primary">添加菜单</a> &nbsp;&nbsp;<span class="btn btn-danger delete_menu">&nbsp;删&nbsp;除&nbsp;</span>&nbsp;&nbsp;<a href="<?php echo web_url('user',array('op'=>'cleanMenu'))?>" class="btn btn-danger">清除节点缓存</a>
<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;;">
    <thead >
    <tr>
        <th style="text-align:center;min-width:20px;"><input type="checkbox" class="btn btn-xs btn-info choose_all"><span class="box_zi">全选</span></th>
        <th style="text-align:center;min-width:20px;">ID</th>
        <th style="text-align:center; min-width:130px;">菜单名</th>
        <th style="text-align:center; min-width:13px;">规则</th>
        <th style="text-align:center; min-width:30px;">排序</th>       
        <th style="text-align:center; min-width:130px;">操作</th>
        
    </tr>
    </thead>
    <tbody>
   <?php  if(!empty($data)) {
        foreach($data as $cat_id => $arr) {    ?>

        <tr><td colspan="6"><strong style="margin-left:10px;"><?php echo $arr[0]['cat_name'];?></strong> &nbsp;&nbsp;
                <span class="btn btn-xs btn-info" data-catid="<?php echo $cat_id?>" onclick="addMenu(this)">添加菜单</span>
        </td></tr>

    <?php foreach($arr as $item){ ?>

        <tr>
            <td style="text-align:center;"><input type="checkbox" class="child_box" name="id[]" value="<?php echo $item['id'];?>" ></td>
            <td style="text-align:center;"><?php echo $item['id'];?></td>
            <td style="text-align:center;"><?php echo $item['moddescription'];?></td>
            <td style="text-align:center;"><?php echo rtrim($item['url'],'/');?></td>
            <td style="text-align:center;">            	
            	<input style="text-align: center;width: 60px;" type="number" min="<?php echo $cat_id-10?>" max="<?php echo $cat_id+10?>" value="<?php echo $item['sort'];?>" name="sort">
            </td>
            <td style="text-align:center;">
                <a class="btn btn-xs btn-info"  href="<?php  echo web_url('user', array('op'=>'sonMenuList','id' => $item['id']))?>"><i class="icon-edit"></i>子菜单</a>&nbsp;&nbsp;
                <a class="btn btn-xs btn-info"  href="<?php  echo web_url('user', array('op'=>'menu','act'=>'post','id' => $item['id']))?>"><i class="icon-edit"></i>编辑菜单</a>&nbsp;&nbsp;
                 <?php if($item['top_menu'] == 0){ ?>
                     <a class="btn btn-xs btn-warning top_menu" href="javascript:;" data-topmenu="0" data-url="<?php  echo web_url('user', array('op'=>'top_nemu','id' => $item['id']))?>"><i class="icon-edit">设为快捷菜单</i></a>&nbsp;&nbsp;
                <?php }else{  ?>
                     <a class="btn btn-xs btn-danger top_menu"  href="javascript:;" data-topmenu="1" data-url="<?php  echo web_url('user', array('op'=>'top_nemu','id' => $item['id']))?>"><i class="icon-edit">取消快递菜单</i></a>&nbsp;&nbsp;
                <?php } ?>
                 <a class="btn btn-xs btn-danger" href="<?php  echo web_url('user', array('op'=>'menu','act'=>'delete','id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
            </td>
        </tr>
<?php     }  } } ?>
    </tbody>
</table>
</form>
<?php  include page('footer');?>
<script>
    function  addMenu(obj){
        var cat_id = $(obj).data('catid');
        var url = "<?php echo web_url('user',array('op'=>'menu','act'=>'post'))?>";
        url += "&cat_id="+cat_id;
        window.location.href = url;
    }

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
    })    
    
  
	$("input[name=sort]").blur(function(){
  		//如果输入的值大于设定的最大值，就显示最大值
  		var maxval = parseInt($(this).attr("max"));   			
     	var sort = parseInt($(this).val());
        var minval = $(this).attr("min")
        console.log("maxval:"+maxval);
        console.log("minval:"+minval);
        console.log("sort:"+sort);
        console.log($(this).val())
		if(sort > maxval){			
			$(this).val(maxval);
            sort = maxval;
		}
	  	//获取id
	  	var ID = $(this).parent().parent().children().eq(0).children().val();
        var url = "<?php echo web_url('user',array('op'=>'menusort'));?>";
        url = url+"&id="+ID+"&sort="+sort;
        $.getJSON(url,function(){

        })
	  		  	
	})

    $(".top_menu").click(function(){
        var topmenu = $(this).data('topmenu');
        var url     = $(this).data('url');
        if(topmenu == 0){
            //设置快捷
            topmenu = 1;
        }else{
            //取消快捷
            topmenu = 0;
        }
        url = url + "&topmenu="+topmenu;
        var obj = this;
        $.getJSON(url,function(data){
            if(data.errno == 200){
                $(obj).data('topmenu',topmenu);
                if(topmenu == 1){
                    console.log('1111111');
                    $(obj).removeClass('btn-warning');
                    $(obj).addClass('btn-danger');
                    $(obj).find('i').html('取消快捷菜单');
                }else{
                    console.log('00000000');
                    $(obj).removeClass('btn-danger');
                    $(obj).addClass('btn-warning');
                    $(obj).find('i').html('设为快捷菜单');
                }
            }
        },'json')
    })
</script>
