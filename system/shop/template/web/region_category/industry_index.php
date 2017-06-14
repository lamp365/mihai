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
<h3 class="header smaller lighter blue">行业列表</h3>
<form action="" class="form-horizontal" method="post">
    <table class="table table-bordered table-hover">
        <tr>
            <th style="width:100px;">排序</th>
            <th style="width:218px;">名称</th>
            <th style="width:60px;">数量限制</th>
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
                            <input  onblur="updateModelData(<?php echo $row['id']; ?>,this.value)" type="number"  style="width:50px"   value="<?php echo $row['gc_order']; ?>">
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo $row['name']; ?></td>
                        <td>                            
                            <!--<input  onblur="updateLimitData(<?php echo $row['id']; ?>,this.value)" type="number"  style="width:50px"   value="<?php echo $row['gc_limit']; ?>">-->
                        </td>
                        <td>
                            <a class="btn btn-xs btn-info"  href="<?php echo web_url($_GP['do'], array('pid' => $row['id'], 'op' => 'add')) ?>"><i class="icon-plus-sign-alt"></i> 添加子分类</a>&nbsp;&nbsp;
                            <!--<a class="btn btn-xs btn-info"  href="<?php  echo web_url($_GP['do'], array('op' => 'listCityLimit', 'id' => $row['id']))?>" target="_blank"><i class="icon-eye-open"></i>&nbsp;&nbsp;设置区域限制&nbsp;</a>&nbsp;&nbsp;-->
                            <a class="btn btn-xs btn-info"  href="<?php  echo web_url($_GP['do'], array('op' => 'edit', 'id' => $row['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
                            <a class="btn btn-xs btn-info"  href="<?php  echo web_url($_GP['do'], array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？(如果有对应的二级分类也删除！)');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
                        </td>
                    </tr>
    <?php }  } ?>
            <tr>
                <td colspan="4">
                    <a  href="<?php echo web_url($_GP['do'], array('op' => 'add')) ?>"><i class="icon-plus-sign-alt"></i> 添加新分类</a>&nbsp;&nbsp;
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
    function updateModelData(id, num) {
        var url = '<?php echo web_url($_GP['do'],array('op'=>'UpdateLimitSingle')) ?>';
        $.get(url, {id: id,gc_order:num}, function (data) {
            if (data.errno == 1) {
                //需要前端加个加个成功显示的标签
            } else {
                alert(data.message);
            }
        }, "json");
    }
    function updateLimitData(id, num) {
        var url = '<?php echo web_url($_GP['do'],array('op'=>'UpdateLimitSingle')) ?>';
        $.get(url, {id: id,gc_limit:num}, function (data) {
            if (data.errno == 1) {
                 //需要前端加个加个成功显示的标签
            } else {
                alert(data.message);
            }
        }, "json");
    }
    
//第二级分类
    function hiddens(thisObj, obj) {
        $('.parent_' + obj).fadeToggle();
        iFrame();
        var url = "<?php echo web_url($_GP['do'],array('name'=>'shop','op'=>'index'));?>";
        if ($('.parent_' + obj).hasClass('parent_show')) {
            return false;
        } else {
            $.get(url, {pid: obj}, function (data) {
                var data_val = data.message;
                var category_html = "";
                if (data.errno == 1) {
                    $.each(data_val, function (index, ele) {
                        category_html += '<tr class="parent_' + obj + ' parent_show">'
                                +'<td class="second-level">'+
                                '<input onblur="updateModelData('+ ele.id+',this.value)" type="number" class="second-level-name" name="displayorder[' + ele.id + ']" value=' + ele.gc_order + '></td>' +
                                '<td>' + ele.name + '</td>' +
                                '<td><input onblur="updateLimitData('+ ele.id+',this.value)" type="number" class="second-level-name"  value=' + ele.gc_limit + '></td></td>' +
                                '<td>' +
                                '<a class="btn btn-xs btn-info" href="<?php  echo web_url($_GP['do'], array('op' => 'edit'))?>&amp;id=' + ele.id + '"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;' +
                                '<a class="btn btn-xs btn-info" href="<?php  echo web_url($_GP['do'], array('op' => 'delete'))?>&amp;id=' + ele.id + '" onclick="return confirm("确认删除此分类吗？");return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>' +
                                '</td></tr>';
                    });
                    $(thisObj).parents(".first_cat").after(category_html).show();
                } else {
                    alert(data.message);
                }
            }, "json")
        }

    }
    
    function iFrame() {
//        var ifm = window.parent.document.getElementById("main");
//        var subWeb = window.parent.document.frames ? window.parent.document.frames["main"].document : ifm.contentDocument;
//        if (ifm != null && subWeb != null) {
//            ifm.height = subWeb.body.scrollHeight + 60;
//        }
    }
</script>
<?php include page('footer'); ?>
