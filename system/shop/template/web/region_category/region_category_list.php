<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<style>
    .parent_show .second-level{
        width: 50px;
        padding-left: 25px;
    }
    .parent_show .second-level-name{
        width:50px;margin-left: 10px;
    }
    .parent_show .second-level-img{
        width: 60px;
        height: 50px;
        padding: 1px;
        border: 1px solid #ccc;
        float: left;
        margin-right: 10px;
    }
</style>
<h3 class="header smaller lighter blue">分类列表</h3>
<form action="<?php echo web_url('region_category', array('op' => 'batchSetOrder')) ?>" class="form-horizontal" method="post">
    <table class="table table-bordered table-hover">
        <tr>
            <th style="width:100px;">默认限制数</th>
            <th style="width:218px;">分类名称</th>
            <!--<th style="width:60px;">数量限制</th>-->
            <!--<th style="width:218px;">缩略图</th>-->
            <th style="width:350px;">操作</th>
        </tr>
        <tbody>
            <?php
            // icon-resize-full  icon-resize-small
            if (is_array($result)) {
                foreach ($result as $row) {
                    ?>
                    <tr class="first_cat" data-id="<?php echo $row['id']; ?>">
                        <td style="width:100px;">
                            <a href="javascript:void(0)" onclick="hiddens(this,<?php echo $row['id']; ?>)">
                                <i class="icon-resize-full"></i>
                            </a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <!--<input  type="text"  style="width:50px"  name="displayorder[<?php echo $row['id']; ?>]" value="<?php echo $row['limit']; ?>">-->
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo $row['name']; ?></td>
                        <td>
                            <a class="btn btn-xs btn-info"  href="<?php echo web_url('category', array('parentid' => $row['id'], 'op' => 'post')) ?>"><i class="icon-plus-sign-alt"></i> 添加子分类</a>&nbsp;&nbsp;
                            <!--<a class="btn btn-xs btn-info"  href="<?php  echo web_url('region_category', array('op' => 'listCityLimit', 'id' => $row['id']))?>" target="_blank"><i class="icon-eye-open"></i>&nbsp;&nbsp;设置区域限制&nbsp;</a>&nbsp;&nbsp;-->
                            <a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('op' => 'post', 'id' => $row['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
                            <a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
                        </td>
                    </tr>
    <?php }  } ?>
            <tr>
                <td colspan="3">
                    <a  href="<?php echo web_url('category', array('op' => 'post','name' => 'shop')) ?>"><i class="icon-plus-sign-alt"></i> 添加新分类</a>&nbsp;&nbsp;
                </td>
            </tr>
<!--            <tr>
                <td colspan="3">
                    <input name="submit" type="submit" class="btn btn-primary" value=" 提 交 ">
                </td>
            </tr>-->
        </tbody>
    </table>
</form>
<script>
    function updateModelData(id, limit) {
        var url = '<?php echo web_url('region_category',array('op'=>'UpdateLimitSingle')) ?>';
        $.get(url, {id: id,limit:limit}, function (data) {
            if (data.errno == 1) {
                //加个标签
            } else {
                alert(data.message);
            }
        }, "json");
    }
//第二级分类
    function hiddens(thisObj, obj) {
        $('.parent_' + obj).fadeToggle();
        iFrame();
        //var url = "<?php echo web_url('category', array('name' =>  $_GP['name'], 'op' => 'display')); ?>";
        var url = "<?php echo web_url('category',array('name'=>'shop','op'=>'display'));?>";
        if ($('.parent_' + obj).hasClass('parent_show')) {
            return false;
        } else {
            $.get(url, {id: obj}, function (data) {
                var data_val = data.message;
                var category_html = "";
                if (data.errno == 200) {
                    $.each(data_val, function (index, ele) {
                        category_html += '<tr class="parent_' + obj + ' parent_show">'
                                +'<td class="second-level">'+
                                '<input onblur="updateModelData('+ ele.id+',this.value)" type="text" class="second-level-name" name="displayorder[' + ele.id + ']" value=' + ele.limit + '></td>' +
                                '<td>' + ele.name + '</td>' +
                                '<td>' +
                                '<a class="btn btn-xs btn-info" href="/index.php?mod=site&amp;op=post&amp;id=' + ele.id + '&amp;id=' + ele.id + '&amp;name=shop&amp;do=category"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;' +
                                '<a class="btn btn-xs btn-info" href="/index.php?mod=site&amp;op=delete&amp;id=' + ele.id + '&amp;name=shop&amp;do=region_category" onclick="return confirm("确认删除此分类吗？");return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>' +
                                '</td></tr>';
                    });
                    $(thisObj).parents(".first_cat").after(category_html).show();
                } else {
                    alert(data.message);
                }
            }, "json")
        }

    }
    //第三级分类
    function secondHiddens(thisObj, obj) {
        $('.second_' + obj).fadeToggle();
        iFrame();
        var url = "<?php echo web_url($_GP['do'], array('name' => $_GP['name'], 'op' => 'display')); ?>";
        if ($('.second_' + obj).hasClass('parent_show')) {
            return false;
        } else {
            $.post(url, {rec_pid: obj}, function (data) {
                var data_val = data.message;
                var category_html = "";
                if (data.errno == 200) {
                    $.each(data_val, function (index, ele) {
                        category_html += '<tr class="second_' + obj + ' parent_show"><td class="second-level" style="padding-left: 50px;"><input type="text" class="second-level-name" name="displayorder[' + ele.rec_id + ']" value=' + ele.displayorder + '></td>' +
                                '<td>' + ele.rec_name + '</td><td>'+ele.region_name+'</td>' +
                                '<td>' +
                                '<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;rec_pid=' + ele.rec_id + '&amp;op=add&amp;name=shop&amp;do=region_category"><i class="icon-plus-sign-alt"></i> 添加子分类</a> &nbsp;&nbsp;' +
                                '<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;op=edit&amp;id=' + ele.rec_id + '&amp;rec_pid=' + ele.rec_pid + '&amp;name=shop&amp;do=region_category"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;' +
                                '<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;op=delete&amp;id=' + ele.rec_id + '&amp;name=shop&amp;do=region_category" onclick="return confirm("确认删除此分类吗？");return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>' +
                                '</td></tr>';
                    });
                    $(thisObj).parents(".parent_show").after(category_html).show();
                } else {
                    alert(data.message);
                }
            }, "json")
        }

    }
    function iFrame() {
        var ifm = window.parent.document.getElementById("main");
        var subWeb = window.parent.document.frames ? window.parent.document.frames["main"].document : ifm.contentDocument;
        if (ifm != null && subWeb != null) {
            ifm.height = subWeb.body.scrollHeight + 60;
        }
    }
</script>
<?php include page('footer'); ?>
