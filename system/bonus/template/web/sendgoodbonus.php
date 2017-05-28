<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
<style type="text/css">
    .dummy-table-list{
    border: 1px solid #ddd;padding: 7px 0;
  }
  .dummy-table-list tr{
    background-color: #fff!important;
  }
  .header .dummy-table-list li{
    float: left;    
    margin-right: 10px;
    list-style-type: none;
  }
  .dummy-table-list .li-height{
      height: 30px;
      padding-left: 5px;
  }
  .search-success{
    color: #1ac11a;
    display: none;
    margin-left: 10px;
  }
</style>
<h3 class="header smaller lighter blue">发放优惠券</h3>


        <table class="table table-striped table-bordered table-hover dummy-table-list">
			<tbody >
				<tr>
				<td>
				<li style="float:left;list-style-type:none;">
						<select  style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" name="cate_1" onchange="fetchChildCategory(this.options[this.selectedIndex].value)">
							<option value="0">请选择一级分类</option>
							<?php  if(is_array($category)) { foreach($category as $row) { ?>
							<?php  if($row['parentid'] == 0) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['cate_1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
						<select style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" id="cate_2" name="cate_2">
							<option value="0">请选择二级分类</option>
							<?php  if(!empty($_GP['cate_1']) && !empty($children[$_GP['cate_1']])) { ?>
							<?php  if(is_array($children[$_GP['cate_1']])) { foreach($children[$_GP['cate_1']] as $row) { ?>
							<option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $_GP['cate_2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
							<?php  } } ?>
							<?php  } ?>
						</select>
						
						</li>
						
						<li style="list-style-type:none;">
							<input type="button" name="search_goods" value="搜索" class="btn btn-primary btn-sm search_goods">
              <i class="search-success icon-ok"></i>
						</li>
					</td>
				</tr>
			</tbody>
		</table>

<form action="" class="form-horizontal" method="post" onsubmit="return validate()" >
<table class="table" style="width:99%;" align="center">
					<tbody>
						<tr>
							<td>
								
								<table cellspacing="1" cellpadding="3">
  <tbody><tr>
    <th>可选商品：(选中商品后，可及时添加到右侧)</th>
    <th>操作</th>
    <th>发放此类型优惠券的商品</th>
  </tr>
  <tr>
    <td width="45%" align="center">
      <select name="good_search[]" id="good_search" size="15" style="width:450px" ondblclick="addUser()" multiple="true">
      					<?php foreach($goodslist as $good){?>
     				  				<option value="<?php echo $good['id']?>"><?php echo $good['title']?></option> 
     							<?php }?>
      
      </select>
    </td>
    <td align="center">
      <p><input type="button" value=">" onclick="addGood()" class="button"></p>
      <p><input type="button" value="<" onclick="delGood()" class="button"></p>
    </td>
    <td width="45%" align="center">
      <select name="good_add[]" id="good_add" multiple="true" size="15" style="width:450px" ondblclick="delUser()">
      	<?php foreach($bonus_good_list as $good){?>
     				  				<option value="<?php echo $good['good_id']?>"><?php echo $good['title']?></option> 
     							<?php }?>
      </select>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="3">
    	<input type="submit" name="send_goods" value="确定发送优惠券" class="btn btn-primary"></td>
  </tr>
</tbody></table>
								
								</td>
						
						</tr>
					</tbody>
				</table>
		</form>
		
			<script language="javascript">
      $("#good_search").select2();
		var category = <?php  echo json_encode($children)?>;
   function fetchChildCategory(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!category || !category[cid]) {
		$('#cate_2').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
}

function validate()
{
    var dest = document.getElementById('good_add');
    for (var i = 0; i < dest.options.length; i++)
    {
        dest.options[i].selected = "true";
    }
   	return true;
}

function addGood()
  {
      var src = document.getElementById('good_search');
      var dest = document.getElementById('good_add');

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

  function delGood()
  {
      var dest = document.getElementById('good_add');

      for (var i = dest.options.length - 1; i >= 0 ; i--)
      {
          if (dest.options[i].selected)
          {
              dest.options[i] = null;
          }
      }
  }

function addUser(){
	addGood();
}
function delUser(){
	delGood();
}
$(".search_goods").click(function(){
    var p1 = $("select[name='cate_1']").val();
    var p2 = $("select[name='cate_2']").val();
    var url = "<?php echo web_url('sendbonus',array('op'=>'searchgood')); ?>"
    $.post(url,{'p1':p1,'p2':p2},function(data){
        if(data.errno == 200){
            var obj = data.message;
            var html = '';
            for(var i=0;i<obj.length;i++){
                var o_data = obj[i];
                html = html+"<option value='"+o_data.id+"'>"+o_data.title+"</option>";
            }
            $(".search-success").show();
            $("#good_search").html(html);
            setTimeout(function(){
              $(".search-success").hide();
            },3000)
        }else{
            alert(data.message);
        }
    },'json');
})
</script>
			
<?php  include page('footer');?>
								