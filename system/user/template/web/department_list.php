<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
<style type="text/css">
.department-step-1 .select2-container--default .select2-selection--single{
	border-radius: 0;
}
.department-step-1 .select2-container .select2-selection--single{
	height: 30px;
}
.department-step-1 .department-manager{
	padding-right:5px;
}
</style>
<div class="department-wrap">
	<div class="department-step-1">
		<h3>设置部门经理</h3>
		<ul class="search-ul">
			<li>
				<span class="left-span">部门</span>
				<select class="input-height department">
					<option value="-1">请选择部门</option>
					<option value="1">产品部</option>
					<option value="2">销售部</option>
					<option value="3">客服部</option>
				</select>
			</li>
			<li>
				<span class="left-span">经理</span>
				<select class="input-height department-manager">
					<option value="-1">请选择经理</option>
					<option value="1">张三</option>
					<option value="2">李四</option>
					<option value="3">王五</option>
				</select>
			</li>
			<li>
				<input type="button" name="button" value=" 设 置 "  class="setup btn btn-primary btn-sm">
			</li>
		</ul>	
	</div>
</div>
<?php  echo $pager;?>
<script>
$(function(){
	$(".department-manager").select2();
	//$("#user_search").select2();
	//设置部门经理的异步请求
	$(".setup").on("click",function(){
		var department = $(".department").val();
		var manager = $(".department-manager").val();
		var url = "";
		if( department == -1 || manager == -1){
			alert("请选择部门和经理","",function () {
	          //回调函数
	        }, {type: 'error', confirmButtonText: 'OK'});
		}else{
			$.post(url,{department:department,manager:manager},function(data){
				if(data.message==1){
					alert("设置成功","",function () {
			          //回调函数
			        }, {type: 'success', confirmButtonText: 'OK'});
				}else{
					alert("设置失败","",function () {
			          //回调函数
			        }, {type: 'error', confirmButtonText: 'OK'});
				}
			},"json");
		}
	});
	//设置部门人员
	$(".setup-personnel").on("click",function(){
		var department = $(".department").val();
		var manager = $(".department-manager").val();
		var user_add_arr = [];
		var dest = document.getElementById('user_add');
		$("#user_add option").each(function(index,e){
			user_add_arr.push($(e).val());
		})
		var url = "";
		if( department == -1 || manager == -1){
			alert("请选择部门和经理","",function () {
	          //回调函数
	        }, {type: 'error', confirmButtonText: 'OK'});
		}else{
			$.post(url,{department:department,manager:manager,addUser:user_add_arr},function(data){
				if(data.message==1){
					alert("设置成功","",function () {
			          //回调函数
			        }, {type: 'success', confirmButtonText: 'OK'});
				}else{
					alert("设置失败","",function () {
			          //回调函数
			        }, {type: 'error', confirmButtonText: 'OK'});
				}
			},"json");
		}
	})
})
function addUser(){
  var src = document.getElementById('user_search');
  var dest = document.getElementById('user_add');

  for (var i = 0; i < src.options.length; i++)
  {
      if (src.options[i].selected)
      {
          var exist = false;
          for (var j = 0; j < dest.options.length; j++)
          {
              if (dest.options[j].value == src.options[i].value)
              {
                  exist = true;
                  break;
              }
          }
          if (!exist)
          {
              var opt = document.createElement('OPTION');
              opt.value = src.options[i].value;
              opt.text = src.options[i].text;
              dest.options.add(opt);
          }
      }
  }
}
function delUser(){
  var dest = document.getElementById('user_add');

  for (var i = dest.options.length - 1; i >= 0 ; i--)
  {
      if (dest.options[i].selected)
      {
          dest.options[i] = null;
      }
  }
}
function validate(){
    var dest = document.getElementById('user_add');
    for (var i = 0; i < dest.options.length; i++)
    {
        dest.options[i].selected = "true";
    }
   	return true;
}

</script>
<?php  include page('footer');?>