<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">[<?php echo $info['name']; ?>]区域限制 &nbsp;&nbsp;
        <a href="javascript:;" data-url="<?php echo web_url('region_category', array('op' => 'addRegion', 'id' => $info['id'])); ?>" class="btn btn-primary" onclick="add_attr(this)">新增区域</a>
        <a href="<?php echo web_url('region_category', array('op' => 'display')); ?>" class="btn btn-warning" >返回列表</a>
    </h3>

    <div class="wrap jj">
        <div class="well form-search">
            <div class="table_list">
                <form action="<?php echo web_url('region_category',array('op'=>'updateLimit'))?>" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
                    <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                        <thead id='table_head'>
                            <tr>
                                <th class="text-center" >城市</th>
                                <th width="35%" class="text-center" >限制</th>
                                <!--<th class="text-center" >已有店铺</th>-->
    <!--                            <th class="text-center" >属性可选值</th>-->
                                <!--<th class="text-center" >排序</th>-->
                                <th class="text-center" >操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $list) { ?>
                                <tr>
                                    <td class="text-center" ><?php echo $list['province_name']."-".$list['city_name']."-". $list['region_name']; ?></td>
                                   
                                    <td class="text-center" > <input type="text" style="width:50px" name="displayLimit[<?php echo $list['rc_region_limit']; ?>]" value="<?php echo $list['rc_region_limit']; ?>"></td>
                                    <!--<td class="text-center" ><?php if ($list['attr_input_type'] == 0) {
                                echo "手工录入";
                            } else if ($list['attr_input_type'] == 1) {
                                echo "从列表中选择";
                            } ?></td>-->
           <!--                         <td class="text-center" >--><?php //echo $list['attr_values']; ?><!--</td>-->
                                    <!--<td class="text-center" ><input type="number" data-id="<?php echo $list['id']; ?>" value="<?php echo $list['sort']; ?>" style="width: 80px;text-align: center"></td>-->
                                    <td class="text-center" >
                                        <a  class="btn btn-info"  href="<?php echo web_url($_GP['do'], array('op' => 'delete', 'id' => $list['rc_id'])) ?>" onclick="return confirm('确认这个区域吗？');return false;" >删除</a>
                                    </td>
                                </tr>
                                
                            <?php } ?>
                                <tr>
                                    <td colspan="5">
                                        <input style="float: right" name="submit" type="submit" class="btn btn-primary" value=" 提 交 ">
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <script>
            function add_attr(obj) {
                var url = $(obj).data('url');
                $.ajaxLoad(url, {}, function () {
                    $('#alterModal').modal('show');
                })
            }
        </script>
<?php include page('footer'); ?>