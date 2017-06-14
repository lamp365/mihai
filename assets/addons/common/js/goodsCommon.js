/**
 * Created by HP on 2017/4/12.
 */

/**
 * 分类筛选的时候，获取下一级别的分类
 * @param obj  当前下拉changge的对象
 * @param num  当前级别
 * 三个分类 分别命名id为 getShopCategory_p1 getShopCategory_p2 getShopCategory_p3
 */
function getShop_sonCategroy(obj,num)
{
    var id = $(obj).val();
    var url ="./index.php?mod=site&name=shop&do=goodscommon&op=getCate";
    if(id == 0){
        var option = "<option value='0'>请选择分类</option>";
        if(num == 1){
            $(obj).parent().parent().find(".get_category").eq(1).html(option);
            $(obj).parent().parent().find(".get_category").eq(2).html(option);
        }else if(num == 2){
            $(obj).parent().parent().find(".get_category").eq(2).html(option);
        }
    }else{
        if(num == 3){
            return '';
        }
        $.post(url,{'id':id},function(data){
            if(data.errno == 200){
                var msg = data.message;
                var html = "<option value='0'>请选择分类</option>";
                for(var i=0;i<msg.length;i++){
                    var opt = msg[i];
                    html = html + "<option value='"+ opt.id +"'>"+ opt.name +"</option>";
                }
                if(num == 1){
                    $(obj).parent().parent().find(".get_category").eq(1).html(html);
                    $(obj).parent().parent().find(".get_category").eq(2).html("<option value='0'>请选择分类</option>");
                }else if(num == 2){
                    $(obj).parent().parent().find(".get_category").eq(2).html(html);
                }
            }else{
                if(num == 1){
                    $(obj).parent().parent().find(".get_category").eq(1).html("<option value='0'>请选择分类</option>");
                    $(obj).parent().parent().find(".get_category").eq(2).html("<option value='0'>请选择分类</option>");
                }if(num == 2){
                    $(obj).parent().parent().find(".get_category").eq(2).html("<option value='0'>请选择分类</option>");
                }
//                        alert('暂无数据');
            }
        },'json');
    }

}

function getNextRegion(obj,num){
    var region_id = $(obj).val();
    var url ="./index.php?mod=site&name=shop&do=goodscommon&op=getNextRegion";
    if(region_id == 0){
        var option1 = "<option value='0'>请选择城市</option>";
        var option2 = "<option value='0'>请选择区域</option>";
        if(num == 1){
            $(obj).parent().parent().find(".get_next_region").eq(1).html(option1);
            $(obj).parent().parent().find(".get_next_region").eq(2).html(option2);
        }else if(num == 2){
            $(obj).parent().parent().find(".get_next_region").eq(2).html(option2);
        }
    }else{
        if(num == 3){
            return '';
        }
        $.post(url,{'region_id':region_id},function(data){
            if(data.errno == 200){
                var msg = data.message;
                if(num == 1) {
                    var html = "<option value='0'>请选择城市</option>";
                }else{
                    var html = "<option value='0'>请选择区域</option>";
                }
                for(var i=0;i<msg.length;i++){
                    var opt = msg[i];
                    html = html + "<option value='"+ opt.region_id +"'>"+ opt.region_name +"</option>";
                }
                if(num == 1){
                    $(obj).parent().parent().find(".get_next_region").eq(1).html(html);
                    $(obj).parent().parent().find(".get_next_region").eq(2).html("<option value='0'>请选择区域</option>");
                }else if(num == 2){
                    $(obj).parent().parent().find(".get_next_region").eq(2).html(html);
                }
            }else{
                if(num == 1){
                    $(obj).parent().parent().find(".get_next_region").eq(1).html("<option value='0'>请选择城市</option>");
                    $(obj).parent().parent().find(".get_next_region").eq(2).html("<option value='0'>请选择区域</option>");
                }if(num == 2){
                    $(obj).parent().parent().find(".get_next_region").eq(2).html("<option value='0'>请选择区域</option>");
                }
//                        alert('暂无数据');
            }
        },'json');
    }
}
/**
 * 根据分类获取品牌以及对应的商品模型
 * @param obj
 * @param num
 * @returns {string}
 */
function getShop_brandOrGtype(obj,num){
    if(num == 1){
        var html_brand = "<option value='0'>请选择品牌</option>";
        var html_gtype = "<option value='0'>请选择商品模型</option>";
        $(".choose_brand").html(html_brand);
        $(".choose_gtype").html(html_gtype);
        return '';
    }
    if(num == 2){
        var p1 =  $(obj).parent().parent().find("select").eq(0).val();
        var p2 =  $(obj).parent().parent().find("select").eq(1).val();
        var p3 =  0;
    }else{
        var p1 =  $(obj).parent().parent().find("select").eq(0).val();
        var p2 =  $(obj).parent().parent().find("select").eq(1).val();
        var p3 =  $(obj).parent().parent().find("select").eq(2).val();
    }
    parame = {'p1':p1,'p2':p2,'p3':p3};
    var url ="./index.php?mod=site&name=shop&do=goodscommon&op=getBrandByCate";
    $.post(url,parame,function(data){
        html_brand = "<option value='0'>请选择品牌</option>";
        html_gtype = "<option value='0'>请选择商品模型</option>";
       if(data.errno != 200){
           $(".choose_brand").html(html_brand);
           $(".choose_gtype").html(html_gtype);
       }else{
            var brandObj = data.message.brand;
            var gtypeObj = data.message.gtype;
            if(brandObj.length > 0){
                for(var i=0;i<brandObj.length;i++){
                    var opt = brandObj[i];
                    html_brand = html_brand + "<option value='"+ opt.id +"'>"+ opt.brand +"</option>";
                }
                $(".choose_brand").html(html_brand);
            }else{
                 html_brand = "<option value='0'>请选择品牌</option>";
                $(".choose_brand").html(html_brand);
            }

           if(gtypeObj.length > 0){
                for(var i=0;i<gtypeObj.length;i++){
                    var opt = gtypeObj[i];
                    html_gtype = html_gtype + "<option value='"+ opt.id +"'>"+ opt.name +"</option>";
                }
               $(".choose_gtype").html(html_gtype);
            }else{
                html_gtype = "<option value='0'>请选择商品模型</option>";
               $(".choose_gtype").html(html_gtype);
           }
       }

    },'json');
}

/**
 * 根据分类 添加没有的 品牌
 * @param obj
 * @param brandname
 * @param icon
 * @param p1
 * @param p2
 * @param p3
 * @returns {boolean}
 */
function addBrandByCategory(obj,brandname,icon,p1,p2,p3){
    var url ="./index.php?mod=site&name=shop&do=goodscommon&op=addbrand";
    if($.trim(brandname) == '' || brandname == null){
        alert('品牌名字不能为空！');
        return false;
    }
    if($.trim(p1) == '' || p1 == null || p1 == 0){
        alert('您未选择分类');
        return false;
    }
    if($.trim(p2) == '' || p2 == null || p2 == 0){
        alert('您未选择分类');
        return false;
    }
    parame  = {
        'brand' : brandname,
        'p1'    : p1,
        'p2'    : p2,
        'p3'    : p3,
        'icon'  : icon
    };

    $.post(url,parame,function(data){
        if(data.errno == 200){
            var id = data.message;
            var html = "<option value='"+id+"'>"+brandname+"</option>";
            $(obj).append(html);
            $(obj).find("option:last").prop('selected',true);
        }else{
            alert(data.message);
        }
    },'json');

}

/**
 * 根据商品模型 获取对应的 属性以及规格
 * @param gtype_id
 * @param goods_id
 */
function getGoodsAttrAndSpec(gtype_id,goods_id){
    var parame   ={
        'gtype_id' : gtype_id,
        'goods_id' : goods_id
    };
    var url ="./index.php?mod=site&name=shop&do=goodscommon&op=goodget_attr";
    $.post(url,parame,function(data){
        if(data.errno == 200){
            var result = data.message;
            $("#goods_attr_table tr:gt(0)").remove();
            $("#goods_attr_table").append(result);
        }else{
            $("#goods_attr_table tr:gt(0)").remove();
        }
    },'json');

    var url2 ="./index.php?mod=site&name=shop&do=goodscommon&op=goodget_spec";
    $.post(url2,parame,function(data){
        if(data.errno == 200){
            var result = data.message;
            $("#goods_spec_table1 tr:gt(0)").remove();
            $("#goods_spec_table1").append(result);
            $("#goods_spec_table2").html('');
            //触发不同的输入框选项
            getGoodsSpecInputInfo(goods_id);
        }else{
            $("#goods_spec_table1 tr:gt(0)").remove();
            $("#goods_spec_table2").html('');
        }
    },'json');
}

/**
 * 根据用户选择的不同规格选项
 * 返回 不同的输入框选项
 * @param goods_id
 */
function getGoodsSpecInputInfo(goods_id){
    var spec_arr = {};// 用户选择的规格数组
    // 选中了哪些属性
    $("#goods_spec_table1  button").each(function(){
        if($(this).hasClass('btn-success'))
        {
            var spec_id = $(this).data('spec_id');
            var item_id = $(this).data('item_id');
            if(!spec_arr.hasOwnProperty(spec_id))
                spec_arr[spec_id] = [];

            spec_arr[spec_id].push(item_id);
            //console.log(spec_arr);
        }
    });

    var parame  = {
        'goods_id' : goods_id,
        'spec_arr' : spec_arr
    };
    var url ="./index.php?mod=site&name=shop&do=goodscommon&op=goodspect_input";
    $.post(url,parame,function(data){
        if(data.errno == 200){
            var result = data.message;
            $("#goods_spec_table2").html(result);
        }else{
            $("#goods_spec_table2").html('');
        }
    },'json');
}