<!DOCTYPE html>
<html>
	<head>
		<?php include page('seller_header');?>
	</head>

	<body>
		<div class="layui-layout layui-layout-admin" style="border-bottom: solid 5px #1aa094;">
			<div class="layui-header header header-demo">
				<div class="layui-main">
					<div class="admin-login-box">
						<div class="admin-side-toggle">
							<i class="fa fa-bars" aria-hidden="true"></i>
						</div>
						<a class="logo" style="left: 0;" href="">
							<span style="font-size: 22px;">CBD管理系统</span>
						</a>
					</div>
					<ul class="layui-nav admin-header-item">
						<li class="layui-nav-item">
                            <a data-url="<?php echo  mobile_url('store_shop',array('op'=>'getStep'))?>" href="javascript:;" class="add-shop-one" ><span>+店铺入驻</span></a>
						</li>
						<li class="layui-nav-item">
							<a href="javascript:;" class="admin-header-user">
								<img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/0.jpg" />
								<span><?php echo $member['store_sts_name']; ?></span>
							</a>
							<dl class="layui-nav-child" >
								<dd>
									<a href="javascript:;" class="modify-password"><i class="fa fa-sign-out" aria-hidden="true"></i> 修改密码</a>
								</dd>
								<?php if(count($mem_store) > 1){ ?>
								<dd>
									<a href="javascript:;" class="change_store"><i class="fa fa-sign-out" aria-hidden="true"></i> 店铺切换</a>
								</dd>
								<?php } ?>
								<dd>
									<a target="_blank" href="<?php echo WEBSITE_ROOT.'index.php';?>"><i class="fa fa-sign-out" aria-hidden="true"></i> 前台首页</a>
								</dd>
								<dd>
									<a href="<?php echo web_url('logout',array('name'=>'public')); ?>"><i class="fa fa-sign-out" aria-hidden="true"></i> 退出</a>
								</dd>
							</dl>
						</li>
					</ul>
					<ul class="layui-nav admin-header-item-mobile">
						<li class="layui-nav-item">
							<a href="<?php echo web_url('logout',array('name'=>'public')); ?>"><i class="fa fa-sign-out" aria-hidden="true"></i> 退出</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="layui-side layui-bg-black" id="admin-side">
				<div class="layui-side-scroll" id="admin-navbar-side" >
					<ul class="layui-nav layui-nav-tree beg-navbar" lay-filter="side">
					<!-- class ='layui-nav-itemed' 代表当前导航展开 -->

						<?php foreach($leftMenu as $row_menu){ $menu = $row_menu['main']; ?>
						<li class="layui-nav-item">
						<!-- 商品管理一级导航 -->
							<a href="javascript:;">
								<i class="fa fa-cubes" aria-hidden="true" data-icon="fa-cubes"></i>
								<cite><?php echo $menu['rule_name']; ?></cite>
								<span class="layui-nav-more"></span>
							</a>
							<!-- 商品管理二级导航 lay-id请勿重复，唯一标识符-->
							<dl class="layui-nav-child">

								<?php foreach($row_menu['child'] as $the_item){ $m_item = $the_item['main']; ?>
								<dd title="<?php echo $m_item['rule_name']; ?>" >
									<div class="left-nav-div" lay-id="<?php echo $m_item['rule_id']; ?>" data-url="<?php echo mobile_url($m_item['moddo'],array('op'=>$m_item['modop'])); ?>">
										<i class="fa fa-pencil-square-o"></i>
										<cite><?php echo $m_item['rule_name']; ?></cite>
									</div>
								</dd>
								<?php } ?>

							</dl>
						</li>
						<?php } ?>


						<br/>
						<span class="layui-nav-bar" style="top: 22.5px; height: 0px; opacity: 0;"></span>
					</ul>
				</div>
			</div>
			<div class="layui-body" style="bottom: 0;border-left: solid 2px #1AA094;" id="admin-body">
				<div class="layui-tab admin-nav-card layui-tab-brief" lay-filter="admin-tab">
					<ul class="layui-tab-title">
						<li class="layui-this">
							<i class="fa fa-dashboard" aria-hidden="true"></i>
							<cite>首页</cite>
						</li>
					</ul>
					<div class="layui-tab-content" style="min-height: 150px; padding: 0;">
						<div class="layui-tab-item layui-show">
                           	<iframe name="frame" class="parent-iframe" src="<?php echo mobile_url('main',array('name'=>'seller')); ?>"></iframe>
							<!-- 链接到数据报表页面 -->
                            <!-- <iframe name="frame" class="parent-iframe" src="<?php echo mobile_url('datareport',array('name'=>'seller')); ?>"></iframe> -->
						</div>
					</div>
				</div>
			</div>
			<div class="layui-footer footer footer-demo" id="admin-footer" style='z-index: 1000;'>
				<div class="layui-main">
					<p>&nbsp;</p>
				</div>
			</div>
			<div class="site-tree-mobile layui-hide">
				<i class="layui-icon">&#xe602;</i>
			</div>
			<div class="site-mobile-shade"></div>
			<div class="left-nav-url"></div>

			<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/index.js"></script>
			<script>
				layui.use('layer', function() {
					var $ = layui.jquery,
						layer = layui.layer;
						//修改密码弹出框
						$(".modify-password").on("click",function(){
							layer.open({
							  title:'修改密码',
							  type: 2, 
							  area : ['350px' , '300px'],
							  content: "<?php echo mobile_url('password',array('name'=>'seller')); ?>" //这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
							});
						});

				});

				$(function(){
					$(".change_store").on("click",function(){
						var url = "<?php echo mobile_url('shop',array('op'=>'change_store')); ?>";
						$.ajaxLoad(url,{},function(data){
							$('#alterModal').modal('show');
						});
					});
				});
			</script>
		</div>

	<?php include page('seller_footer');?>
	</body>

</html>