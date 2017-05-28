<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">

    <div class="wrap jj">
        <div class="well form-search">
            
            <div class="table_list">
                <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                    <thead id='table_head'>
                        <tr>
                            <th class="text-center" >id</th>
                            <th width="35%" class="text-center" >属性名称</th>
                            <th class="text-center" >属性值输入方式</th>
<!--                            <th class="text-center" >属性可选值</th>-->
                            <th class="text-center" >排序</th>
                            <th class="text-center" >操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($attrlist as $list){ ?>
                     <tr>
                         <td class="text-center" ><?php echo $list['attr_id'];?></td>
                         <td class="text-center" ><?php echo $list['attr_name'];?></td>
                         <td class="text-center" ><?php if($list['attr_input_type'] == 0){ echo "手工录入"; }else if($list['attr_input_type'] == 1){ echo "从列表中选择";}?></td>
<!--                         <td class="text-center" >--><?php //echo $list['attr_values'];?><!--</td>-->
                         <td class="text-center" ><input type="number" data-id="<?php echo $list['id'];?>" value="<?php echo $list['sort'];?>" style="width: 80px;text-align: center"></td>
                        <td class="text-center" >
                            <a href="javascript:;" data-url="<?php echo web_url('goodstype',array('op'=>'add_attr','gtype_id'=>$gtype['id'],'id'=>$list['attr_id'])); ?>"  class="btn btn-info" onclick="add_attr(this)" >编辑属性</a>
                        </td>
                     </tr>
                     <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            function add_attr(obj){
                var url = $(obj).data('url');
                $.ajaxLoad(url,{},function(){
                    $('#alterModal').modal('show');
                })
            }
        </script>
<?php  include page('footer');?>