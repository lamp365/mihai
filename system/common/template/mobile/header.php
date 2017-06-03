<?php defined('SYSTEM_IN') or exit('Access Denied');
	   $cfg = globaSetting();
       if ( empty( $member ) ){
             $member=get_member_account(false);
		   	 if(empty($member['openid'])){
				 $member=member_get($member['openid']);
			 }
		   $mem_rank = member_rank_model($member['experience']);
	    }else{
		   $mem_rank = member_rank_model($member['experience']);
	   }
		$is_login=is_login_account();
		$article_foot = getArticle(4,1);
		$shop_menu_list = mysqld_selectall("SELECT * FROM " . table('shop_menu')." where menu_type='fansindex' and type = 1 order by torder desc" );	
		$advtop = mysqld_select("select * from " . table('shop_adv') . " where enabled=1 and type = 1 and page = 4 order by displayorder desc");
		if ( empty($category) ){
				$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 ORDER BY parentid ASC, displayorder DESC");
				foreach ($category as $index => $row) {
					if (!empty($row['parentid'])) {
						$children[$row['parentid']][$row['id']] = $row;
						unset($category[$index]);
					}
				}
		}
        $category = index_c_goods($category,4);

		if ( !function_exists(getHottpoic) ){
	       if (file_exists(WEB_ROOT . '/includes/hottpoic.func.php')) {
               require WEB_ROOT . '/includes/hottpoic.func.php';
           }
	    }
        $hot = getHottpoic(0);

?>

<!--[if lt IE 9]>
<div class="m-browserupdate">
<p>您的浏览器该退休啦！为了您的购物安全，觅海建议您升级浏览器：<a onclick="window._dapush('_trackEvent', '浏览器升级提示', '点击', 'chrome')" class="w-icn-14" target="_blank" href="http://mm.bst.126.net/download/ChromeSetup.exe" rel="nofollow">chrome浏览器</a>，<a onclick="window._dapush('_trackEvent', '浏览器升级提示', '点击', 'firefox')" class="w-icn-14 w-icn-14-2" target="_blank" href="http://www.firefox.com.cn/" rel="nofollow">火狐浏览器</a> 或 <a onclick="window._dapush('_trackEvent', '浏览器升级提示', '点击', 'IE')" class="w-icn-14 w-icn-14-3" target="_blank" href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie" rel="nofollow">最新IE浏览器</a></p>
</div>
<![endif]-->
<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/js/index_nav.js"></script>
<div class="navtop" style="display:none;padding:0;background:none;height:100px;">
      <a href="<?php echo $advtop['link'];?>" target="_blank" ><img src="<?php echo $advtop['thumb'];?>" height="100"/></a>
</div>
<div class="navtop">
   <div class="center">
       <div class="le"> 
            <ul class="le-list">
            <li style="margin-right: 5px;">您好，欢迎来到觅海环球购！</li>
             <?php 
	             if ( !empty($member['mobile']) ){
			  ?>
	             <li style="position: relative;" class="le-login-hover"> 
	              		<a class="le-login-hover-a" href="javascript:;">
	              		<img style="width: 16px;margin-right: 3px;vertical-align: middle;" src="<?php echo $mem_rank['icon'];?>"><?php echo $member['mobile']; ?><i class="re-icon icon-sort-down"></i></a>
	              		<div class="le-child-list">
	                		<div class="clearfix">
								<?php if(empty($member['avatar'])){ ?>
								<img class='header_02_img' src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/userface.png" data-pic=''/>
	                			<?php }else{  ?>
								<img class='header_02_img' src="<?php echo download_pic($member['avatar'],100,100,2); ?>" data-pic=''/>
	                			<?php } ?>
								<a class="le-child-list-login" href="<?php  echo mobile_url('logout',array('name'=>'shopwap')); ?>"><?php echo $member['mobile']; ?> [退出]</a>
							</div>
		   					<ul class="clearfix">
		   						<!-- <li>
		   							<a href="#">我的收藏</a>
		   						</li> -->
		   						<li>
		   							<a href="<?php  echo mobile_url('myorder',array('name'=>'shopwap')); ?>" target="_blank">我的订单</a>
		   						</li>
		   						<!-- <li>
		   							<a href="#">觅海钱包</a>
		   						</li> -->
		   						<li>
		   							<a href="<?php  echo mobile_url('bonus',array('name'=>'shopwap')); ?>" target="_blank">我的优惠券</a>
		   						</li>
		   						<!-- <li>
		   							<a href="#">我的觅海币</a>
		   						</li> -->
		   					</ul>
		   				</div>
	              </li>
				  <li style="position: relative;"><span style="position: absolute;left: -3px;color: #ccc">|</span> <a href="<?php  echo mobile_url('fansindex',array('name'=>'shopwap')); ?>" target="_blank" style="padding: 0 7px;">个人中心</a></li>
				<?php
	                }else{
				?>
	                <li style="position: relative;" class="le-login-hover">
	                	<a class="le-login-hover-a" href="<?php  echo mobile_url('login',array('name'=>'shopwap')); ?>">请登录</a>
	                	<div class="le-child-list">
	                		<div class="clearfix">
	                			<img class="header_02_img" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/userface.png">
	                			<a class="le-child-list-login" href="<?php  echo mobile_url('login',array('name'=>'shopwap')); ?>">您好！[请登录]</a>
	                		</div>
		   					<ul class="clearfix">
		   						<!-- <li>
		   							<a href="#">我的收藏</a>
		   						</li> -->
		   						<li>
		   							<a href="<?php  echo mobile_url('myorder',array('name'=>'shopwap')); ?>" target="_blank">我的订单</a>
		   						</li>
		   						<!-- <li>
		   							<a href="#">觅海钱包</a>
		   						</li> -->
		   						<li>
		   							<a href="<?php  echo mobile_url('bonus',array('name'=>'shopwap')); ?>" target="_blank">我的优惠券</a>
		   						</li>
		   						<!-- <li>
		   							<a href="#">我的觅海币</a>
		   						</li> -->
		   					</ul>
		   				</div>
	                </li>
					<li style="position: relative;"><span style="position: absolute;left: -3px;color: #ccc">|</span><a href="<?php  echo mobile_url('regedit',array('name'=>'shopwap')); ?>"  style="padding: 0 7px;">免费注册</a></li>
				<?php
				}
				?>
			</ul>
	   </div>
	   <div class="re">
	   		<ul class="re-list">
	   			<li><a href="<?php  echo mobile_url('iclub',array('name'=>'shopwap','op'=>'display')); ?>" target="_blank"><i class="icon-flag" style="margin-right:5px;color: #E31436"></i>每日签到</a></li>
	   			<li><a href="<?php  echo mobile_url('myorder',array('name'=>'shopwap')); ?>" target="_blank">我的订单</a></li>
	   			<!-- <li class="re-collection">
	   				<a href="javascript:;" class="re-collection-a">我的收藏<i class="re-icon icon-sort-down"></i></a>
	   				<div class="re-child-list">
	   					<div><a href="#">收藏的商品(0)</a></div>
	   				</div>
	   			</li> -->
	   			<li class="re-vip">
	   				<a href="<?php  echo mobile_url('iclub',array('name'=>'shopwap','op'=>'display')); ?>" target="_blank" class="re-vip-a">会员俱乐部<i class="re-icon icon-sort-down"></i></a>
	   				
	   				<div class="re-child-list">
	   					<div><a href="<?php echo mobile_url('iclub',array('name'=>'shopwap')) ?>">俱乐部首页</a></div>
	   					<div><a href="<?php echo mobile_url('iclub',array('name'=>'shopwap')) ?>">觅海币兑换</a></div>
	   				</div>
	   			</li>
			 
				<li class="re-mobile"><a href="Javascript:;" >
					<i class="icon-mobile-phone"></i>微信版</a>
					<div class="mobile-code"><img src="<?php echo getFullPicUrl('images/weixin.jpg'); ?>"><div style="text-align: center;">随时逛，及时抢</div></div>
				</li>
				<li><a href="<?php  echo mobile_url('mycart',array('name'=>'shopwap')); ?>"  target="_blank" ><i class="re-icon icon-shopping-cart" style="color: #828282;margin-right: 5px;"></i>购物车</a></li>
			  	
		  </ul>
	   </div>
   </div>
</div>
<div class="nav" style="overflow: visible;height: 104px;box-sizing: border-box;">
    <a href="<?php echo WEBSITE_ROOT;?>" style="margin:0 60px 0 0;float:left;"><img src="<?php echo $cfg['shop_logo']; ?>" height="60" /></a>
    <div class="WX_search1" id="mallHead" >
		  <form class="WX_search_frm1" action="index.php" id="searchForm"
			name="searchForm">
			<input type="hidden" name="mod" value="mobile" /><input
				type="hidden" name="op" value="dish" /> <input
				type="hidden" name="do" value="goodlist" /> <input type="hidden"
				name="name" value="shopwap" /> <input name="keyword"
				id="search_word" class="WX_search_txt hd_search_txt_null"
				placeholder="请输入商品名进行搜索！" ptag="37080.5.2" type="search"
				AUTOCOMPLETE="off"/>
			<div class="WX_me">
				<a href="javascript:;" id="submit" class="WX_search_btn_blue">搜索</a>
			</div>
		   </form>
		
		   <ul class="keyword">
		        <?php foreach($hot as $keyword){ ?>
                     <li><a href="<?php echo $keyword['url']; ?>"><?php echo $keyword['name']; ?></a></li>
				<?php } ?>
		   </ul>
		   
		    <!--搜索框的相关搜索数据-->
		   <ul id="search-related" class="srelated">

		   </ul>
	   </div>
</div>
<nav class="topTabbox" style="position: relative;z-index: 100;">
    <div class="nav2" style="padding:0;height:40px;background:none;">
     <ul style="float:right;">
	 <li class="f_category">
	 <div class="lineicon"><i></i><i></i><i></i></div><span>所有分类</span>
	 </li>
    <?php foreach($shop_menu_list as $value){ if(strstr("http://",$value['url'])){ $m_url = $value['url']; }else{ $url= WEBSITE_ROOT.$value['url']; } ?>
         <li><a href="<?php echo $url; ?>" target="_blank" ><?php echo $value['tname']; ?></a></li>
	<?php }?>
	</ul>
    <ul class="catitmlst j-catmenu">
    <?php foreach($category as $value){ ?>
         <li style="height: 40px;">
		        <a href="<?php  echo mobile_url('goodlist', array('name'=>'shopwap','pcate' => $value['id'])); ?>" target="_blank" >
				<img class="icon" src="<?php echo $value['thumb']; ?>">
				<span class="t"><?php echo $value['name']; ?></span></A>
				<i class="icon-angle-right"></i>
				<em class="vcenter"></em>
				<em class="seg"></em>
				<div class="m-ctgcard f-cb"></div>
				
				<div class="c_category">
					<ul class="c2_category">
					<?php if (is_array($children[$value['id']])){ foreach( $children[$value['id']] as $c_value ){ ?>
						  <li><a href="<?php  echo mobile_url('goodlist', array('name'=>'shopwap','p2' => $c_value['id'])); ?>" target="_blank" ><img  src="<?php echo $c_value['thumb']; ?>" height="40"><?php echo $c_value['name']; ?></a></li>
					<?php }} ?>
					</ul>
				</div>
         </li>
	<?php }?>
	 
</ul> 
     <div class="topimg" style="position:absolute;right:5px;bottom:0;">
          <a href="<?php echo $advtop['link'];?>" target="_blank" ><img  src="<?php echo $advtop['thumb'];?>" /></a>
	</div>
	</div>
</nav>

