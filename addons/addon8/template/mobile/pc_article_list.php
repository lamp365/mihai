<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<link type="text/css" href="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/bootstrap3/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet"	type="text/css" />
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<link rel="shortcut icon" href="favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="../../../../themes/default/__RESOURCE__/recouse/css/pc_article_list.css"/>
	<link rel="stylesheet" type="text/css" href="../../../../themes/default/__RESOURCE__/recouse/css/PingFang-font.css"/>
	<script type="text/javascript"src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/bootstrap3/js/bootstrap.min.js"></script>
	<script src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/js/swiper-3.4.0.jquery.min.js" type="text/javascript" charset="utf-8"></script>
   	<link rel="stylesheet" type="text/css" href="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/css/swiper-3.4.0.min.css"/>   
</head>


<body>

	<?php  include page('header'); ?>
<?php if($_GP['op'] == 'healty' || empty($_GP['op'])){  ?>
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
					<div id="seeAndsee" class="customer-rec-list">
						<ul>
							<?php  if ( is_array($jp_goods) && !empty($jp_goods)){ $num = 1; foreach ( $jp_goods as $hstory_value ){ if ($num <=10 ){?>
							<li>
								<a target="_blank"  title="<?php echo $hstory_value['title']; ?>" href="<?php  echo create_url('mobile', array('id' => $hstory_value['id'],'op'=>'dish','name'=>'shopwap','do'=>'detail'))?>" target="_blank" class="product-img">
									<img alt="<?php echo $hstory_value['title']; ?>" data-original="<?php echo $hstory_value['small']; ?>" class="lazy" src="images/loading.gif" height="80" />
								</a>
								<p style="padding: 0;">
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
	<!--觅海头条-->
	<?php }else if($_GP['op'] == 'headline'){  ?>
		<div class="viewport">
			<!--tab-->
			<ul class="articletab hltab">
				<li <?php if($_GP['op']=='headline' || empty($_GP['op'])){ echo "class='article_active'"; } ?>>
					<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline'))?>">觅海头条</a>
				</li>
				<li <?php if($_GP['op']=='note'){ echo "class='article_active'"; } ?>>
					<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'note'))?>">晒物笔记</a>
				</li>
			</ul>
			<!--tab下面的-->
			<div class="content">
				<!--左边的头条列表-->
				<div class="headline_list">
					<ul>
						<!--显示10条-->
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
						<li>
							<!--头条没有图片，就显示默认日期样式-->
							<div>	
								<a href="#">							
									<p>22</p>
									<p style="margin-top: 0;">十一月</p>
								</a>
							</div>							
							<a href="#">
								<p class="title">吐槽一下你用过的护肤品吧</p>
							</a>							
						</li>
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
						<li>
							<!--头条图片-->
							<div>
								<a href="#">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>	
								</a>							
							</div>
							<!--头条标题-->
							<a href="#">
								<p class="title">给各位盘点一些可以入手的尖货吧！给各位盘点一些可以入手的尖货吧</p>
							</a>
						</li>
					</ul>
				</div>
				<!--右边的头条详情-->
				<div class="headline_detail">						
					<!--轮播图-->
					<!--如果没有上传图片，那这一块就没有,直接显示文章-->
					<div class="swiper-container">
			    		<div class="swiper-wrapper">
			    			<!--一个slide是一张图片，最多出5张-->
					        <div class="swiper-slide">					        	
					        	<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>				        	
					        </div>					        
						</div>						
    					<div class="swiper-pagination"></div>
    				</div> 
    				<!--头条标题-->
    				<p>
    					给各位盘点一些值得入手的尖货吧    					
    				</p>
    				<div class="content">
    					<div class="info">
    						<!--头像，用户名-->
    						<div class="men">
    							<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/tx.jpg" />
    							<span>觅海掌门人</span>
    						</div>
    						<!--发表时间-->
    						<div class="time">
    							<span>2016/12/19</span>
    							<span>17:25</span>
    						</div>
    					</div>
    					<!--头条内容-->
    					<div class="detail">
    						看见啊发货计划改U看见啊</br>发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U
    						看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U
    						看见啊发货计划改U看见啊</br>发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U
    						看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U
    						看见啊发货计划改U看见啊</br>发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U
    						看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U
    						看见啊发货计划改U看见啊</br>发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U
    						看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U看见啊发货计划改U
    					</div>
    					<!--评论-->
    					<div class="comment">
    						<!--点击img到app下载页-->
    						<a href="">
    							<img class="todownapp" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/hl_03.jpg"/>
    						</a>
    						<span>评论20</span>
    						<!--评论列表-->
    						<ul>
    							<!--一个li是一条评论-->
    							<li>
    								<div class="meninfo">
    									<!--头像-->
    									<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/tx.jpg" />
    									<!--用户名-->
    									<span>爱炫富的小哥</span>
    									<!--评论时间-->
    									<span class="commenttime">
    										2016/12/20 11:53
    									</span>
    								</div>
    								<!--评论内容-->
    								<div class="cdetail">
    									这个多少钱？
    								</div>
    							</li>    							
    						</ul>
    					</div>    					
    				</div>    					
				</div>
			</div>			
		</div>
		
	<!--晒物笔记-->
	<?php }else { ?>
		<!--引入首页觅海头条，笔记的样式文件-->
		<link rel="stylesheet" type="text/css" href="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/css/community.css" />
		<div class="viewport">
			<!--tab-->
			<ul class="articletab">
				<li <?php if($_GP['op']=='headline' || empty($_GP['op'])){ echo "class='article_active'"; } ?>>
					<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline'))?>">觅海头条</a>
				</li>
				<li <?php if($_GP['op']=='note'){ echo "class='article_active'"; } ?>>
					<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'note'))?>">晒物笔记</a>
				</li>
			</ul>
			
			<div class="notelist">
    		<ul>
    			<li  data-toggle="modal" data-target="#note-detail">
    				<!--笔记图片-->
    				<a href="#" class="notepic">
    					<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/bg3.png"/>
    				</a>
    				<!--图片下面的东西-->
    				<div class="noteinfo">
    					<!--笔记详情-->
    					<a href="#">
	    					<p>
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    					</p>
    					</a>
    					<!--头像，用户名-->
    					<div>
    						<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />
    						<h3>觅海掌门人</h3>
    					</div>
    				</div>
    			</li>
    			<li  data-toggle="modal" data-target="#note-detail">
    				<!--笔记图片-->
    				<a href="#" class="notepic">
    					<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/912865945439541.jpg"/>
    				</a>
    				<!--图片下面的东西-->
    				<div class="noteinfo">
    					<!--笔记详情-->
    					<a href="#">
	    					<p>
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    					</p>
    					</a>
    					<!--头像，用户名-->
    					<div>
    						<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />
    						<h3>觅海掌门人</h3>
    					</div>
    				</div>
    			</li>
    			<li  data-toggle="modal" data-target="#note-detail">
    				<!--笔记图片-->
    				<a href="#" class="notepic">
    					<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/912865945439541.jpg"/>
    				</a>
    				<!--图片下面的东西-->
    				<div class="noteinfo">
    					<!--笔记详情-->
    					<a href="#">
	    					<p>
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    					</p>
    					</a>
    					<!--头像，用户名-->
    					<div>
    						<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />
    						<h3>觅海掌门人</h3>
    					</div>
    				</div>
    			</li>
    			<li  data-toggle="modal" data-target="#note-detail">
    				<!--笔记图片-->
    				<a href="#" class="notepic">
    					<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/912865945439541.jpg"/>
    				</a>
    				<!--图片下面的东西-->
    				<div class="noteinfo">
    					<!--笔记详情-->
    					<a href="#">
	    					<p>
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    					</p>
    					</a>
    					<!--头像，用户名-->
    					<div>
    						<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />
    						<h3>觅海掌门人</h3>
    					</div>
    				</div>
    			</li>
    			<li  data-toggle="modal" data-target="#note-detail">
    				<!--笔记图片-->
    				<a href="#" class="notepic">
    					<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/912865945439541.jpg"/>
    				</a>
    				<!--图片下面的东西-->
    				<div class="noteinfo">
    					<!--笔记详情-->
    					<a href="#">
	    					<p>
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    					</p>
    					</a>
    					<!--头像，用户名-->
    					<div>
    						<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />
    						<h3>觅海掌门人</h3>
    					</div>
    				</div>
    			</li>
    			<li  data-toggle="modal" data-target="#note-detail">
    				<!--笔记图片-->
    				<a href="#" class="notepic">
    					<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/912865945439541.jpg"/>
    				</a>
    				<!--图片下面的东西-->
    				<div class="noteinfo">
    					<!--笔记详情-->
    					<a href="#">
	    					<p>
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    					</p>
    					</a>
    					<!--头像，用户名-->
    					<div>
    						<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />
    						<h3>觅海掌门人</h3>
    					</div>
    				</div>
    			</li>
    			<li data-toggle="modal" data-target="#note-detail">
    				<!--笔记图片-->
    				<a href="#" class="notepic">
    					<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/912865945439541.jpg"/>
    				</a>
    				<!--图片下面的东西-->
    				<div class="noteinfo">
    					<!--笔记详情-->
    					<a href="#">
	    					<p>
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    					</p>
    					</a>
    					<!--头像，用户名-->
    					<div>
    						<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />
    						<h3>觅海掌门人</h3>
    					</div>
    				</div>
    			</li>
    			<!--最后一个笔记-->
    			<li>
    				<!--笔记图片-->
    				<a href="#" data-toggle="modal" data-target="#note-detail" class="notepic">
    					<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/912865945439541.jpg"/>
    				</a>
    				<!--查看更多-->
    				<div class="lookmore">
    					<div>
    						<span></span>
    						<span></span>
    						<span></span>
    					</div>    					
    					<p>查看更多笔记</p>
    				</div>
    			</li>
    		</ul>     		
    	</div>
    	<!--点击笔记弹出的-->
			<div class="modal fade" taria-hidden="true" id="note-detail">
				<div class="modal-dialog">
					<div class="modal-content">	
						<div class="modal-header">
							<!--头像-->
							<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />
							<div class="info">
								<p>用户名</p>
								<!--发表日期-->
								<p>2016/12/21 09:59</p>
							</div>														
							<img class="comment" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/comment.png">
							<!--评论数量-->
							<span>20</span>
						</div>	
											  	
					  	<div class="modal-body">
							<!--笔记详情-->
							<!--内容多时出现滚动条-->
							<p>								
								疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
	    						疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果
							</p>
							<!--笔记图片,最少一张，最多五张,全部排列下去-->
							<img src="../../../../themes/default/__RESOURCE__/912865945439541.jpg"/>	
							<!--评论-->
							<a href="">
    							<img class="todownapp" src="http://dev-hinrc.com/themes/default/__RESOURCE__/recouse/images/hl_03.jpg">
    						</a>	
    						<span style="margin-top: 20px;display: block;">评论20</span>
    						<!--评论列表-->
    						<ul>
    							<!--一个li是一条评论-->
    							<li>
    								<div class="meninfo">
    									<!--头像-->
    									<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/tx.jpg" />
    									<!--用户名-->
    									<span>爱炫富的小哥</span>
    									<!--评论时间-->
    									<span class="commenttime">
    										2016/12/20 11:53
    									</span>
    								</div>
    								<!--评论内容-->
    								<div class="cdetail">
    									这个多少钱？
    								</div>
    							</li>   													
    						</ul>					
						</div>
					</div>
				</div>
			</div>   		
	</div>
	<?php } ?>				
	<?php  include themePage('footer'); ?>		      	

</body>
<script>
	//	头条图片滚动控制	       
	  var mySwiper = new Swiper ('.swiper-container', {	
	  	direction: 'horizontal',					   
	    loop: true,
	    autoplay:2000,
	    pagination: '.swiper-pagination',							    					     	    
	  }) 
	  
	  //如果头条轮播图没有图片，就将那块去掉
	 var len = $(".swiper-wrapper .swiper-slide").length;	  
	  if(len == 0){
	  	$(".swiper-container").hide();
	  }   
	  
	  //点击查看更多笔记，直接在当前页出现隐藏的笔记，点击一次出三行
	$(".lookmore").click(function(){
		var note = '<li data-toggle="modal" data-target="#note-detail">'+
			'<!--笔记图片-->'+
			'<a href="#"  class="notepic">'+
				'<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/912865945439541.jpg"/>'+
			'</a>'+
			'<!--图片下面的东西-->'+
			'<div class="noteinfo">'+
				'<!--笔记详情-->'+
				'<a href="#">'+
					'<p>'+
						'疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看'+	    						
					'</p>'+
				'</a>'+
				'<!--头像，用户名-->'+
				'<div>'+
					'<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/f5.png" />'+
					'<h3>觅海掌门人</h3>'+
				'</div>'+
			'</div>'+
		'</li>';    			
		$(".viewport .notelist ul li:last-child").before(note);    			
	})
	
	//只有头条二级页滚动的控制
	/*$(document).scroll(function(){
	 	var h = $(".headline_detail").height();//获取右边头条详情模块的高度		
        var top = $(document).scrollTop();//获取滚动条距离顶部的位置
        var bottom = $(document).height()-$(window).height()-278;//获取，滚动到出现底部栏时的位置
        
        if(300 <= top <= bottom){        	
        	$(".headline_detail").addClass("headline_detail_fixed");
        }
        //如果头条详情模块的高度大于，且，滚动条滚动到出现底部栏时，就不要固定了
      	if(h > 788 && top > bottom){     	
     		$(".headline_detail").removeClass("headline_detail_fixed");
      	} 
      	if(top < 300){
      		$(".headline_detail").removeClass("headline_detail_fixed");
      	}  	
   }); */ 
   
  //滚动条到底部时就加载剩下数据
  $(function(){	
		$(window).scroll(function(){
			console.log($(document).scrollTop())	
			console.log($(document).height() - $(window).height())		
			if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
				alert();
			}
		})
	})
</script>

</html>
