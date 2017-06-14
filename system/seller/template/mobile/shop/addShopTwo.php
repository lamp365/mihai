<?php defined('SYSTEM_IN') or exit('Access Denied'); ?>
<?php include page('seller_header'); ?>
<link rel="stylesheet" href="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/css/cropper.min.css" media="all" />
<link rel="stylesheet" href="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/css/main.css" media="all" />
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/cropper.min.js"></script>
<!--不做页面提交，用ajaxsubmit提交和控制回调-->
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/main_no_sumbit.js"></script> 
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/script/jquery.form.js"></script>
<!--加載高德地圖START -->
<link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=202a9a4762b30ac29f750d7aa8c179e1&plugin=AMap.CitySearch"></script>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=8ecaa654c1cb4291647a565c375cc5ae&plugin=AMap.Geocoder"></script>
<script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
<!--加載高德地圖END -->
<style>
    .ncap-form-default {
        padding: 10px 0;
        overflow: hidden;
    }
    .ncap-form-default dl.row, .ncap-form-all dd.opt {
        color: #777;
        background-color: #FFF;
        padding: 12px 0;
        margin-top: -1px;
        border-style: solid;
        border-width: 1px 0;
        border-color: #F0F0F0;
        position: relative;
        z-index: 1;
    }
    .table-bordered {
        width: 100%;
    }
    table {
        border-collapse: collapse;
    }
    .row .table-bordered td {
        padding: 8px;
        line-height: 1.42857143;
    }
    .table-bordered tr td {
        border: 1px solid #f4f4f4;
    }
    .row{
        margin:0;
        padding:0;
    }
    .layui-form-label{
        width: 110px;
    }
    #layui-layer1.layui-layer1{

        margin-top:-300px!important;
    }
    .left-list,.right-list{
        float: left;
        width: 50%;
        width: 300px;
        height: 200px;
        overflow: auto;
        border: 1px solid #ddd;
        padding: 10px;
        box-sizing:border-box;
    }
    .right-list{
        margin-left: 10px;
    }
    .right-list li{
        cursor: pointer;
        display: none;
        line-height: 25px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 3px 5px;
        box-sizing:border-box;
        border: 1px solid #fff;
    }
    .left-list li{
        cursor: pointer;
        line-height: 25px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 3px 5px;
        box-sizing:border-box;
        border: 1px solid #fff;
    }
    .left-list li.left-list-li-check,.right-list li.left-list-li-check{
        background-color: #d9edf7;
        color: #4e90b5;
        border: 1px solid #bee9f1;
    }
</style>
<body style="padding:10px;" class="step2">
    <blockquote class="layui-elem-quote">第2步-店铺信息<span class="child-stop-info"></span></blockquote>
    <!--<form class="layui-form" id="formtag" action="">-->
        <input type="hidden" name="id"  value="<?php echo $_GP['id']; ?>" />
        <input type="hidden" name="sts_lat" id="point_lat" value="<?php echo $info['point_lat']; ?>" />
        <input type="hidden" name="sts_lng" id="point_lng" value="<?php echo $info['point_lng']; ?>" />

        <!-- 分隔符 -->
                <!-- 商品主图 -->
                <div id="crop-avatar">
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label"><font color="red">*</font> 店铺头像</label>
                        <div class="layui-input-block" >
                            <div class="avatar-view">
                                <img id="avatar_locate"  style="width:120px;height:120px;border-radius:120px" src="http://odozak4lg.bkt.clouddn.com/2016101809555805812b2ee04.jpg">
                                <input name="sts_avatar" id="postadd_uoload_img" class="" type="hidden" />
                            <!--postadd_uoload_img-->
                            </div>
                        </div>
                    </div>
                    <!-- Cropping modal -->
                    <div class="modal fade" id="avatar-modal" aria-hidden="trueatar-moda" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                            <form class="avatar-form" onsubmit="ajaxImg();return false;"  id="avatar-form"  action="<?php echo mobile_url('fileupload') ?>" method="post">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title" id="avatar-modal-label">修改头像</h4>
                            </div>
                            <div class="modal-body">
                              <div class="avatar-body">
                                <!-- Upload image and data -->
                                <div class="avatar-upload">
                                  <input type="hidden" class="avatar-src" name="avatar">
                                  <input type="hidden" class="avatar-src" name="upload_name" value="thumb">
                                  <input type="hidden" class="avatar-data" name="avatar_data">
                                  <a href="javascript:;" class="file_wrap_a">上传头像
                                  <input type="file" class="avatar-input" id="avatarInput" name="thumb">
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
                              <button type="submit"  class="btn btn-primary avatar-save">保存</button>
                              <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div><!-- /.modal -->
                </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label"><font color="red">*</font> 店铺名</label>
            <div class="layui-input-inline">
                <input type="text" name="sts_name" id="sts_name" lay-verify="title" autocomplete="off" placeholder="店铺名" class="layui-input">
            </div>
        </div>
        <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">实体店</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input"  name="sts_physical_shop_name" id="sts_physical_shop_name" placeholder="实体店" >
            </div>
        </div>
        <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">联系人</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" id="sts_contact_name"  name="sts_contact_name" placeholder="联系人" >
            </div>
        </div><!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">手机号</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" id="sts_mobile"  name="sts_mobile" placeholder="手机号" >
            </div>
        </div><!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">微信号</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input"  name="sts_weixin" id="sts_weixin" placeholder="微信号" >
            </div>
        </div>
        <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">QQ</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" id="sts_qq" name="sts_qq" placeholder="QQ" >
            </div>
        </div>
        <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">店铺简介</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input"  name="sts_summary" id="sts_summary" placeholder="店铺简介" >
            </div>
        </div>
        <!-- 分隔符 -->
        <div class="layui-form-item layui-form">
            <label class="layui-form-label">所在地区</label>
            <div class="layui-input-inline">
                <select  id="cate_1" lay-filter="cate_1" style="margin-right:15px;"  name="cate_1" class="pcates"   autocomplete="off">
                    <option value="0">请选择一级城市</option>
                    <?php
                    if (is_array($result)) { foreach ($result as $row) {    ?>
                            <?php if ($row['parent_id'] == 0) { ?>
                                <option value="<?php echo $row['region_id']; ?>" data-code="<?php echo $row['region_code']; ?>" ><?php echo $row['region_name']; ?></option>
                            <?php } ?>
                        <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="layui-input-inline">
                <select  id="cate_2"  lay-filter="cate_2"    name="cate_2" class="cates_2"  autocomplete="off">
                    <option value="-1">请选择二级城市</option>
                </select>
            </div>
            <div class="layui-input-inline">
                <select  id="cate_3" name="cate_3" lay-filter="cate_3"  autocomplete="off" onchange="fetchMap()">
                    <option value="0">请选择三级城市</option>
                </select>
            </div>
        </div>
         <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">详细地址</label>
            <div class="layui-input-inline">
                <input class="layui-input" id="sts_address" onblur="SearchDetail()" name="sts_address" placeholder="详细地址" > </input>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">地理位置</label>
            <div class="layui-input-inline" id="container" style="width: 800px;height: 400px;position:relative"   >
            </div>
        </div>
       
     
        <div class="layui-form-item">
            <div class="layui-input-block">
                <span class="layui-btn next-step" lay-submit="" lay-filter="demo">下一步</span>
                <span class="layui-btn" data-url="<?php echo mobile_url('store_shop',array('op'=>'dialogMap'))?>" onclick="addedit();return false;" id="gaode_map">高级地图搜索</span>
            </div>
        </div>
    <!--</form>-->
<?php include page('seller_footer'); ?>
</body>

<script type="text/javascript">
    var category = <?php echo json_encode($childrens) ?>;//    省市区JS
   
    //解析定位结果
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
    }

    $(function () {
        $(".left-list li").on("click", function () {
            var list_value = $(this).attr("list-value");
            $(".right-list li").hide().removeClass("left-list-li-check");
            $(this).addClass("left-list-li-check").siblings("li").removeClass("left-list-li-check");
            $(".right-list li[list-value=" + list_value + "]").show();
        });
        $("body").on("click", ".right-list li", function () {
            $(this).addClass("left-list-li-check").siblings("li").removeClass("left-list-li-check");
        });
    })
    
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
        //,dragEnable:true//拖拽还不太稳定
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
//                console.log(result);
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
        //地理编码,返回地理编码结果
        geocoder.getLocation(fulltext, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                geocoder_CallBack(result);
            }
        });
   }

    function addedit(obj){
        var fulltext =  $("#cate_1").find("option:selected").text()+$("#cate_2").find("option:selected").text()+$("#cate_3").find("option:selected").text()+$("input[name=sts_address]").val();
        window.localStorage.setItem("poi_search_text", fulltext);
        layer.open({
        type: 2,
        title: 'POI高级地图搜索',
        shadeClose: true,
        shade: 0.8,
        area: ['90%', '90%'],
        content: '<?php echo mobile_url('store_shop',array('op'=>'dialogMap'))?>' //iframe的url
        ,btn: ['确定']
        ,yes: function(index, layero){
            var   point_lng=    window.localStorage.getItem("dialog_map_lng");
            var   point_lat=    window.localStorage.getItem("dialog_map_lat");
            var   addr= window.localStorage.getItem("dialog_map_addr");
                $("input[name=sts_address]").val(addr);
                addMarker( point_lng,point_lat );
//                pointToAddress(point_lng,point_lat);
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
        var fulltext =  $("#cate_1").find("option:selected").text()+$("#cate_2").find("option:selected").text()+$("#cate_3").find("option:selected").text()+$("#sts_address").val();
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
    
     layui.use(['form', 'element', 'layer'], function () {
        var form = layui.form();
        var element = layui.element();
        var $ = layui.jquery,
                layer = layui.layer;
        //监听提交
        form.on('submit(demo)', function (data) {
            var fdata=  {
                sts_avatar: $("#sts_avatar").val(),
                id: $("input[name=id]").val(),
                sts_name: $("#sts_name").val(),
                sts_physical_shop_name: $("#sts_physical_shop_name").val(),  
                sts_lat: $("input[name=sts_lat]").val(),
                sts_summary: $("#sts_summary").val(),
                sts_qq: $("#sts_qq").val(), 
                sts_lng: $("input[name=sts_lng]").val(),  
                cate_1:  $("#cate_1 option:selected").attr('data-code'), 
                cate_2:  $("#cate_2 option:selected").attr('data-code'), 
                cate_3:  $("#cate_3 option:selected").attr('data-code'),  
                sts_address: $("#sts_address").val(), 
                sts_contact_name: $("#sts_contact_name").val(), 
                sts_mobile: $("#sts_mobile").val(), 
                sts_weixin: $("#sts_weixin").val()
            };
           
            $.post('<?php echo mobile_url('store_shop', array('op' => 'shopRegisterStep2')) ?>', fdata, function (ret) {
                if (ret.errno == 1) {
                    location.href = '<?php echo mobile_url('store_shop', array('op' => 'addShopThree')) ?>?id='+ret.data.id;
                } else {
                    layer.alert(ret.message);
                }
                return false;
            })

        });
//    省市区JS   START ↓
        form.on('select(cate_1)', function (data) {
            var cid = data.value;
            var html = '<option value="0">请选择二级</option>';
            if (!category || !category[cid]) {

            } else {
                for (i in category[cid]) {
                    html += '<option value="' + category[cid][i][0] + '"   data-code="'+category[cid][i][2]+'"  >' + category[cid][i][1] + '</option>';
                }
            }
            $('#cate_2').html(html);
            form.render();
        });
        form.on('select(cate_2)', function (data) {
            var cid = data.value;
            var html = '<option value="0">请选择三级</option>';
            if (!category || !category[cid]) {

            } else {
                for (i in category[cid]) {
                    html += '<option value="' + category[cid][i][0] + '"   data-code="'+category[cid][i][2]+'"  >' + category[cid][i][1] + '</option>';
                }
            }
            $('#cate_3').html(html);
            form.render();
        });
        //    省市区JS   END ↑

        //    省市区JS   START ↓
        form.on('select(cate_3)', function (data) {
            fetchMap();
        });
    });
        
   
    </script>

</html>