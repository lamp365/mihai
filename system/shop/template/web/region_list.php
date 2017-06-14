<?php defined('SYSTEM_IN') or exit('Access Denied'); ?><?php include page('header'); ?>
<style>
    .shop-list-tr li{
        float:left;
        width:300px;
    }
</style>
<h3 class="header smaller lighter blue">区域管理&nbsp;&nbsp;&nbsp;
    <a href="<?php echo web_url('region', array('do' => 'region', 'op' => 'add')); ?>" class="btn btn-primary">添加区域</a></h3>
<form action=""  class="form-horizontal" method="post">
    <table class="table table-striped table-bordered table-hover">
        <tbody>
            <tr class="shop-list-tr">
            <td>
                <li>
                    <span class="left-span">城市名称</span>
                    <input style="margin-right:5px;width: 200px; height:30px; line-height:28px; padding:2px 0" name="reg_name" type="text" value="<?php  echo $_GP['reg_name'];?>" placeholder="城市名称"/> 
                </li>	
                <li>
                    <span class="left-span">区域名称</span>
                    <input style="margin-right:5px;width: 200px; height:30px; line-height:28px; padding:2px 0" name="region_name" type="text" value="<?php  echo $_GP['region_name'];?>" placeholder="区域名称"/> 
                </li>	
                <li >
                    <button class="btn btn-primary btn-sm" ><i class="icon-search icon-large"></i> 搜索</button>
                </li>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th class="text-center" >ID</th>
            <th class="text-center" >区域名称</th>
            <th class="text-center" >城市</th>
            <th class="text-center" >操作</th>
        </tr>
    </thead>
<?php
$index = 0;
if (is_array($result)) {
    foreach ($result as $index => $item) {
        ?>
            <tr>
                <td class="text-center"><?php echo $item['reg_cst_id']; ?></td>
                <td class="text-center"><?php echo $item['reg_name']; ?></td>
                <td class="text-center"><?php echo $item['region_name']; ?></td>
                <td class="text-center">
                    <a class="btn btn-xs btn-info"  href="
                        <?php echo create_url('site', array('name' => $_GP['name'], 'do' => $_GP['do'], 'op' =>'edit', 'id' => $item['reg_cst_id'])) ?>
                       "><i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> 
                    &nbsp;&nbsp;	<a class="btn btn-xs btn-info" onclick="return confirm('此操作不可恢复，确认删除？');return false;"  href="
                        <?php echo create_url('site', array('name' => $_GP['name'], 'do' => $_GP['do'], 'op' => 'delete', 'id' => $item['reg_cst_id'])) ?>
                                    "><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a> </td>
                </td>
            </tr>
        <?php
    }
}
?>
</table>

<?php include page('footer'); ?>
								