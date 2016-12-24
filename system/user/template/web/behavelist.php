<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">【行为日志】</h3>
<style>
    .vip-table-list tr{
        background-color: #f9f9f9;
        border-top: 1px solid #ddd;
    }
    .vip-table-list td{
        border: 1px solid #ddd;
    }
    .vip-table-list li{
        margin-top:3px;
        float: left;
        margin-right: 10px;
        list-style: none;
    }
    .vip-table-list li select{
        height:26px;
    }
    .vip-table-list li span{
        display: inline-block;
        height:24px;
        line-height: 24px;
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
                <li><span>管理员</span></li>
                <li>
                    <select name="uid">
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
                    时间范围:
                </li>
                <li>
                    <input type="text" id="datepicker_timestart" name="timestart" value="<?php echo $_GP['timestart']; ?>" readonly="readonly" />
                    <script type="text/javascript">
                        $("#datepicker_timestart").datetimepicker({
                            format: "yyyy-mm-dd hh:ii",
                            minView: "0",
                            //pickerPosition: "top-right",
                            autoclose: true
                        });
                    </script> -
                    <input type="text"  id="datepicker_timeend" name="timeend" value="<?php echo $_GP['timeend']; ?>" readonly="readonly" />
                    <script type="text/javascript">
                        $("#datepicker_timeend").datetimepicker({
                            format: "yyyy-mm-dd hh:ii",
                            minView: "0",
                            //pickerPosition: "top-right",
                            autoclose: true
                        });
                    </script>
                </li>
                <li>
                    <input name="submit" type="submit" style="margin-top:-4px; " value=" 查 找 " class="btn btn-info">
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