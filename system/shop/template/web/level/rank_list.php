<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style type="text/css">
	.grade-table{
		text-align: center;
	}
	.grade-table th{
		text-align: center;
	}
</style>
<div class="panel with-nav-tabs panel-default" style="margin-top: 20px;">	
	<div class="panel-heading">
	            <ul class="nav nav-tabs">
	                <!--<li class="active"><a href="#tab1primary" data-toggle="tab">基础信息</a></li>-->
	                <li id="second" class="active"><a href="#tab2primary" data-toggle="tab">商铺等级</a></li>
	                <!--<li id="third"><a href="#tab3primary" data-toggle="tab">商铺等级特权</a></li>-->
	            </ul>
	    </div>
	    <div class="panel-body third-party">
	        <div class="tab-content">
	            <div class="tab-pane fade in active" id="tab2primary">
					<div class="alert alert-info" style="margin:10px 0; width:auto;display: none" id="rank_top_tip">
						<i class="icon-lightbulb"></i> <span class="tip_txt"></span>
					</div>
	            	<h3 class="header smaller lighter blue">商铺等级管理&nbsp;&nbsp;&nbsp;<a href="<?php  echo web_url('shop_level_manage',array('op'=>'add'));?>" class="btn btn-primary">新建等级</a></h3>
                    <form action="<?php  echo web_url('shop_level_manage',array('op'=>'sectionPost')) ?>" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
                    <table class="table table-striped table-bordered table-hover">
						<thead >
							<tr>
								<th style="text-align:center;">等级(数字)</th>
								<th style="text-align:center;">等级名称</th>
								<th style="text-align:center;">icon</th>
								<th style="text-align:center;">wap_icon</th>
                                <th style="text-align:center;">等级</th>
                                <th style="text-align:center;">免费/收费</th>
                                <th style="text-align:center;">可上架商品数量</th>
                                <th style="text-align:center;">有效期（年）</th>
                                <!--<th style="text-align:center;">商家数量</th>-->
								<th style="text-align:center;">操作</th>
							</tr>
						</thead>
						<tbody>
						 <?php  if(is_array($list)) { foreach($list as $v) { ?>
                            <!--等级(数字)-->
							<tr>
									<td class="text-center">
									<?php  echo $v['rank_level'];?>
								</td>
                                 <!--等级(等级名称)-->
								<td class="text-center">
									<?php  echo $v['rank_name'];?>
								</td>
                                 <!--等级(icon)-->
								<td class="text-center">
									<?php  if ($v['icon']){ echo "<img width='25' src='{$v['icon']}'/>"; };?>
								</td>
                                <!--等级(wap_icon)-->
								<td class="text-center">
									<?php  if ($v['wap_icon']){ echo "<img width='25' src='{$v['wap_icon']}'/>"; };?>
								</td>
                                <!--等级-->
                                <td class="text-center">
									<?php echo $this->typeText[$v['level_type']];?>
								</td>
                                 <!--是否免费-->
                                <td class="text-center">
									<?php echo $v['is_free']==1?"免费":"收费";?>
                                    <?php if($v['is_free']!=1 ){ ?>
                                        <input type="text" name="money[<?php echo  $v['rank_level']?>]"  value="<?php echo  $v['money']?>" style="width: 60px;text-align: center" data-id="<?php echo  $v['rank_level']?>">
                                    <?php   } ?>
								</td>
                                  <!--可上架商品数量-->
                                <td class="text-center">
                                    <input type="text" name="dish_num[<?php echo  $v['rank_level']?>]"  value="<?php echo  $v['dish_num']?>" style="width: 60px;text-align: center" >
								</td>
                                   <!--有效期-->
                                <td class="text-center">
                                    <input type="text" name="time_range[<?php echo  $v['rank_level']?>]"  value="<?php echo  $v['time_range']?>" style="width: 60px;text-align: center" >
								</td>
								<td class="text-center">
                                                                      
                                    &nbsp;&nbsp;<a  class="btn btn-xs btn-success"  data-url="<?php echo web_url('shop_level_manage',array('op'=>'dialogChoose','rank_level'=>$v['rank_level'],'level_type'=>$v['level_type'])); ?>" href="javascript:;" onclick="chooseType(this)"><i class="icon-edit"></i>&nbsp;调整级别&nbsp;</a>&nbsp;&nbsp;

									&nbsp;&nbsp;<a  class="btn btn-xs btn-info" href="<?php  echo web_url('shop_level_manage',array('op'=>'edit','rank_level' => $v['rank_level']));?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
																

									&nbsp;&nbsp;<a  class="btn btn-xs btn-danger" href="<?php  echo web_url('shop_level_manage',array('op'=>'delete','rank_level' => $v['rank_level']));?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>&nbsp;&nbsp;
									</td>
							</tr>
							<?php  } } ?>
                            <tr>
                                <td colspan="9">
                                <input name="submit" type="submit" class="btn btn-primary" value=" 提 交 ">
                                </td>
                            </tr>
						  </tbody>
			    	</table>
                    </form>
	            </div>
	        </div>
	    </div>
</div>
<?php  echo $pager;?>
<?php  include page('footer');?>


<script type="text/javascript">
$(function(){
	var td_index = 0 ;
	$(".grade-check-input").on("click",function(){
		var $this = $(this);
		td_index =parseInt($this.parent("td").index());
		var ischeck = $this.prop("checked");
		if(ischeck){
			$this.parents("td").siblings("td").each(function(index,ele){
				if( parseInt(index) >= td_index){
					$(ele).find("input").prop("checked",true);
				}
			})
		}else{
			$this.parents("td").siblings("td").each(function(index,ele){
				if( parseInt(index) < td_index){
					$(ele).find("input").prop("checked",false);
				}
			})
		}
	});
})
function chooseType(obj){
    var url = $(obj).data('url');
    $.ajaxLoad(url,{},function(){
        $('#alterModal').modal('show');
    })
}
function locationHash(){
	var home = location.hash;
	if( home == "#tab2primary" ){
		$('#second a').tab('show');
	}else if(home == "#tab3primary" ){
		$('#third a').tab('show');
	}
}
locationHash();

$(".set_order").blur(function(){
	var old_sort = $(this).data('sort');
	var id       = $(this).data('id');
	var new_sort = $(this).val();
	if(new_sort != old_sort){
		var url = "<?php echo web_url('rank',array('name'=>'member','op'=>'set_sort'));?>";
		$.post(url,{'sort':new_sort,'id':id},function(data){
			if(data.errno == 200){
				$("#top_tip .tip_txt").html(data.message);
				$("#top_tip").fadeIn();
				setTimeout(function(){
					$("#top_tip").fadeOut();
				},2000)
			}
		},'json')
	}
});

$(".del_this").click(function(){
	var obj = this;
	confirm("确认删除么？", "", function (isConfirm) {
		if (isConfirm) {
			//after click the confirm
			var id = $(obj).data('id');
			var url = "<?php echo web_url('rank',array('name'=>'member','op'=>'del_privile'));?>";
			url = url + "&id="+id;
			$.post(url,{},function(data){
				if(data.errno == 200){
					$("#top_tip .tip_txt").html(data.message);
					$("#top_tip").fadeIn();
					setTimeout(function(){
						$("#top_tip").fadeOut();
					},2000)
					$(obj).parent().parent().remove();
				}
			},'json')
		}
	}, {confirmButtonText: '确认删除', cancelButtonText: '取消删除', width: 400});
});

$(".rank_order").blur(function(){
	var old_sort = $(this).data('sort');
	var id       = $(this).data('id');
	var new_sort = $(this).val();
	if(new_sort != old_sort){
		var url = "<?php echo web_url('rank',array('name'=>'member','op'=>'rank_sort'));?>";
		$.post(url,{'sort':new_sort,'id':id},function(data){
			if(data.errno == 200){
				$("#rank_top_tip .tip_txt").html(data.message);
				$("#rank_top_tip").fadeIn();
				setTimeout(function(){
					$("#rank_top_tip").fadeOut();
				},2000)
			}
		},'json')
	}
});

</script>