<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>健康文化</title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
	<link href="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet"	type="text/css" />
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<link rel="shortcut icon" href="favicon.ico"/>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
</head>

<style type="text/css">
	/*觅海头条*/
.mhnews{padding: 10px 0;margin-top: 40px;}
.mhnews .mhheadline{
	width: 918px;
	height: 35px;
	overflow: hidden;
}
.mhnews .mhheadline span{
	display: inline-block;
	width: 4px;
	height: 20px;
	float: left;
	background: #E53A57;
}
.mhnews .mhheadline p{
	font-weight: bold;
	font-size: 16px;
	float:left;
	color: #E53A57;
	letter-spacing: 0;
	line-height: 20px;
	margin-bottom: 10px;
	margin-left: 10px;
	display: inline-block;
}
.mhnews .mhheadline hr{
	border: none;
	border-top: solid 1px gainsboro;
	
}
.mhnews .per-mhnews{
	margin-top: 30px;
	width: 948px;
	float: left;
}
.mhnews .per-mhnews ul{
	overflow: hidden;
	padding-top: 0px;
}
.mhnews .per-mhnews ul li{
	width: 286px;
	overflow: hidden;
	float: left;
	margin-right: 30px;
	margin-top: 12px;
}
.mhnews .per-mhnews ul li:first-child{
	margin-left: 0;
}
.mhnews .per-mhnews ul li a img{
	width: 286px;
	height: 160px;
}
.mhnews .per-mhnews ul li a p{	
	font-size: 16px;
	color: #333333;
	letter-spacing: 0;
	line-height: 18px;
	display: inline-block;
	width: 286px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	padding-right: 5px;
	margin-top: 10px;
}
.mhnews .per-mhnews ul li a span{	
	font-size: 14px;
	color: #999999;
	letter-spacing: 0;
	line-height: 18px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	padding-right: 5px;
	width: 286px;
	height: 18px;
	display: inline-block;
	margin-top: 5px;
}
.mhnews .per-mhnews ul li a p:hover{
	text-decoration: underline;
}
.mhnews .per-mhnews ul li a span:hover{
	text-decoration: underline;
}
.mhnews .per-mhnews ul li .hottag{
	width: 32px;
	height: 16px;
	line-height: 16px;
	display: inline-block;
	border: 1px solid #E53A57;
	border-radius: 4px;
	color: #E53A57;
	float: left;
	text-align: center;
}
.mhnews .per-mhnews ul li .see{
	display: inline-block;
	margin-left: 10px;
	height: 16px;
	line-height: 16px;
	float: left;
	margin-top: 2px;
}
.mhnews .per-mhnews ul li .see img{
	width: 16px;	
	float: left;
	margin-top: 3px;
}
.mhnews .per-mhnews ul li .see .seenum{
	display: inline-block;
	float: left;	
	font-size: 12px;
	color: #999999;
	letter-spacing: 0;
	line-height: 16px;
	margin-left: 2px;
}
.mhnews .seemore{	
	width: 120px;
	height: 36px;
	line-height: 36px;
	text-align: center;
	margin: 0 auto;
	margin-top: 37px;
	border: 1px solid #888888;
	border-radius: 18px;
	
}
.mhnews .seemore a{	
	font-size: 16px;
	color: #888888;
}
.hot{margin-left: 10px !important;}
.tip .jiazai{
	margin: 0 auto;
	display: block;
}
</style>

<body>

	<?php  include page('header'); ?>

	<div class="viewport mhnews">
		<div class="mhheadline">
			<div style="overflow: hidden;">
				<span></span>
				<p>最新</p>
			</div>

			<hr />
		</div>
		<!--每个头条，一行显示4个-->
		<div class="per-mhnews">
			<ul>
			<?php if (is_array($article_list)){ foreach ($article_list as $val){ ?>
				<li>
					<a target="_blank" href="<?php  echo mobile_url('article',array('name'=>'addon8','id'=>$val['id']))?>">
						<!--文章图片-->
						<img data-original="<?php  echo $val['thumb']?>" class="lazy" src="images/loading.gif" />
						<!--文章标题-->
						<p><?php  echo $val['title']?></p>
						<!--文章内容-->
						<span><?php  echo $val['description']?></span>
					</a>
					<div style="overflow: hidden;">
						<!--文章的热门标签-->
						<?php if($val['iscommend'] == 1 && $val['ishot'] == 0) {?>
							<span class="hottag">推荐</span>
						<?php }else if($val['ishot'] == 1 && $val['iscommend'] == 0){ ?>
							<span class="hottag">热门</span>
						<?php }else if($val['iscommend'] == 1 && $val['ishot'] == 1){ ?>
							<span class="hottag">推荐</span>
							<span class="hottag hot">热门</span>
						<?php } ?>
						<!--查看了文章的人数-->
						<span class="see">
							<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/seenum.png" />
							<span class="seenum"><?php  echo $val['readcount']?></span>
						</span>
					</div>
				</li>
			<?php }}?>
			</ul>
		</div>
		<!--看了又看-->
		<div class="shopinfo" style="float: right;margin-top: 42px;margin-right: 30px;border: solid 1px gainsboro;border-left: none;height: auto;overflow: hidden;">
			<div id="cshopBox" class="proinfo-side" style="height: auto;overflow: hidden;">
				<div id="seeAgainTile" class="customer-rec" style="height: auto;overflow: hidden;">
					<div class="customer-rec-title">
						<h3>
							<span>看了又看</span>
						</h3>
					</div>
					<div id="seeAndsee" class="customer-rec-list" style="">
						<ul>
							<?php  if ( is_array($jp_goods) && !empty($jp_goods)){ $num = 1; foreach ( $jp_goods as $hstory_value ){ if ($num <=10 ){?>
							<li>
								<a target="_blank"  title="<?php echo $hstory_value['title']; ?>" href="<?php  echo create_url('mobile', array('id' => $hstory_value['id'],'op'=>'dish','name'=>'shopwap','do'=>'detail'))?>" target="_blank" class="product-img">
									<img alt="<?php echo $hstory_value['title']; ?>" data-original="<?php echo $hstory_value['small']; ?>" class="lazy" src="images/loading.gif" height="80" />
								</a>
								<p>
									<a href="<?php  echo create_url('mobile', array('id' => $hstory_value['id'],'op'=>'dish','name'=>'shopwap','do'=>'detail'));?>" title="<?php echo $hstory_value['title']; ?>"  target="_blank" class="title">
										<?php echo $hstory_value['title']; ?>
									</a>
									<span class="price">
										<i>¥</i>
										<em><?php echo $hstory_value['marketprice']; ?></em>
									</span>
								</p>
							</li>
							<?php $num++; } } }?>
						</ul>
					</div>

				</div>
			</div>
		</div>

		<!--页码-->
		<?php echo $pager;?>
	</div>

	  <?php  include themePage('footer'); ?>
		      	

</body>
</html>
