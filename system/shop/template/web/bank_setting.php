<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue"> 数据配置 </h3>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
            <form action="<?php echo web_url('bank', array('op' => 'post'))?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="mb10">
                        <br/>
                        <br/>
                        
                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >返佣现金百分比：</div>
                            <div class="col-sm-9">
                                <input name="set_1" class="set_1" type="text"  value="<?php echo $sett[0]['value']*100?>">(%)
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >积分比例：</div>
                            <div class="col-sm-9">
                                <input name="set_2" class="set_2" type="text" value="<?php echo $sett[1]['value']?>">(1:X)(例：设置10,一元现金获得的积分即是1*10=10分)
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >返佣积分比例：</div>
                            <div class="col-sm-9">
                                <input name="set_3" class="set_3" type="text" value="<?php echo $sett[2]['value']?>">(1:X)(同上)
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >最低提现金额：</div>
                            <div class="col-sm-9">
                                <input name="set_4" class="set_4" type="text" value="<?php echo $sett[3]['value']?>">
                            </div>
                        </div>

                        <br/>
                        <br/>
                        <div class="form-group">
                            <div class="col-sm-2" ></div>
                            <div class="col-sm-9">
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
