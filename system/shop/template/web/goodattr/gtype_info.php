
        <div class="alertModal-dialog-sm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">查看<?php if($_GP['type'] == 'attr'){ echo "属性";}else{ echo '规格'; } ?></h4>
                </div>
                <div class="modal-body">
                    <?php if($_GP['type'] == 'attr') { ?>
                    <div class="">
                        <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                            <thead id='table_head'>
                            <tr>
                                <th width="35%" class="text-center" >属性名称</th>
                                <th class="text-center" >属性值输入方式</th>
<!--                                <th class="text-center" >属性可选值</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($attr_list as $list){ ?>
                                <tr>
                                    <td class="text-center" ><?php echo $list['attr_name'];?></td>
                                    <td class="text-center" ><?php if($list['attr_input_type'] == 0){ echo "手工录入"; }else if($list['attr_input_type'] == 1){ echo "从列表中选择";}?></td>
<!--                                    <td class="text-center" >--><?php //echo $list['attr_values'];?><!--</td>-->
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php }else{ ?>

                    <div>
                        <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                            <thead id='table_head'>
                            <tr>
                                <th width="35%" class="text-center" >规格名称</th>
                                <th class="text-center" >规格项</th>
                                <th class="text-center" >已下架</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($spec_list as $list){ ?>
                                <tr>
                                    <td class="text-center" ><?php echo $list['spec_name'];?></td>
                                    <td class="text-center" >
                                        <?php
                                        $str1 = '';
                                        $str2 = '';
                                        foreach($list['spec_item'] as $spec_row){
                                            if($spec_row['status'] == 1){
                                                $str1 .= $spec_row['item_name'].',';
                                            }else{
                                                $str2 .= $spec_row['item_name'].',';
                                            }

                                        }
                                        echo rtrim($str1,',');
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo rtrim($str2,',');?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>

        </div>

