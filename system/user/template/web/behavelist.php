<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">【行为日志】</h3>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<style>

    .vip-table-list{
        border: 1px solid #ddd;padding: 7px 0;
    }
    .left-span{
        float: left;
        line-height: 28px;
        background-color: #ededed;
        padding: 0 5px;
        border: 1px solid #cdcdcd;
        border-right: 0;
        font-size: 12px;
    }
    .vip-table-list li{
        float: left;    
        margin-right: 10px;
        list-style-type: none;
    }
    .vip-table-list .li-height{
        height: 30px;
        padding-left: 5px;
    }
</style>
<form action="" method="get" class="form-horizontal" enctype="multipart/form-data">

    <input type="hidden" name="op" value="list" />
    <input type="hidden" name="name" value="user" />
    <input type="hidden" name="do" value="behave" />
    <input type="hidden" name="mod" value="site"/>
    <table class="table vip-table-list" style="width:100%;" align="center">
        <tbody>
        <tr>
            <td>
                <li>
                    <span class="left-span">管理员</span>
                    <select class="li-height" name="uid">
                        <option value="0">选择管理员</option>
                        <?php foreach($all_admin as $admin){
                            if($admin['id'] == $_GP['uid']){
                                $sel = "selected='selected'";
                            }else{
                                $sel = '';
                            }
                            echo "<option value='{$admin['id']}' {$sel}>{$admin['username']}</option>";
                        } ?>
                    </select>
                </li>
                <li>
                    <span class="left-span">起始日期</span>
                    <input class="li-height" type="text" id="datepicker_timestart" name="timestart" placeholder="起始日期" value="<?php echo $_GP['timestart']; ?>" readonly="readonly" />
                </li>
                <li>-</li>
                <li>
                    <span class="left-span">终止日期</span>
                    <input class="li-height" type="text"  id="datepicker_timeend" name="timeend" placeholder="终止日期" value="<?php echo $_GP['timeend']; ?>" readonly="readonly" />

                    <script type="text/javascript">
                        laydate({
                            elem: '#datepicker_timestart',
                            istime: true, 
                            event: 'click',
                            format: 'YYYY-MM-DD hh:mm:ss',
                            istoday: true, //是否显示今天
                            start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
                        });
                        laydate({
                            elem: '#datepicker_timeend',
                            istime: true, 
                            event: 'click',
                            format: 'YYYY-MM-DD hh:mm:ss',
                            istoday: true, //是否显示今天
                            start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
                        });
                        laydate.skin("molv"); 
                    </script> 
                </li>
                <li>
                    <input name="submit" type="submit" value=" 查 询 " class="btn btn-primary btn-sm">
                </li>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;;">
    <thead >
    <tr>
<!--        <th style="text-align:center;min-width:20px;"><input type="checkbox" class="btn btn-xs btn-info choose_all"><span class="box_zi">全选</span></th>-->
        <th style="text-align:center;min-width:20px;">ID</th>
        <th style="text-align:center; min-width:130px;">操作位置</th>
        <th style="text-align:center; min-width:13px;">管理员</th>
        <th style="text-align:center; min-width:13px;">角色名</th>
        <th style="text-align:center; min-width:13px;">IP</th>
        <th style="text-align:center; min-width:13px;">地区</th>
        <th style="text-align:center; min-width:30px;">操作id</th>
        <th style="text-align:center; min-width:30px;">操作时间</th>
    </tr>
    </thead>
    <tbody>
    <?php  if(!empty($all_log)) { foreach($all_log as $item) { ?>
        <tr>
<!--            <td style="text-align:center;"><input type="checkbox"  name="id[]" value="--><?php //echo $item['id'];?><!--" class="child_box"></td>-->
            <td style="text-align:center;"><?php echo $item['id'];?></td>

            <td style="text-align:center;"><?php echo $item['name'].' '.$item['message'];?></td>
            <td style="text-align:center;"><?php echo getAdminName($item['uid']);?></td>
            <td style="text-align:center;"><?php echo $item['rolername'];?></td>
            <td style="text-align:center;"><?php echo $item['ip'];?></td>
            <td style="text-align:center;"><?php echo $item['area'];?></td>
            <td style="text-align:center;"><?php echo $item['act_id'];?></td>
            <td style="text-align:center;"><?php echo date("Y-m-d H:i:s",$item['createtime']);?></td>
        </tr>
    <?php  } } ?>
    </tbody>
</table>
<?php  echo $pager;?>

<?php  include page('footer');?>
<script>
    $(".choose_all").click(function(){
        if(this.checked){
            $(".child_box").each(function(){
                this.checked = true;
            })
        }else{
            $(".child_box").each(function(){
                this.checked = false;
            })
        }

    })

</script>