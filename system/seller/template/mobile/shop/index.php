<!DOCTYPE html>
<html>
	<head>
        <?php include page('seller_header');?>
        <!--不做页面提交，用ajaxsubmit提交和控制回调-->
        <script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/script/jquery.form.js"></script>
	</head>
	<body style="padding:10px;">
    	<blockquote class="layui-elem-quote">店铺信息</blockquote>
        <input type="hidden" name="id" value="<?php  echo $storeInfo['sts_id']; ?>">
        <div class="stop-info clearfix">
            <!-- 店铺信息开始 -->
            <div class="stop-info-l">
                <div class="stop-info-title">店铺信息</div>
                <div class="clearfix">
                    <div class="userface"><img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201704/20170401075058df5bb62dd84.jpg"></div>
                    <div class="user-infos">
                        <ul>
                            <li>
                                <span class="user-infos-1">店铺名：</span>
                                <span style="position:relative">
                                    <span class="username-span"><?php echo $storeInfo['sts_name'] ?></span>
                                    <input type="text" class="edit-username-input">
                                </span>
                                <span>
                                    <i class="layui-icon edit-icon" onclick="username()">&#xe642;</i>
                                </span>
                            </li>
                            <li>
                                <span class="user-infos-1">配送范围：</span><span><?php echo region_func_getNameByCode($storeInfo['sts_province'])."-".region_func_getNameByCode($storeInfo['sts_city'])."-".region_func_getNameByCode($storeInfo['sts_region']) ?></span>
                            </li>
                            <li>
                                <span class="user-infos-1">经营业务：</span><span><?php echo getIndustryNameByID($storeInfo['sts_category_p1_id'])."-".getIndustryNameByID($storeInfo['sts_category_p2_id']) ;?></span>
                            </li>
                            <li>
                                <span class="user-infos-1">注册时间：</span><span><?php echo  date('Y-m-d',$storeInfo['sts_creatime']);  ?></span>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                <div class="layui-btn layui-btn-primary layui-btn-small"><?php echo  $storeInfo['level_type_text']?></div>
                                <div class="layui-btn layui-btn-primary layui-btn-small"><?php echo $storeInfo['sts_info_status_text'];  ?></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- 店铺信息结束 -->
            <!-- 店铺资质开始 -->
            <div class="stop-info-r">
                <div class="stop-info-title">店铺资质</div>
                <div class="clearfix">
                    <div class="user-infos">
                        <ul>
                            <li>
                                <span class="user-infos-1">店铺等级：</span>
                                <span><?php echo $storeInfo['rank_name'] ?></span>
                                <input type="button" onclick="chooseType(this);" class="layui-btn layui-btn-small" data-url="<?php echo mobile_url('store_shop',array('op'=>'dialogCharge','id'=>$storeInfo['sts_id']))?>" value="升级">
                            </li>
                            <li>
                                <span class="user-infos-1">有效期至：</span><span> <?php if( $storeInfo['sts_level_valid_time'] ) echo  date('Y-m-d H:i:s',$storeInfo['sts_level_valid_time'] ) ?></span>
                            </li>
                            <li>
                                <span class="user-infos-1">年审状态：</span><span><?php echo $storeInfo['sts_info_status_text'];  ?></span>
                            </li>
                            <?php if( $storeInfo['sts_level_valid_time'] ) {?>
<!--                                <li style="margin-top:5px;">
                                    <div class="layui-btn layui-btn-small">查看年审</div>
                                </li>-->
                            <?php }?>
                            <?php if( $storeInfo['sts_level_valid_time'] >= $_SERVER['REQUEST_TIME'] ) {?>    
                                <li class="limited-state">
                                    <i class="layui-icon" style="margin-right:5px;vertical-align: middle;">&#xe610;</i>
                            <?php if( $storeInfo['sts_info_status'] == 0 ) {?>        您已经通过年审。<?php } ?>
                            <!--有效期续期一年。-->
                                <?php if($storeInfo['level_type']==1) echo "可上架商品：". $storeInfo['dish_num'];  ?>
                                </li>
                            <?php }?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- 店铺资质结束 -->
        </div>
        <!-- tab切换 -->
        <div class="layui-tab layui-tab-card">
            <ul class="layui-tab-title">
                <li class="layui-this">店铺信息</li>
                <li>资质信息</li>
            </ul>
            <div class="layui-tab-content">
            <!-- 账户资质信息开始 -->
                <div class="layui-tab-item layui-show">
                                        
                    <form  id="formregion" onsubmit="postRegion();return false;" action="">
                    <table class="layui-table">
                        <tbody>
                            <tr>
                                <td class="stop-table-td-1">店铺名</td>
                                <td><?php echo $storeInfo['sts_name'] ?></td>
                               
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">实体店</td>
                                <td><?php echo $storeInfo['sts_physical_shop_name'] ?></td>

                            </tr>
                            <tr>
                                <td class="stop-table-td-1">联系人</td>
                                <td><?php echo $storeInfo['sts_contact_name'] ?></td>

                            </tr>
                            <tr>
                                <td class="stop-table-td-1">手机号</td>
                                <td><?php echo $storeInfo['sts_mobile'] ?></td>

                            </tr>
                            <tr>
                                <td class="stop-table-td-1">微信号</td>
                                <td><?php echo $storeInfo['sts_weixin'] ?></td>

                            </tr>
                            <tr>
                                <td class="stop-table-td-1">QQ号</td>
                                <td><?php echo $storeInfo['sts_qq'] ?></td>

                            </tr>
                            <tr>
                                <td class="stop-table-td-1">店铺简介</td>
                                <td><?php echo $storeInfo['sts_summary'] ?></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">所在地区</td>
                                <td>   
                                        <?php if( $store_shop_identity_info&& $store_shop_identity_info['ssi_shenhe_region']==2 ){ echo "原提交信息：";} ?>
                                <?php echo region_func_getNameByCode($storeInfo['sts_locate_add_1'])."-".region_func_getNameByCode($storeInfo['sts_locate_add_2'])."-".region_func_getNameByCode($storeInfo['sts_locate_add_3']) ?>
    <?php if( $store_shop_identity_info&& $store_shop_identity_info['ssi_shenhe_region']==2 ){ ?>
                                    <select  id="cate_1" onchange="fetchChildCategory(this.options[this.selectedIndex].value)"   lay-filter="cate_1" style="margin-right:15px;margin-left: 50px;"  name="cate_1" class="pcates"   autocomplete="off">
                                        <option value="0">请选择一级城市</option>
                                            <?php if (is_array($result)) {foreach ($result as $row) {?>
                                                    <?php if ($row['parent_id'] == 0) { ?>
                                        <option value="<?php echo $row['region_id']; ?>" data-code="<?php echo $row['region_code']; ?>" <?php if($row['region_id'] == $storeInfo['sts_locate_add_1_id'] ){?>selected="selected" <?php } ?>   ><?php echo $row['region_name']; ?></option>
                                                    <?php } ?>
                                                <?php }
                                            } ?>
                                    </select>
                                    <select  id="cate_2" onchange="fetchChildCategory2(this.options[this.selectedIndex].value)"  lay-filter="cate_2"    name="cate_2" class="cates_2"  autocomplete="off">
                                        <option value="-1">请选择二级城市</option>
                                        <?php if (is_array($childrens[ $storeInfo['sts_locate_add_1_id'] ])) {foreach ($childrens[ $storeInfo['sts_locate_add_1_id'] ] as $row) {?>
                                            <option  value="<?php echo $row['0']; ?>" data-code="<?php echo $row[2]; ?>" <?php if ($row['2'] == $storeInfo['sts_locate_add_2']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
                                        <?php }} ?>
                                    </select>
                <select  id="cate_3" name="cate_3" autocomplete="off" >
                <option value="0">请选择三级城市</option>
                <?php if (is_array($childrens[  $storeInfo['sts_locate_add_2_id'] ])) {foreach ($childrens[  $storeInfo['sts_locate_add_2_id'] ] as $row) {?>
                    <option value="<?php  echo $row['0'];?>" data-code="<?php echo $row[2]; ?>" <?php  if($row['0'] == $storeInfo['sts_locate_add_3_id']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
                <?php  } }  ?>
            </select>
    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">详细地址</td>
                                <!--这地方要百度地图展示-->
                                <td><?php echo $storeInfo['sts_address'] ?></td>
                            </tr>

                        </tbody>
                    </table>
                    </form>
                </div>
                <!-- 账户资质信息结束 -->
                
                <!-- 账户入驻信息开始 -->
                <div class="layui-tab-item">
                                    
                    <form class="layui-form" id="formtag" onsubmit="postFileForm();return false;" action="">
                    <input type="hidden" name="ssi_id" value="<?php  echo $store_shop_identity_info['ssi_id']; ?>">
                        <table class="layui-table">
                        <tbody>
                            <tr>
                                <td class="stop-table-td-1">法人</td>
                                <td><?php echo $store_shop_identity_info['ssi_owner_name'] ?></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">身份证号</td>
                                <td><?php echo cbd_decrypt($store_shop_identity_info['ssi_owner_shenfenhao'],$member['openid']); ?></td>
                            </tr>
                         
                            <tr>
                                <td class="stop-table-td-1">身份证照</td>
                                <td> <img src="<?php  echo $store_shop_identity_info['ssi_shenfenzheng'];?>" height="120" width="120"></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">营业执照</td>
                                <td> <img src="<?php  echo $store_shop_identity_info['ssi_yingyezhizhao'];?>" height="120" width="120"></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">许可证</td>
                                <td> 
                               
                                    <img src="<?php  echo $store_shop_identity_info['ssi_xukezheng'];?>" height="120" width="120">
                                </td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">店面</td>
                                <td> <img src="<?php  echo $store_shop_identity_info['ssi_dianmian'];?>" height="120" width="120"></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">店内环境图</td>
                                <td> <img src="<?php  echo $store_shop_identity_info['ssi_diannei'];?>" height="120" width="120"></td>
                            </tr>
                        </tbody>
                    </table>
                    </form>
                </div>
               
                <!-- 账户入驻信息结束 -->
            </div>
        </div>
        <?php include page('seller_footer');?>
	</body>

<script type="text/javascript">
     var category = <?php echo json_encode($childrens) ?>;//    省市区JS
     
function chooseType(obj){
    var url = $(obj).data('url');
    $.ajaxLoad(url,{},function(){
        $('#alterModal').modal('show');
        //获取已选择分类的隐藏域值
    });
}

     
     
layui.use(['form','element','layer'], function(){
  var $ = layui.jquery
  ,element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
  var form = layui.form();
//    省市区JS   START ↓
   
});
   function fetchChildCategory(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!category || !category[cid]) {
		$('#cate_2').html(html);
					fetchChildCategory2(document.getElementById("cate_2").options[document.getElementById("cate_2").selectedIndex].value);

		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
	fetchChildCategory2(document.getElementById("cate_2").options[document.getElementById("cate_2").selectedIndex].value);

}
  function fetchChildCategory2(cid) {
	var html = '<option value="0">请选择三级分类</option>';
	if (!category || !category[cid]) {
		$('#cate_3').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cate_3').html(html);
 }
 
  function postRegion() {
      var fdata=  {
                cate_1:  $("#cate_1 option:selected").attr('data-code'), 
                cate_2:  $("#cate_2 option:selected").attr('data-code'), 
                cate_3:  $("#cate_3 option:selected").attr('data-code'), 
                id: $("input[name=id]").val()
            };
	//var data= $('#formregion').serialize();
    //console.log(data);return false;
    var url= '<?php echo mobile_url('shop',array('op'=>'changeStoreInfo')) ?>';
    $.post(url,fdata,function(data){
        if(data.errno==1){
           layer.open({
                content: '我们已经收到您提交的申请，服务人员将在24小时内处理，请您耐心等待。',
                yes: function(index, layero){
                  layer.close(index); //如果设定了yes回调，需进行手工关闭
                  window.location.href=window.location.href; 
                    window.location.reload;     
                }
            });    
        }else{
            alert(data.message);
        }

    },'json')
 }
 
  function postFileForm() {
	//var data= $('#formtag').serialize();
    //console.log(data);return false;
    var url= '<?php echo mobile_url('shop',array('op'=>'changeIdentityInfo')) ?>';
    $("#formtag").ajaxSubmit({
                type: "post",
                url: url,
                dataType: "json",
                success: function(ret){
                    //返回提示信息       
                    if(ret.errno==1){
                        layer.open({
                            content: '我们已经收到您提交的申请，服务人员将在24小时内处理，请您耐心等待。',
                            yes: function(index, layero){
                              layer.close(index); //如果设定了yes回调，需进行手工关闭
                               window.location.href=window.location.href; 
                                window.location.reload;     
                            }
                        });        
                    }else{
                        layer.open({title: '提示',content: data.message});
                    }
                }
            });
            return false;
 }
$(function(){
    

})
</script>
</html>