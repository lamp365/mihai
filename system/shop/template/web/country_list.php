<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">国家列表</h3>
    <div class="wrap jj">
        <div class="well form-search">
            
        <div class="table_list">
            <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                <thead id='table_head'>
                    <tr>
                        <th class="text-center" >编号</th>
                        <th width="65%" class="text-center" >国家</th>
                        <th class="text-center" >图标</th>
                        <th class="text-center" >操作</th>
                    </tr>
                </thead>
                <tbody id='table_body'>
                    <?php foreach ($country as $c) { ?>
                        <tr>
                            <td style="text-align:center;"><?php  echo $c['id'];?></td>
                            <td style="text-align:center;"><?php  echo $c['name'];?></td>
                            <td><p style="text-align:center"><img src="<?php  echo $c['icon'];?>" height="60" width="60"></p></td>
                            <td style="text-align:center;">
                                <?php if(isHasPowerToShow('shop','country','edit','edit')){ ?>
                                    <a class="btn btn-xs btn-info"  href="<?php  echo web_url('country', array('op'=>'edit','id' => $c['id']))?>"><i class="icon-edit"></i>修改</a>&nbsp;&nbsp;
                                <?php } ?>
                                <?php if(isHasPowerToShow('shop','country','delete','delete')){ ?>
                                    <a class="btn btn-xs btn-danger"  href="<?php  echo web_url('country', array('op'=>'delete','id' => $c['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4">
                            <?php if(isHasPowerToShow('shop','country','add','add')){ ?>
                                <a href="<?php  echo web_url('country', array('op' => 'add'))?>"><i class="icon-plus-sign-alt"></i> 添加国家</a>
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php  include page('footer');?>