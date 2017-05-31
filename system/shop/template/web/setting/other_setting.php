<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style>
    .nav-tabs li a{
        padding: 6px 15px;
    }
</style>
<body class="J_scroll_fixed">
<br/>
<ul class="nav nav-tabs" >
    <li style="" <?php  if($_GP['op'] == 'index') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('config',  array('op' => 'index'))?>">基础设置</a></li>
    <li style="" <?php  if($_GP['op'] == 'otherSet') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('config',  array('op' => 'otherSet'))?>">其他设置</a></li>
</ul>
<br/>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
            <form action="<?php echo web_url('config', array('op' => 'otherSet'))?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="mb10">
                        <br/>
                        <br/>
                        
                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >返佣现金百分比：</div>
                            <div class="col-sm-3">
                                <input name="com_gold" class="set_1 form-control"  type="text"  value="<?php echo $sett['com_gold']*100?>">
                            </div>
                            <div class="col-sm-1" style="margin-top:5px;">
                                (%)
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >积分比例：</div>
                            <div class="col-sm-3">
                                <input name="credit_ratio" class="set_2 form-control" type="text" value="<?php echo $sett['credit_ratio']?>">
                            </div>
                            <div class="col-sm-4" style="margin-top:5px;">
                                (1:X)(例：设置10,一元现金获得的积分即是1*10=10分)
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >返佣积分比例：</div>
                            <div class="col-sm-3">
                                <input name="com_credit" class="set_3 form-control" type="text" value="<?php echo $sett['com_credit']?>">
                            </div>
                            <div class="col-sm-1" style="margin-top:5px;">
                                (1:X)(同上)
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >最低提现金额：</div>
                            <div class="col-sm-3">
                                <input name="teller_limit" class="set_4 form-control" type="text" value="<?php echo $sett['teller_limit']?>">
                            </div>
                        </div>

                        <br/>
                        <br/>
                        <div class="form-group">
                            <div class="col-sm-2" ></div>
                            <div class="col-sm-9">
                                <input type="hidden" name="doadd" value="1">
                                <input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
                            </div>
                        </div>
                </div>
            </form>
            </div>
        </div>
    </div>
<?php  include page('footer');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/jscolor/jscolor.js"></script>
