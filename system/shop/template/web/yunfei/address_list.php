<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style>
	.nav-tabs li a{
		padding: 6px 22px;
	}
</style>
<br/>
<ul class="nav nav-tabs" >
	<li style="" <?php  if($_GP['do'] == 'disharea') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'disharea','op' => 'display'))?>">运费管理</a></li>
	<li style="" <?php  if($_GP['do'] == 'promotion')  { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'promotion','do'=>'promotion','op' => 'display'))?>">促销免邮</a></li>
	<li style="" <?php  if($_GP['do'] == 'address')  { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'address','op' => 'index'))?>">退货地址</a></li>
</ul>

<h3 class="header smaller lighter blue">退货地址</h3>



<?php  include page('footer');?>
