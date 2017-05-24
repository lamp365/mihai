<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">银行卡列表<a class="btn btn-info btn-sm add-user" href="<?php echo web_url('bank', array('op' => 'setting'))?>">数据配置</a></h3>
    <div class="wrap jj">
        <div class="well form-search">
            
        <div class="table_list">
            <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                <thead id='table_head'>
                    <tr>
                        <th class="text-center" >编号</th>
                        <th  class="text-center" >银行名字</th>
                        <th class="text-center" >图标</th>
                        <th class="text-center" >背景图</th>
                        <th class="text-center" >背景颜色</th>
                        <th class="text-center" >操作</th>
                    </tr>
                </thead>
                <tbody id='table_body'>
                    <?php foreach ($bank as $c) { ?>
                        <tr>
                            <td style="text-align:center;"><?php  echo $c['id'];?></td>
                            <td style="text-align:center;"><?php  echo $c['bank'];?></td>
                            <td><p style="text-align:center"><img src="<?php  echo $c['card_icon'];?>" height="50" width="50"></p></td>
                            <td><p style="text-align:center"><img src="<?php  echo $c['card_bg'];?>" height="50"></p></td>
                            <td><p style="text-align:center;color:#<?php echo $c['bg_color'];?>"><?php  echo $c['bg_color'];?></p></td>
                            <td style="text-align:center;">
                                <a class="btn btn-xs btn-info"  href="<?php  echo web_url('bank', array('op'=>'edit','id' => $c['id']))?>"><i class="icon-edit"></i>修改</a>&nbsp;&nbsp;
                                <a class="btn btn-xs btn-danger"  href="<?php  echo web_url('bank', array('op'=>'delete','id' => $c['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="6">

                            <a href="<?php  echo web_url('bank', array('op' => 'add'))?>"><i class="icon-plus-sign-alt"></i> 添加银行</a>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php  include page('footer');?>