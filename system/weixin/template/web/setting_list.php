<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
    <h3 class="header smaller lighter blue">
        微信公众号列表&nbsp;&nbsp;&nbsp;
        <a href="<?php  echo web_url('setting',array('op' =>'add','name'=>'weixin'))?>" class="btn btn-primary">添加微信公众号</a>
    </h3>

    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th style="text-align: center; width: 30px">ID</th>
            <th style="text-align: center;">访问域名</th>
            <th style="text-align: center;">公众号名称</th>
            <th style="text-align: center;">创建时间</th>
            <th style="text-align: center;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
            <tr style="text-align: center;">
                <td><?php echo $value['id'];?></td>
                <td><?php echo $value['domain'];?></td>
                <td><?php echo $value['weixinname'];?></td>
                <td><?php echo date("Y-m-d H:i:s",$value['createtime']);?></td>
                <td style="text-align: center;">
                    <a class="btn btn-xs btn-info" href="<?php echo web_url('setting', array('op' => 'add','name'=>'weixin', 'id' => $value['id']))?>">
                        <i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;
                    </a>
                    <?php if($value['is_used'] == 1){ ?>
                        <a class="btn btn-xs btn-info" href="<?php echo web_url('setting', array('op' => 'use_weixin','name'=>'weixin','is_used'=>'0', 'id' => $value['id']))?>">
                            已&nbsp;启&nbsp;用&nbsp;
                        </a>
                    <?php }else{  ?>
                        <a class="btn btn-xs btn-danger" href="<?php echo web_url('setting', array('op' => 'use_weixin','name'=>'weixin','is_used'=>'1', 'id' => $value['id']))?>">
                            已&nbsp;禁&nbsp;用&nbsp;
                        </a>
                    <?php }  ?>
                    <?php if($value['is_default'] == 1){ ?>
                        <a class="btn btn-xs btn-info" href="<?php echo web_url('setting', array('op' => 'isdefault','name'=>'weixin','is_default'=>'0', 'id' => $value['id']))?>">
                            取消默认
                        </a>
                    <?php }else{  ?>
                        <a class="btn btn-xs btn-success" href="<?php echo web_url('setting', array('op' => 'isdefault','name'=>'weixin','is_default'=>'1', 'id' => $value['id']))?>">
                            设为默认
                        </a>
                    <?php }  ?>
                </td>
            </tr>
        <?php  } } ?>
        </tbody>
    </table>

<div class="row">
    <form action="<?php echo web_url('setting', array('op' => 'loginStatus','name'=>'weixin'))?>" method="post">
        <div class="form-group">
            <label class="col-sm-1 control-label no-padding-left" >微信快捷登陆：</label>

            <div class="col-sm-1">
                <input type="radio" name="thirdlogin_weixin" value="0" <?php  echo empty($thirdlogin['enabled'])?"checked=\"true\"":"";?>> 关闭  &nbsp;&nbsp;

                <input type="radio" name="thirdlogin_weixin" value="1" <?php  echo $thirdlogin['enabled']==1?"checked=\"true\"":"";?>> 开启

            </div>
            <div class="col-sm-1">
                <button type="submit" class="btn btn-primary btn-xs">确 定</button>
            </div>
        </div>
    </form>
</div>


<?php  echo $pager;?>
<?php  include page('footer');?>