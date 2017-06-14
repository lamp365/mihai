<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <br/>
    <ul class="nav nav-tabs" >
        <li style="width:7%" <?php  if(empty($_GP['op'])) { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('goodstype')?>">分组管理</a></li>
        <li style="width:7%" <?php  if($_GP['op'] == 'lists') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('goodstype',  array('op' => 'lists'))?>">模型管理</a></li>
    </ul>
    <h3 class="header smaller lighter blue">商品模型列表 &nbsp;&nbsp; <a data-url="<?php echo web_url('goodstype',array('op'=>'add_gtype')); ?>" class="btn btn-primary" href="javascript:;" onclick="add_gtype(this)">添加模型</a>
    &nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size: 13px;">请按照分组查询</span>
    </h3>
    <form action="" class="form-horizontal" method="get">
        <input type="hidden" name="mod" value="site">
        <input type="hidden" name="name" value="shop">
        <input type="hidden" name="do" value="goodstype">
        <input type="hidden" name="op" value="lists">
        <table class="table table-striped table-bordered table-hover">
            <tbody>
            <tr>
                <td style="background-color: #fff">
                    <li style="float:left;list-style-type:none;">

                        <select name="group_id" id="" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0">
                            <option value="0">请选择模型分组</option>
                            <?php foreach($group_list as $gitem) {
                                if($gitem['group_id'] == $_GP['group_id']){
                                    $sel = "selected";
                                }else{
                                    $sel = '';
                                }
                                echo "<option value='{$gitem['group_id']}' {$sel} data-group_id='{$_GP['group_id']}'>{$gitem['group_name']}</option>";
                            }
                            ?>
                        </select>
                        <select name="p1" class="get_category" id="getShopCategory_p1" onchange="getShop_sonCategroy(this,1)" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
                            <option value="">请选择分类</option>
                            <?php foreach($parent_category as $item) {
                                if($item['id'] == $_GP['p1']){
                                    $sel = "selected";
                                }else{
                                    $sel = '';
                                }
                                echo "<option value='{$item['id']}' {$sel} data-id='{$_GP['p1']}'>{$item['name']}</option>";
                            }
                            ?>
                        </select>
                        <select name="p2" class="get_category" id="getShopCategory_p2" onchange=""  style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
                            <option value="">请选择分类</option>
                            <?php foreach($first_son as $item2) {
                                if($item2['id'] == $_GP['p2']){
                                    $sel = "selected";
                                }else{
                                    $sel = '';
                                }
                                echo "<option value='{$item2['id']}' {$sel}>{$item2['name']}</option>";
                            }
                            ?>
                        </select>

                        <select name="status" id="" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
                            <option value="-1" <?php if($_GP['status'] == -1 || $_GP['status'] != null){ echo 'selected'; }?> >请选择状态</option>
                            <option value="1" <?php if($_GP['status'] == 1){ echo 'selected'; }?>>已经上架</option>
                            <option value="0" <?php if($_GP['status'] == 0 && $_GP['status'] != null){ echo 'selected'; }?>>已经下架</option>
                        </select>

                    </li>

                    <li style="list-style-type:none;">
                        <button class="btn btn-primary  btn-sm" style="margin-right:10px;"><i class="icon-search icon-large"></i> 搜索</button>
                    </li>
                </td>
            </tr>
            </tbody>
        </table>
    </form>

    <div class="wrap jj">
        <div class="well form-search">
            
            <div class="table_list">
                <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                    <thead id='table_head'>
                        <tr>
                            <th class="text-center" >序号</th>
                            <th width="35%" class="text-center" >模型名称</th>
                            <th class="text-center" >状态</th>
                            <th class="text-center" >所属分类</th>
                            <th class="text-center" >操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($goodstype as $key => $gtype){ ?>
                     <tr>
                         <td class="text-center" ><?php echo ++$key;?></td>
                         <td class="text-center" ><?php echo $gtype['name'];?></td>
                         <td class="text-center" ><?php if($gtype['status'] == 1){ echo '<span class="btn btn-success btn-xs">已上架</span>'; }else{ echo '<span class="btn btn-danger btn-xs">已下架</span>';}?></td>
                         <td class="text-center">
                             asdas
                         </td>
                         <!--
                         <td class="text-center" >
                             <span class="btn btn-info btn-xs" onclick="show_gtype_info(<?php echo $gtype['id']; ?>,'attr')">查看属性</span>
                             <span class="btn btn-info btn-xs" onclick="show_gtype_info(<?php echo $gtype['id']; ?>,'spec')">查看规格</span>
                         </td>
                          -->
                         <td class="text-center" >
                             <!--
                            <a class="btn btn-primary btn-xs" href="<?php echo web_url('goodstype',array('op'=>'gattr_list','id'=>$gtype['id'])); ?>">属性列表</a>
                           -->
                            <a class="btn btn-primary btn-xs" href="javascript:;" onclick="spec_list(this)" data-url="<?php echo web_url('goodstype',array('op'=>'gspec_list','id'=>$gtype['id'])); ?>">规格操作</a>
                            <a data-url="<?php echo web_url('goodstype',array('op'=>'add_gtype','id'=>$gtype['id'])); ?>" class="btn btn-info btn-xs" href="javascript:;" onclick="add_gtype(this)">编辑模型</a>
                            <?php if($gtype['status'] == 1){  ?>
                            <span class="btn btn-danger btn-xs" data-url="<?php echo web_url('goodstype',array('op'=>'set_status','id'=>$gtype['id'])); ?>" onclick="set_status(this,0)">下架模型</span>
                            <?php }else{ ?>
                            <span class="btn btn-warning btn-xs"  data-url="<?php echo web_url('goodstype',array('op'=>'set_status','id'=>$gtype['id'])); ?>" onclick="set_status(this,1)">上架模型</span>
                            <?php } ?>
                        </td>
                     </tr>
                     <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            function add_gtype(obj)
            {
                var url = $(obj).data('url');
                $.ajaxLoad(url,{},function(){
                    $('#alterModal').modal('show');
                })
            }
            function set_status(obj,status){
                var url = $(obj).data('url');
                url = url + "&status="+status;
                window.location.href = url;
            }

            function show_gtype_info(id,type){
                var url = "<?php echo web_url('goodstype',array('op'=>'gtype_info')); ?>";
                $.ajaxLoad(url,{'id':id,'type':type},function(){
                    $('#alterModal').modal('show');
                })
            }
            function spec_list(obj){
                var url = $(obj).data('url');
                $.ajaxLoad(url,{},function(){
                    $('#alterModal').modal('show');
                })
            }
        </script>
<?php  include page('footer');?>