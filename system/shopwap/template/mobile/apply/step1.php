<?php  include page('h'); ?>
<link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/font-awesome/4.6.0/css/font-awesome.min.css">
<link type="text/css" href="<?php echo RESOURCE_ROOT;?>addons/common/bootstrap3/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/apply.css" />
  <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/clipboard.min.js"></script>
  <link rel="stylesheet" href="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/css/cropper.min.css" media="all" />
<link rel="stylesheet" href="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/css/main.css" media="all" />
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/script/jquery.form.js"></script>

<!--加載高德地圖START -->
<link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=202a9a4762b30ac29f750d7aa8c179e1&plugin=AMap.CitySearch"></script>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=8ecaa654c1cb4291647a565c375cc5ae&plugin=AMap.Geocoder"></script>
<script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
<!--加載高德地圖END -->
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/html5shiv.min.js"></script>
<script src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/respond.min.js"></script>
<![endif]-->
<style>

</style>

<!-- 商家入住 -->
<div class="layui-form form-area">
    <!-- 入驻类型 -->
    <div class="layui-form-item">
        <label class="layui-form-label">入驻类型</label>
        <div class="layui-input-inline">
            <select name="sts_shop_type" lay-filter="pingtai_group_id" id="sts_shop_type">
                <option value="1" <?php  if( $info['sts_shop_type']==1 ) echo 'selected=true'; ?> >交收商铺</option>
                <option value="2"  <?php  if( $info['sts_shop_type']==2 ) echo 'selected=true'; ?> >集团大客户</option>
                <option value="3"  <?php  if( $info['sts_shop_type']==3 ) echo 'selected=true'; ?> >合作客户</option>
            </select>
        </div>
    </div>
    <!-- 配送范围 -->
    <div class="layui-form-item">
        <label class="layui-form-label">配送范围</label>
        <div class="layui-input-inline">
             <select  id="cate_1" onchange="fetchChildCategory(this, this.options[this.selectedIndex].value)" lay-filter="cate_1" style="margin-right:15px;"  name="cate_1" class="pcates"   autocomplete="off">
                                <option value="0">请选择一级城市</option>
                                <?php if (is_array($result)) {
                                    foreach ($result as $row) {
                                        ?>
                                        <?php if ($row['parent_id'] == 0) { ?>
                                            <option value="<?php echo $row['region_id']; ?>" data-code="<?php echo $row['region_code']; ?>"  <?php if ($row['region_code'] == $info['sts_province']) { ?> selected="selected"<?php } ?>><?php echo $row['region_name']; ?></option>
                                        <?php } ?>
                                    <?php }
                                }
                                ?>
                        </select>
        </div>
        <div class="layui-input-inline">
            <select  id="cate_2"  lay-filter="cate_2" onchange="fetchChildCategory2(this, this.options[this.selectedIndex].value)"    name="cate_2" class="cate_2"  autocomplete="off">
                <option value="-1">请选择二级城市</option>
    <?php if (!empty($info['sts_province_id']) && !empty($childrens[$info['sts_province_id']])) { ?>
                    <?php if (is_array($childrens[$info['sts_province_id']])) {
                        foreach ($childrens[$info['sts_province_id']] as $row) { ?>
                            <option  data-code="<?php echo $row['2']; ?>"   value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $info['sts_city_id']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
        <?php }} ?>
<?php } ?>
            </select>
        </div>
        <div class="layui-input-inline">
            <select  id="cate_3" name="cate_3" lay-filter="cate_3"  autocomplete="off" onchange="fetchMap()">
                    <option value="0">请选择三级城市</option>
        <?php if (!empty($info['sts_city_id']) && !empty($childrens[$info['sts_city_id']])) { ?>
                    <?php if (is_array($childrens[$info['sts_city_id']])) {
                        foreach ($childrens[$info['sts_city_id']] as $row) { ?>
                            <option  data-code="<?php echo $row['2']; ?>"   value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $info['sts_qu_id']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
        <?php }} ?>
<?php } ?>
            </select>
        </div>
        <div class="layui-form-mid layui-word-aux"><img class="prompt" src="<?php echo RESOURCE_ROOT;?>addons/seller/images/prompt.png">每个配送范围内只允许固定数量主营业务的加盟商。</div>
    </div>
  
    <!-- 主营业务 -->
              <div class="layui-form-item">
                    <label class="layui-form-label">主营业务</label>
                    <div class="layui-input-block choosetype-list">
            <div class="left-list">
                <ul class="parent-type">
                    <?php if (is_array($catStruct)) { foreach ($catStruct as $row) { ?>
                    <li    <?php if ($row['gc_id'] == $info['sts_category_p1_id']){echo 'class="li-check"';} ?> >
                        <span class="parent-type-val" data-value="<?php echo $row['gc_id']; ?>" type-id="<?php echo $row['gc_id']; ?>"><?php echo $row['gc_name']; ?></span>
                    </li>
                   <?php }  }    ?>
                </ul>
            </div>
            <div class="right-list" id="industry_p2_id">
                <?php if (is_array($catStruct)) { foreach ($catStruct as $first_row) { ?>
                <ul class="child-type <?php if ($first_row['gc_id'] == $info['sts_category_p1_id']){echo "type-show";} ?>"  >
                    <?php if (is_array($first_row['sub'])) { foreach ($first_row['sub'] as $row) { ?>
                    <li <?php if ($row['gc_id'] == $info['sts_category_p2_id']){echo 'class="li-check"';} ?>>
                        <span class="parent-type-val" type-id="<?php echo $row['gc_pid']; ?>"   data-value="<?php echo $row['gc_id']; ?>"   ><?php echo $row['gc_name']; ?>(<?php echo $row['remain']; ?>)</span></li>
                    <?php }  }    ?>
                </ul>
                <?php }  }    ?>
            </div>
        </div>
                </div>
    <?php if(!$info) {?>
    <!-- 邀请码 添加时才显示和填写 -->
    <div class="layui-form-item">
        <label class="layui-form-label">邀请码</label>
        <div class="layui-input-inline">
            <input type="text"  name="invitationCode" lay-verify="invitation-code" autocomplete="off" placeholder="请输入邀请码" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux"><img class="prompt" src="<?php echo RESOURCE_ROOT;?>addons/seller/images/prompt.png">如果为好友邀请，请输入邀请码，若无则忽略</div>
    </div>
    <?php } ?>
    <!-- 头像上传 -->
    <div class="layui-form-item" id="crop-avatar">
        <label class="layui-form-label">上传头像</label>
        <div class="layui-input-block">
            <div class="avatar-view">
                <img id="avatar_locate" src="<?php echo isset($info['sts_avatar'])?$info['sts_avatar']:'http://odozak4lg.bkt.clouddn.com/2016101809555805812b2ee04.jpg'?>">
                <input name="sts_avatar"  value="<?php echo $info['sts_avatar']; ?>" id="postadd_uoload_img" class="" type="hidden" />
            </div>
        </div>
        <!-- Cropping modal -->
        <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
            <!-- 头像上传的form表单 -->
              <form class="avatar-form" id="avatar-form"  action="" method="post">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" id="avatar-modal-label">修改头像</h4>
                </div>
                <div class="modal-body">
                  <div class="avatar-body">
                    <!-- Upload image and data -->
                    <div class="avatar-upload">
                      <input type="hidden" class="avatar-src" name="avatar">
                      <input type="hidden" class="avatar-data" name="avatar_data">
                      <a href="javascript:;" class="file_wrap_a">上传头像
                      <input type="file" class="avatar-input" id="avatarInput" name="file">
                      </a>
                      <span style="margin-top: 4px;float: right;margin-right: 258px;color: #999;font-size: 12px;">请上传 jpg、png、gif 格式的图片</span>
                    </div>
                    <!-- Crop and preview -->
                    <div class="row">
                      <div class="col-md-8">
                        <div class="avatar-wrapper"></div>
                      </div>
                      <div class="col-md-2">
                        <div class="avatar-preview preview-md"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit"   onclick="ajaxImg();return false;"  class="btn btn-primary avatar-save">保存</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
              </form>
            </div>
          </div>
        </div><!-- /.modal -->
    </div>
    <!-- 店铺信息 -->
    <div class="layui-form-item">
        <label class="layui-form-label">店铺名</label>
        <div class="layui-input-inline">
            <input type="text" name="sts_name" value="<?php echo $info['sts_name']; ?>" lay-verify="storename" autocomplete="off" placeholder="店铺名一旦提交将无法修改" class="layui-input">
        </div>
        <label class="layui-form-label">实体店</label>
        <div class="layui-input-inline">
            <input type="text" name="sts_physical_shop_name" value="<?php echo $info['sts_physical_shop_name']; ?>" lay-verify="storeName" autocomplete="off" placeholder="实体店铺名称" class="layui-input">
        </div>
        <label class="layui-form-label">联系人</label>
        <div class="layui-input-inline">
            <input type="text" name="sts_contact_name" value="<?php echo $info['sts_contact_name']; ?>" lay-verify="user" autocomplete="off" placeholder="联系人姓名" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">手机号</label>
        <div class="layui-input-inline">
            <input type="text" name="sts_mobile"  value="<?php echo $info['sts_mobile']; ?>" lay-verify="mobile" autocomplete="off" placeholder="联系人的手机号码" class="layui-input">
        </div>
        <label class="layui-form-label">微信号</label>
        <div class="layui-input-inline">
            <input type="text" name="sts_weixin" value="<?php echo $info['sts_weixin']; ?>" lay-verify="weixin" autocomplete="off" placeholder="联系人的微信号" class="layui-input">
        </div>
        <label class="layui-form-label">QQ号</label>
        <div class="layui-input-inline">
            <input type="text" name="sts_qq" value="<?php echo $info['sts_qq']; ?>" lay-verify="qq" autocomplete="off" placeholder="联系人的QQ号" class="layui-input">
        </div>
    </div>
    <!-- 店铺简介 -->
    <div class="layui-form-item">
        <label class="layui-form-label">店铺简介</label>
        <div class="layui-input-block">
            <textarea placeholder="简要得介绍下你的店铺" id="sts_summary" value="<?php echo $info['sts_summary']; ?>" name="sts_summary" class="layui-textarea store-mark"><?php echo $info['sts_summary']; ?></textarea>
        </div>
    </div>
    <!-- 配送范围 -->
    <div class="layui-form-item address-div">
        <label class="layui-form-label">所在地区</label>
        <div class="layui-input-inline">
            <select name="sts_locate_add_1" id="sts_locate_add_1" lay-filter="sts_locate_add_1" id="pingtai_group_id">
                <option value="-1">请选择一级城市</option>
            <?php if (is_array($result)) {
            foreach ($result as $row) {
                ?>
                <?php if ($row['parent_id'] == 0) { ?>
                    <option value="<?php echo $row['region_id']; ?>" data-code="<?php echo $row['region_code']; ?>"  <?php if ($row['region_code'] == $info['sts_locate_add_1']) { ?> selected="selected"<?php } ?>><?php echo $row['region_name']; ?></option>
                <?php } ?>
            <?php }
            }
            ?>
            </select>
        </div>
        <div class="layui-input-inline">
            <select name="sts_locate_add_2" id="sts_locate_add_2"  lay-filter="sts_locate_add_2" id="pingtai_group_id">
                <option value="-1">请选择二级城市</option>
                    <?php if (!empty($info['sts_locate_add_1_id']) && !empty($childrens[$info['sts_locate_add_1_id']])) { ?>
                    <?php if (is_array($childrens[$info['sts_locate_add_1_id']])) {
                        foreach ($childrens[$info['sts_locate_add_1_id']] as $row) { ?>
                            <option  data-code="<?php echo $row['2']; ?>"   value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $info['sts_locate_add_2_id']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
        <?php }} ?>
<?php } ?>
            </select>
        </div>
        <div class="layui-input-inline">
            <select  name="sts_locate_add_3"  id="sts_locate_add_3" lay-filter="sts_locate_add_3" id="pingtai_group_id">
               <option value="-1">请选择三级城市</option>
                  <?php if (!empty($info['sts_locate_add_2_id']) && !empty($childrens[$info['sts_locate_add_2_id']])) { ?>
                    <?php if (is_array($childrens[$info['sts_locate_add_2_id']])) {
                        foreach ($childrens[$info['sts_locate_add_2_id']] as $row) { ?>
                            <option  data-code="<?php echo $row['2']; ?>"   value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $info['sts_locate_add_3_id']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
        <?php }} ?>
<?php } ?>
            </select>
        </div>
    </div>
    <!-- 店铺简介 -->
    <div class="layui-form-item">
        <label class="layui-form-label">详细地址</label>
        <div class="layui-input-block">
            <input type="text" name="sts_address" onblur="SearchDetail()" value="<?php echo $info['sts_address']; ?>" lay-verify="address" autocomplete="off" placeholder="请填写详细地址" class="layui-input store-mark">
        </div>
    </div>
    <!--  -->
    <div class="layui-form-item">
        <input type="hidden" name="id" value="<?php echo $info['sts_id']; ?>">
        <input type="hidden" name="sts_lat" id="point_lat" value="<?php echo $info['sts_lat']; ?>" />
        <input type="hidden" name="sts_lng" id="point_lng" value="<?php echo $info['sts_lng']; ?>" />
            <label class="layui-form-label">地理位置</label>
            <div class="layui-input-block" id="container" style="width: 810px;height: 400px;position:relative"   >
            </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"> </label>
        <div class="layui-input-block">
        <!-- 下一步的链接暂时写死的，请服务端人员更改 -->
            <div >
                <span class="layui-btn" onclick="ajaxsubmitform();return false;" lay-submit=""  lay-filter="demo">下一步</span>
                <span class="layui-btn" data-url="<?php echo mobile_url('index',array('op'=>'dialogMap'))?>" onclick="addedit();return false;" id="gaode_map">高级地图搜索</span>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/cropper.min.js"></script>
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/main.js"></script>

<script>
    
    /***************************************
     * 高德地图         高德地图            高德地图
由于Chrome、IOS10等已不再支持非安全域的浏览器定位请求，为保证定位成功率和精度，请尽快升级您的站点到HTTPS。
***************************************/
var map, geolocation,marker,infoWindow,point,geocoder,isNew;
    isNew = <?php echo $info?0:1; ?>;
$(function(){
    infoWindow = new AMap.InfoWindow({ offset: {x: 0, y: -30} });
    $(window.parent.document).find("#main").height("700px");
    //加载地图，调用浏览器定位服务
    map = new AMap.Map('container', {
        resizeEnable: true
        ,dragEnable:true//拖拽还不太稳定
    });
    map = new AMap.Map('container', {
        resizeEnable: true
        ,dragEnable:true
    });
    
    map.on('dragging', function(data) {
        infoWindow.close();
        marker.setPosition( map.getCenter());
    });
    map.on('dragging', function(data) {
        marker.setPosition( map.getCenter());
    });
    map.on('dragend', function(data) {
        marker.setPosition( map.getCenter());
    });
    map.on('click', function(data) {
        marker.setPosition( data.lnglat );
        var addr =pointToAddress(data.lnglat.getLng(),data.lnglat.getLat());
        $("#point_lng").val( data.lnglat.getLng());
        $("#point_lat").val( data.lnglat.getLat() );
        infoWindow = new AMap.InfoWindow({
           content: addr,
           offset: {x: 0, y: -30}
        });
        infoWindow.open(map, marker.getPosition());
    });
    geocoder =  new AMap.Geocoder({radius: 1000});
    
    if(isNew>0){//新增页面用浏览器定位
        map.plugin('AMap.Geolocation', function() {
            geolocation = new AMap.Geolocation({
                enableHighAccuracy: true,//是否使用高精度定位，默认:true
                timeout: 10000,                         //超过10秒后停止定位，默认：无穷大
                buttonOffset: new AMap.Pixel(10, 20),  //定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                zoomToAccuracy: true      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
            });
            map.addControl(geolocation);
            geolocation.getCurrentPosition();
            AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息
        });
    }else{//修改页面用已知点定位
        point =  [ <?php echo $info['sts_lng']? $info['sts_lng']:0.00; ?>, <?php echo $info['sts_lat']? $info['sts_lat']:0.00; ?>];
        geocoder.getAddress(point, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                console.log(result);
                addMarker(<?php echo $info['sts_lng']? $info['sts_lng']:0.00; ?>,<?php echo $info['sts_lat']? $info['sts_lat']:0.00; ?>) ;
            }
        });  
        marker = new AMap.Marker({  //加点
            map: map,
            position: point
        });
        map.setFitView();
        map.setZoom(map.getZoom()-2);
    }
})

   function SearchDetail(){
        var fulltext =  $("#sts_locate_add_1").find("option:selected").text()+$("#sts_locate_add_2").find("option:selected").text()+$("#sts_locate_add_3").find("option:selected").text()+$("input[name=sts_address]").val();
            console.log(fulltext);
        //地理编码,返回地理编码结果
        geocoder.getLocation(fulltext, function(status, result) {
            console.log(result);
            if (status === 'complete' && result.info === 'OK') {
                geocoder_CallBack(result);
            }
        });
   }

    function addedit(obj){
        var fulltext =  $("#sts_locate_add_1").find("option:selected").text()+$("#sts_locate_add_2").find("option:selected").text()+$("#sts_locate_add_3").find("option:selected").text()+$("input[name=sts_address]").val();

        window.localStorage.setItem("poi_search_text", fulltext);
        layer.open({
        type: 2,
        title: 'POI高级地图搜索',
        shadeClose: true,
        shade: 0.8,
        area: ['90%', '90%'],
        content: '<?php echo mobile_url('index',array('op'=>'dialogMap'))?>' //iframe的url
        ,btn: ['确定']
        ,yes: function(index, layero){
            var   point_lng=    window.localStorage.getItem("dialog_map_lng");
            var   point_lat=    window.localStorage.getItem("dialog_map_lat");
            var   addr= window.localStorage.getItem("dialog_map_addr");
            $("input[name=sts_address]").val(addr);
                addMarker( point_lng,point_lat );
                pointToAddress(point_lng,point_lat);
                $("#point_lng").val( point_lng );
                $("#point_lat").val( point_lat );
                infoWindow = new AMap.InfoWindow({
                   content: addr,
                   offset: {x: 0, y: -30}
                });
                infoWindow.open(map, marker.getPosition());
                map.setFitView();
                map.setZoom(map.getZoom()-2);
                layer.closeAll();
        }
      }); 
    }; 
   
    //解析定位结果
    function onComplete(data) {
        map.setZoom(map.getZoom()-2);
        map.clearMap();//清除浏览器上的定位小点
        marker = new AMap.Marker({
            map: map,
            draggable: true,
            raiseOnDrag: false,
            position: [data.position.getLng(), data.position.getLat()]
        });
        marker.on('dragend', function(data) {
            var addr =pointToAddress(data.lnglat.getLng(),data.lnglat.getLat());
            $("#point_lng").val( data.lnglat.getLng());
            $("#point_lat").val( data.lnglat.getLat() );
            infoWindow = new AMap.InfoWindow({
               content: addr,
               offset: {x: 0, y: -30}
            });
            infoWindow.open(map, marker.getPosition());
        });
//        document.getElementById('tip').innerHTML = str.join('<br>');
    }
    //解析定位错误信息
    function onError(data) {
        //document.getElementById('tip').innerHTML = '定位失败';
    }
    
    function fetchMap() {
        var fulltext =  $("#sts_locate_add_1").find("option:selected").text()+$("#sts_locate_add_2").find("option:selected").text()+$("#sts_locate_add_3").find("option:selected").text();
        //地理编码,返回地理编码结果
        geocoder.getLocation(fulltext, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                geocoder_CallBack(result);
            }
        });
    }
    function addMarker(lng,lat,fulltext) {
        map.clearMap();//清除浏览器上的定位小点
        marker = new AMap.Marker({
            map: map,   draggable: true,
              position: [ lng,  lat]
//            position: [ point.location.getLng(),  point.location.getLat()]
        });
        infoWindow = new AMap.InfoWindow({
            content: fulltext,
            offset: {x: 0, y: -30}
        });
        marker.on("mouseover", function(e) {
            infoWindow.open(map, marker.getPosition());
        });
        marker.on("dragstart", function(e) {
            infoWindow.close();
        });
        marker.on('dragend', function(data) {//重新定位？0
            pointToAddress(data.lnglat.getLng(),data.lnglat.getLat());
            $("#point_lng").val( data.lnglat.getLng());
            $("#point_lat").val( data.lnglat.getLat() );
            infoWindow.open(map, marker.getPosition());
        });
    }
    
    function geocoder_CallBack(data) {
        var geocode = data.geocodes;
        addMarker( geocode[0].location.getLng(),  geocode[0].location.getLat(),geocode[0].formattedAddress);//默认调最匹配的第一个返回地点
        map.setFitView();
        map.setZoom(map.getZoom()-2);
        $("#point_lng").val( geocode[0].location.getLng());
        $("#point_lat").val( geocode[0].location.getLat() );
        //document.getElementById("result").innerHTML = resultStr;
    }
    function pointToAddress(lng,lat) {
        point =  [lng, lat];
        geocoder.getAddress(point, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                var full_addr=result.regeocode.formattedAddress;
                var position=result.regeocode.formattedAddress.indexOf("区")+1;
                var detail_addr= full_addr.substring(position);
                infoWindow.setContent(result.regeocode.formattedAddress );
                $("input[name=sts_address]").val(detail_addr) ;
                return detail_addr;
            }
        });        
        //infoWindow.open(map, marker.getPosition());
        //document.getElementById("result").innerHTML = resultStr;
    }
    
    
    
    
    
    
    </script>
    <script>
    
   function ajaxsubmitform() {
            var fdata=  {
                id: $("input[name=id]").val(),
                sts_shop_type:  $("#sts_shop_type option:selected").val(), 
                
                sts_avatar: $("#postadd_uoload_img").val(),
                sts_name: $("input[name=sts_name]").val(),
                sts_physical_shop_name: $("input[name=sts_physical_shop_name]").val(),  
                sts_lat: $("input[name=sts_lat]").val(),
                sts_lng: $("input[name=sts_lng]").val(),  
                sts_summary: $("#sts_summary").val(),
                sts_qq:  $("input[name=sts_qq]").val(), 
                
                sts_province:  $("#cate_1 option:selected").attr('data-code'), 
                sts_city:  $("#cate_2 option:selected").attr('data-code'), 
                sts_region:  $("#cate_3 option:selected").attr('data-code'),
                
                sts_category_p1_id     : $(".left-list ul li.li-check span").attr('data-value'),
                sts_category_p2_id     :  $(".right-list ul li.li-check span").attr('data-value'),
                
                cate_1:  $("#sts_locate_add_1 option:selected").attr('data-code'), 
                cate_2:  $("#sts_locate_add_2 option:selected").attr('data-code'), 
                cate_3:  $("#sts_locate_add_3 option:selected").attr('data-code'),  
                
                sts_address: $("input[name=sts_address]").val(), 
                sts_contact_name: $("input[name=sts_contact_name]").val(), 
                sts_mobile: $("input[name=sts_mobile]").val(), 
                sts_weixin: $("input[name=sts_weixin]").val(),
                'invitation_code':$("input[name='invitationCode']").val()
            };
//            console.log(fdata);return '';
           layer.load(3);
           if( fdata.sts_category_p2_id == undefined || fdata.sts_category_p2_id == 0){
                layer.alert("请选择主营业务");
               layer.closeAll('loading');
                return false;
           }

           if( !fdata.sts_city || fdata.sts_city == undefined){
                layer.alert("请设置配送区域");
               layer.closeAll('loading');
                return false;
           }
           if( fdata.sts_name == "" ){
                layer.alert("店铺名不能为空");
               layer.closeAll('loading');
                return false;
           }
            $.post('<?php echo mobile_url('store_shop', array('op' => 'shopRegisterStep2','name'=>'seller')) ?>', fdata, function (ret) {
                if (ret.errno == 1) {
                    if(fdata.sts_shop_type == 1){
                        location.href = '<?php echo mobile_url('index', array('op' => 'apply2','nosense' => '1')) ?>&id='+ret.data.id;
                    }else{
                        layer.open({
                            content: '我们已经收到您提交的申请，服务人员将在24小时内处理，请您耐心等待。',
                            yes: function(index, layero){
                                layer.close(index); //如果设定了yes回调，需进行手工关闭
                                location.href = '<?php echo mobile_url('index', array('name' => 'shopwap')) ?>';
                            }
                        });
                    }
                } else {
                    layer.alert(ret.message);
                }
                layer.closeAll('loading');
                return false;
            })

        };
        
layui.use('form',function(){
    var form = layui.form();
    var category = <?php echo json_encode($childrens) ?>;
    //    省市区JS   START ↓
    form.on('select(cate_1)', function(data){
        var cid = data.value;
        var html = '<option value="0">请选择二级分类</option>';
        if (!category || !category[cid]) {
           
        }else{
            for (i in category[cid]) {
            html += '<option value="' + category[cid][i][0] + '" data-code="'+category[cid][i][2]+'">' + category[cid][i][1] + '</option>';
            }
        }
        $("#sts_locate_add_1").find("option[value="+cid+"]").attr("selected",true);
        $('#cate_2').html(html);
        $('#sts_locate_add_2').html(html);
        form.render();
    });
    form.on('select(sts_locate_add_1)', function(data){
        var cid = data.value;
        var html = '<option value="0">请选择二级分类</option>';
        if (!category || !category[cid]) {
           
        }else{
            for (i in category[cid]) {
            html += '<option value="' + category[cid][i][0] + '" data-code="'+category[cid][i][2]+'">' + category[cid][i][1] + '</option>';
            }
        }
        $('#sts_locate_add_2').html(html);
        form.render();
    });
  
    form.on('select(cate_2)', function(data){
        var cid = data.value;
        var html = '<option value="0">请选择三级分类</option>';
        if (!category || !category[cid]) {
          
        }else{
            for (i in category[cid]) {
                html += '<option value="' + category[cid][i][0] + '" data-code="'+category[cid][i][2]+'">' + category[cid][i][1] + '</option>';
            }
        }
        $("#sts_locate_add_2").find("option[value="+cid+"]").attr("selected",true);
        $('#cate_3').html(html);
        $('#sts_locate_add_3').html(html);
        
        form.render();
    });
    form.on('select(sts_locate_add_2)', function(data){
        var cid = data.value;
        var html = '<option value="0">请选择三级分类</option>';
        if (!category || !category[cid]) {
          
        }else{
            for (i in category[cid]) {
                html += '<option value="' + category[cid][i][0] + '" data-code="'+category[cid][i][2]+'">' + category[cid][i][1] + '</option>';
            }
        }
        $('#sts_locate_add_3').html(html);
        form.render();
    });
    
    form.on('select(cate_3)', function(data){
        var region_code = $(data.elem).find("option:checked").attr("data-code");
        var region_id = $('#cate_3').val();
        $("#sts_locate_add_3").find("option[value="+region_id+"]").attr("selected",true);
        var url = '<?php echo mobile_url('store_shop',array('op'=>'appChooseIndustry','name'=>'seller')) ?>';
        $.get(url, {region_code:region_code}, function (ret) {
            if (ret.errno == 1) {
                var data_val = ret.data.shop_cat;
                var category_html = "";
                var sub_html = '';
                $.each(data_val, function (index, ele) {
                    if ( !ele['sub']) {
                    }else{
                        sub_html +='<ul class="child-type">';
                        for (i in ele['sub']) {
                            sub_html  += '<li><span class="parent-type-val" type-id="'+ele['sub'][i]['gc_pid']+'"   data-value="'+ele['sub'][i]['gc_id']+'"   >' + ele['sub'][i]['gc_name'] + '('+ele['sub'][i]['remain']+')</span></li>';
                        }
                        sub_html +='</ul>';
                    }
                });
                $('#industry_p2_id').html(sub_html);
                form.render();
            } else {
                layer.alert(ret.message);
            }
        }, "json");
         fetchMap();//用来匹配下面的地图
    });
    
    form.on('select(sts_locate_add_3)', function(data){
        fetchMap();
    });
    
})
  function ajaxImg() {
  
        $("#avatar-form").ajaxSubmit({
            type: "post",
            url: "<?php echo mobile_url('fileupload') ?>",
            dataType: "json",
            success: function(ret){
                //返回提示信息       
                if(ret.errno==1){
                    $("#postadd_uoload_img").val( ret.data.pic_url );
                    $("#avatar_locate").attr("src", ret.data.pic_url );
                    $('#avatar-modal').modal('hide');
                }else{
                    layer.open({title: '提示',content: data.message});
                }
            }
        });
        return false;
    }
    
$(function(){
    var type_index = 0;
    //一级分类
    $("body").on("click",'.parent-type .parent-type-val',function(){
        //把子类的 默认被选中清除掉
        $(".choosetype-list .child-type").each(function(){
            $(this).find("li").removeClass("li-check");
        });
        type_index = $(this).parent("li").index();
        $(this).parent("li").addClass("li-check").siblings("li").removeClass("li-check");
        if( $(".child-type").eq(type_index).length == 0 ){
            $(".child-type").removeClass("type-show");
        }else{
            $(".child-type").eq(type_index).addClass("type-show").siblings().removeClass("type-show");
        }
    });
    //二级分类
    $("body").on("click",".child-type .parent-type-val",function(){
        $(".child-type li").removeClass("li-check");
        $(this).parent("li").addClass("li-check");
    });

});
</script>
<script>

</script>

<?php  include page('f'); ?>