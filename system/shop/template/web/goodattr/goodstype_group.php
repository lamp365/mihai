<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <br/>
    <ul class="nav nav-tabs" >
        <li style="width:7%" <?php  if(empty($_GP['op'])) { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('goodstype')?>">分组管理</a></li>
        <li style="width:7%" <?php  if($_GP['op'] == 'lists') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('goodstype',  array('op' => 'lists'))?>">模型管理</a></li>
    </ul>
    <h3 class="header smaller lighter blue">商品模型分组 &nbsp;&nbsp; <a data-url="<?php echo web_url('goodstype',array('op'=>'add_group')); ?>" class="btn btn-primary" href="javascript:;" onclick="add_group(this)">添加分组</a></h3>

    <div class="wrap jj">
        <div class="well form-search">
            
            <div class="table_list">
                <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                    <thead id='table_head'>
                        <tr>
                            <th class="text-center" >序号</th>
                            <th width="35%" class="text-center" >分组名称</th>
                            <th class="text-center" >创建时间</th>
                            <th class="text-center" >操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($group_list as $gtype){ ?>
                     <tr>
                         <td class="text-center" ><?php echo $gtype['group_id'];?></td>
                         <td class="text-center" ><?php echo $gtype['group_name'];?></td>
                         <td class="text-center" >
                             <?php echo date("Y-m-d H:i",$gtype['createtime']);?>
                         </td>
                         <td class="text-center" >
                            <a data-url="<?php echo web_url('goodstype',array('op'=>'add_group','group_id'=>$gtype['group_id'])); ?>" class="btn btn-info btn-xs" href="javascript:;" onclick="add_group(this)">编辑分组</a>
                            <a data-url="<?php echo web_url('goodstype',array('op'=>'del_group','group_id'=>$gtype['group_id'])); ?>" class="btn btn-danger btn-xs" href="javascript:;" onclick="del_group(this)">删除分组</a>
                        </td>
                     </tr>
                     <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            function add_group(obj)
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
        </script>
<?php  include page('footer');?>