<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<h3 class="header smaller lighter blue">区域管理</h3>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT; ?>/addons/common/css/datetimepicker.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT; ?>/addons/common/laydate/laydate.js"></script>

<!--加載高德地圖START -->
<link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=202a9a4762b30ac29f750d7aa8c179e1&plugin=AMap.CitySearch"></script>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=8ecaa654c1cb4291647a565c375cc5ae&plugin=AMap.Geocoder"></script>
<script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
<!--加載高德地圖END -->

<form action="" method="post" class="form-horizontal" >
    <input type="hidden" name="id" value="<?php echo $info['reg_cst_id']; ?>" />
    <input type="hidden" name="point_lat" id="point_lat" value="<?php echo $info['point_lat']; ?>" />
    <input type="hidden" name="point_lng" id="point_lng" value="<?php echo $info['point_lng']; ?>" />
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >所在城市</label>
        <div class="col-sm-9">
            <select  id="cate_1" style="margin-right:15px;"  name="cate_1" class="pcates" onchange="fetchChildCategory(this, this.options[this.selectedIndex].value)"  autocomplete="off">
                <option value="0">请选择一级城市</option>
                <?php if (is_array($result)) {
                    foreach ($result as $row) { ?>
                        <?php if ($row['parent_id'] == 0) { ?>
                            <option value="<?php echo $row['region_id']; ?>" <?php if ($row['region_id'] == $extend_arr['p1']) { ?> selected="selected"<?php } ?>><?php echo $row['region_name']; ?></option>
                        <?php } ?>
    <?php }
} ?>
            </select>

            <select  id="cate_2" name="cate_2" class="cates_2" onchange="fetchChildCategory2(this, this.options[this.selectedIndex].value)" autocomplete="off">
                <option value="-1">请选择二级城市</option>
        <?php if (!empty($extend_arr['p2']) && !empty($childrens[$extend_arr['p1']])) { ?>
                    <?php if (is_array($childrens[$extend_arr['p1']])) {
                        foreach ($childrens[$extend_arr['p1']] as $row) { ?>
                            <option  value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $extend_arr['p2']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
        <?php }} ?>
<?php } ?>
            </select>
            
            <select  id="cate_3" name="cate_3" autocomplete="off" onchange="fetchMap()">
                <option value="0">请选择三级城市</option>
                <?php 
				    if(!empty($extend_arr['p3']) && !empty($childrens[$extend_arr['p2']])) { 
				       if(is_array($childrens[$extend_arr['p2']])) { 
						   foreach($childrens[$extend_arr['p2']] as $row) { 
				?>
                    <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $extend_arr['p3']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
                <?php  } } } ?>
            </select>

        </div>

    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >区域名称</label>

        <div class="col-sm-9">

            <input type="text" name="reg_name" class="col-xs-10 col-sm-2" value="<?php echo $info['reg_name']; ?>" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" >地图</label>

        <div   class="col-sm-9" id="container" style="width: 800px;height: 400px;position:relative"   > 
        </div>
    </div>



    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-left" > </label>

        <div class="col-sm-9">

            <input name="submit" type="submit" value="提交" class="btn btn-primary span3">
        </div>
    </div>
</form>
<script language="javascript">
    var category = <?php echo json_encode($childrens) ?>;
    
    function fetchChildCategory(o_obj, cid) {
        var html = '<option value="0">请选择二级分类</option>';

        var obj = $(o_obj).parent().find('.cates_2').get(0);
        if (!category || !category[cid]) {
            $(o_obj).parent().find('.cates_2').html(html);

            fetchChildCategory2(o_obj, obj.options[obj.selectedIndex].value);
            return false;
        }
        for (i in category[cid]) {
            html += '<option value="' + category[cid][i][0] + '">' + category[cid][i][1] + '</option>';
        }
        $(o_obj).parent().find('.cates_2').html(html);
        fetchChildCategory2(o_obj, obj.options[obj.selectedIndex].value);

    }
    function fetchChildCategory2(o_obj, cid) {
        var html = '<option value="0">请选择三级分类</option>';
        if (!category || !category[cid]) {
            $(o_obj).parent().find('.cate_3').html(html);
            return false;
        }
        for (i in category[cid]) {
            html += '<option value="' + category[cid][i][0] + '">' + category[cid][i][1] + '</option>';
        }
        $('#cate_3').html(html);
    }
    
   
    /***************************************
     * 高德地图         高德地图            高德地图
由于Chrome、IOS10等已不再支持非安全域的浏览器定位请求，为保证定位成功率和精度，请尽快升级您的站点到HTTPS。
***************************************/
var map, geolocation,marker,infoWindow,point,geocoder,isNew;
    isNew = <?php echo $isNeedLocate?>;
$(function(){
     
    $(window.parent.document).find("#main").height("700px");
    //加载地图，调用浏览器定位服务
    map = new AMap.Map('container', {resizeEnable: true});
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
    //        AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息
        });
    }else{//修改页面用已知点定位
        point =  [ <?php echo $info['point_lng']; ?>, <?php echo $info['point_lat']; ?>];
        geocoder.getAddress(point, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                //geocoder_CallBack(result);
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
            $("#point_lat").val(data.lnglat.getLat());
            $("#point_lng").val(data.lnglat.getLng());
        });
//        document.getElementById('tip').innerHTML = str.join('<br>');
    }
    //解析定位错误信息
    function onError(data) {
        //document.getElementById('tip').innerHTML = '定位失败';
    }
    
    function fetchMap() {
        var fulltext =  $("#cate_1").find("option:selected").text()+$("#cate_2").find("option:selected").text()+$("#cate_3").find("option:selected").text();
        //地理编码,返回地理编码结果
        geocoder.getLocation(fulltext, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                geocoder_CallBack(result);
            }
        });
    }
    function addMarker(point) {
        map.clearMap();//清除浏览器上的定位小点
        marker = new AMap.Marker({
            map: map,   draggable: true,
            position: [ point.location.getLng(),  point.location.getLat()]
        });
        infoWindow = new AMap.InfoWindow({
            content: point.formattedAddress,
            offset: {x: 0, y: -30}
        });
        marker.on("mouseover", function(e) {
            infoWindow.open(map, marker.getPosition());
        });
        marker.on("dragstart", function(e) {
            infoWindow.close();
        });
        marker.on('dragend', function(data) {//重新定位？0
            pointToAddress(data);
            $("#point_lng").val( data.lnglat.getLng());
            $("#point_lat").val( data.lnglat.getLat() );
            infoWindow.open(map, marker.getPosition());
        });
    }
    
    function geocoder_CallBack(data) {
        var geocode = data.geocodes;
        addMarker( geocode[0]);//默认调最匹配的第一个返回地点
        map.setFitView();
        $("#point_lng").val( geocode[0].location.getLng());
        $("#point_lat").val( geocode[0].location.getLat() );
        //document.getElementById("result").innerHTML = resultStr;
    }
    function pointToAddress(data) {
        point =  [data.lnglat.getLng(), data.lnglat.getLat()];
        geocoder.getAddress(point, function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                infoWindow.setContent(result.regeocode.formattedAddress ); 
            }
        });        
        //infoWindow.open(map, marker.getPosition());
        //document.getElementById("result").innerHTML = resultStr;
    }
    
    
    
    
    
</script>
<?php include page('footer'); ?>
