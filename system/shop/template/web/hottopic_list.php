<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">热词列表</h3>
    <div class="wrap jj">
        <div class="well form-search">
            
        <div class="table_list">
            <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                <thead id='table_head'>
                    <tr>
                        <th class="text-center" >编号</th>
                        <th class="text-center" >分类</th>
                        <th width="50%" class="text-center" >热词</th>
                        <th class="text-center" >操作</th>
                    </tr>
                </thead>
                <tbody id='table_body'>
                    <?php if(is_array($list)) { foreach($list as $item) { ?>
                        <tr>
                            <?php $classify =  mysqld_select('SELECT * FROM '.table('shop_category')." WHERE  id=:uid" , array(':uid'=> $item['classify_id'])); ?>
                            <td style="text-align:center;"><?php echo $item['id']; ?></td>
                            <?php if ($item['classify_id'] == '0') { ?>
                                <td style="text-align:center;"><?php echo "搜索栏"; ?></td>
                            <?php }else{?>
                                <td style="text-align:center;"><?php echo $classify['name']; ?></td>
                            <?php } ?>
                            
                            <td style="text-align:center;"><?php for ($u=0; $u < count($item['name']); $u++) { echo '<a href="'.$item['url'][$u].'" title="'.$item['url'][$u].'">'.$item['name'][$u].'</a>'.'&nbsp'; } ?></td>
                            <td style="text-align:center;">
                                <a class="btn btn-xs btn-info"  href="<?php  echo web_url('hottopic', array('op'=>'edit','id' => $item['id']))?>"><i class="icon-edit"></i>修改热词</a>&nbsp;&nbsp;
                                <a class="btn btn-xs btn-danger" href="<?php  echo web_url('hottopic', array('op'=>'delete','id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<?php  include page('footer');?>