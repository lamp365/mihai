<div class="alertModal-dialog-bg" style="width:52%">
    <!--关闭按钮-->
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;margin-top: 10px;">&times;</button>
    <h3 class="header smaller lighter blue">[<?php echo $p_info['region_name']."-".$info['region_name']; ?>]区域限制 &nbsp;&nbsp;
    </h3>
        <div class="well form-search">
            <div class="table_list">
                <form  class="form-horizontal" id="mydialogform" method="post" onsubmit="return ajaxForm()">
                    <input type="hidden" name="region_code" value="<?php echo $_GP['region_code'];?>"/>
                    <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                        <thead id='table_head'>
                            <tr>
                                <th class="text-center" >行业名</th>
                                <th width="20%" class="text-center" >行业默认限制</th>
                                <th width="20%" class="text-center" >已有店铺数</th>
                                <th width="15%" class="text-center" >地区特殊限制</th>
    <!--                            <th class="text-center" >属性可选值</th>-->
                                <!--<th class="text-center" >排序</th>-->
                                <th  width="5%"  class="text-center" >               
                                    <a  class="btn btn-info"  href="#" onclick="return ajaxForm();return false;" >提交</a>
                                </th>    
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($result)) { ?>
                                <?php foreach ($result as $list) { ?>
                                    <tr>
                                        <td class="text-center" ><?php echo $ParentIDVALUE[$list['gc_pid']]."-----".$list['gc_name']; ?></td>
                                        <td class="text-center" > <?php echo $list['gc_limit']; ?></td>
                                        <td class="text-center" > <?php echo $list['cat_num']; ?></td>
                                        <td class="text-center" ><input onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" style="width:50px" name="displayLimit[<?php echo $list['gc_id']; ?>]" value="<?php echo $list['rc_region_limit']; ?>"></td>
                                        <td class="text-center" ></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
</div>
    
    
<script>
    function ajaxForm(code) {
        //var url = $(obj).data('url');
        var data= $('#mydialogform').serialize();
        var url= '<?php echo web_url('region',array('op'=>'batchSetLimit')) ?>';
        $.post(url,data,function(data){
            if(data.errno==1){
                alert(data.message);
                $('#alterModal').modal('hide');
            }else{
                alert(data.message);
            }
            
        },'json')
        
    };
    
</script>