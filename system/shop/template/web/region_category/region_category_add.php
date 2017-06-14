<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue"> 地区分类 </h3>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
                <form action="" method="post" class="form-horizontal" >
                    <input type="hidden" name="id" value="<?php echo $info['rec_id']; ?>" />
                    <input type="hidden" name="rec_pid" value="<?php echo $info['rec_pid']?$info['rec_pid']:$_GP['rec_pid']; ?>" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-left" >所在城市</label>
                        <div class="col-sm-9">
                            <select  id="cate_1" style="margin-right:15px;"  name="cate_1" class="pcates" onchange="fetchChildCategory(this, this.options[this.selectedIndex].value)"  autocomplete="off">
                                <option value="0">请选择一级城市</option>
                                <?php if (is_array($result)) {
                                    foreach ($result as $row) {
                                        ?>
                                        <?php if ($row['parent_id'] == 0) { ?>
                                            <option value="<?php echo $row['region_id']; ?>" <?php if ($row['region_id'] == $extend_arr['p1']) { ?> selected="selected"<?php } ?>><?php echo $row['region_name']; ?></option>
                                        <?php } ?>
                                    <?php }
                                }
                                ?>
                            </select>

                            <select  id="cate_2" name="cate_2" class="cates_2" onchange="fetchChildCategory2(this, this.options[this.selectedIndex].value)" autocomplete="off">
                                <option value="-1">请选择二级城市</option>
                                <?php if (!empty($extend_arr['p2']) && !empty($childrens[$extend_arr['p1']])) { ?>
                                    <?php if (is_array($childrens[$extend_arr['p1']])) {
                                        foreach ($childrens[$extend_arr['p1']] as $row) {
                                            ?>
                                            <option  value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $extend_arr['p2']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
        <?php }
    } ?>
                            <?php } ?>
                            </select>
                            <!--
                            <select  id="cate_3" name="cate_3" autocomplete="off" onchange="fetchMap()">
                                <option value="0">请选择三级城市</option>
                            <?php
                            if (!empty($extend_arr['p3']) && !empty($childrens[$extend_arr['p2']])) {
                                if (is_array($childrens[$extend_arr['p2']])) {
                                    foreach ($childrens[$extend_arr['p2']] as $row) {
                                        ?>
                                                <option value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $extend_arr['p3']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
        <?php }}} ?>
                            </select>
                            -->

                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-left" >分类名称</label>

                        <div class="col-sm-9">

                            <input type="text" name="cate_name" class="col-xs-10 col-sm-2" value="<?php echo $info['rec_name']; ?>" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-left" >商店限制</label>
                        <div class="col-sm-9">
                            <input type="text" name="rec_limit" class="col-xs-10 col-sm-2" value="<?php echo $info['rec_limit']; ?>" />
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-left" >排序</label>
                        <div class="col-sm-9">
                            <input type="text" name="rec_order" class="col-xs-10 col-sm-2" value="<?php echo $info['rec_order']; ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-left" > </label>
                        <div class="col-sm-9">
                            <input name="submit" type="submit" value="提交" class="btn btn-primary span3">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    </script>
<?php include page('footer'); ?>