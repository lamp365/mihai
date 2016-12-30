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
	<style>
		#note-detail .modal-dialog{
			width: 700px;
		}
	</style>
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
						<?php if (is_array($article_list)){ foreach ($article_list as $val){ ?>
						<!--显示10条-->
						<li>
							<!--头条图片-->
							<div>
								<?php if(empty($val['pic'])){  ?>
									<a href="<?php echo mobile_url('article_list',array('op'=>'headline','id'=>$val['headline_id']));?>">
										<p><?php echo date('d',$val['createtime']);?></p>
										<p style="margin-top: 0"><?php echo date('m',$val['createtime']);?>月</p>
									</a>
								<?php }else{  ?>
									<a href="<?php echo mobile_url('article_list',array('op'=>'headline','id'=>$val['headline_id']));?>">
										<?php $pic = explode(';',$val['pic']); ?>
										<img src="<?php echo download_pic($pic[0],200); ?>"/>
									</a>
								<?php }  ?>

							</div>
							<!--头条标题-->
							<a href="<?php echo mobile_url('article_list',array('op'=>'headline','id'=>$val['headline_id']));?>">
								<p class="title"><?php echo $val['title']; ?></p>
							</a>
						</li>
						<?php }} ?>
					</ul>
				</div>
				<!--右边的头条详情-->
				<div class="headline_detail">						
					<!--轮播图-->
					<!--如果没有上传图片，那这一块就没有,直接显示文章-->
					<?php if(!empty($one_article['pic'])){  ?>
					<div class="swiper-container">
			    		<div class="swiper-wrapper">
			    			<!--一个slide是一张图片，最多出5张-->
					        <div class="swiper-slide">
								<?php $pic = explode(';',$one_article['pic']); foreach($pic as $one) { ?>
					        	<img src="<?php echo $one; ?>"/>
								<?php } ?>
					        </div>					        
						</div>						
    					<div class="swiper-pagination"></div>
    				</div>
					<?php } ?>
    				<!--头条标题-->
    				<p>
						<?php echo $one_article['title']; ?>
    				</p>
    				<div class="content">
    					<div class="info">
    						<!--头像，用户名-->
    						<div class="men">
								<?php if(empty($one_article['avatar'])){ ?>
    							<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/userface.jpg" />
								<?php }else{ ?>
									<img src="<?php echo $one_article['avatar']; ?>" />
								<?php } ?>
    							<span><?php echo $one_article['nickname']; ?></span>
    						</div>
    						<!--发表时间-->
    						<div class="time">
    							<span><?php echo date('Y/m/d  H:i',$one_article['createtime']);?></span>
    						</div>
    					</div>
    					<!--头条内容-->
    					<div class="detail">
							<?php echo $one_article['description']; ?>
    					</div>
    					<!--评论-->
    					<div class="comment">
    						<!--点击img到app下载页-->
    						<a href="<?php echo mobile_url('appdown',array('name'=>'shopwap')); ?>" target="_blank">
    							<img class="todownapp" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/hl_03.jpg"/>
    						</a>
    						<span>评论<?php echo $comment_num;?></span>
    						<!--评论列表-->
    						<ul>
    							<!--一个li是一条评论-->
								<?php if(!empty($article_comment)){ foreach($article_comment as $comment){ ?>
    							<li>
    								<div class="meninfo">
    									<!--头像-->
										<?php if(empty($comment['avatar'])){ ?>
											<img src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/userface.jpg" />
										<?php }else{ ?>
											<img src="<?php echo $comment['avatar']; ?>" />
										<?php } ?>
    									<!--用户名-->
    									<span><?php echo $comment['nickname']; ?></span>
    									<!--评论时间-->
    									<span class="commenttime">
    										<?php echo date("Y/m/d  H:i",$comment['createtime']); ?>
    									</span>
    								</div>
    								<!--评论内容-->
    								<div class="cdetail">
										<?php echo $comment['comment']; ?>
    								</div>
    							</li>
								<?php }} ?>
    						</ul>
    					</div>    					
    				</div>    					
				</div>
			</div>			
		</div>
		<!--用来存当前page-->
		<input type="hidden" value="2" id="page"/>

	<script>
		//	头条图片滚动控制
		var mySwiper = new Swiper ('.swiper-container', {
			direction: 'horizontal',
			loop: true,
			autoplay:2000,
			pagination: '.swiper-pagination'
		})


		//觅海头条，滚动条到底部时就加载剩下数据
		$(function(){
			$(".headline_list").scroll(function(){				
				var index = 1 ; //默认开关打开
				if ( $(this)[0].scrollTop + $(".headline_list").height() >= $(this)[0].scrollHeight ) {					
					if(index == 1){
						index = 0; //关闭开关
						var page = $("#page").val(); //第一次传的是2
						var url = "<?php echo mobile_url('article_list',array('op'=>'headline'));?>"
						$.post(url, {'page' : page,'nextpage' : 'ajax'}, function(s){
							if(s.errno != 200){
								//如果没有数据								
							}else{
								$("#page").val(++page);
								var art_data = s.message;
								for(var i = 0;i < art_data.length;i++){
									//循环拼接 html下一页数据
									Append(art_data[i]);
								}
								index = 1;  //加载完后重新打开开关
							}
						}, 'json');
					}

				}
			})
		})

		function Append(art_data){			
			var url = "<?php echo mobile_url('article_list',array('op'=>'headline'));?>";
			 url = url+"&id="+art_data.headline_id;
			var piclist = art_data.pic;
			//时间戳是 art_data.createtime
			var day = StringtotimeD(art_data.createtime);
			var month = StringtotimeM(art_data.createtime);
			var picurl = '<p>'+ day +'</p><p style="margin-top: 0">'+ month +'</p>';
			if(piclist.length > 0){
				//如果有图片换成图片
				var perpic = piclist.split(";"); //字符串截取，成为数组
				picurl     = '<img src="'+ perpic[0] +'" />';
			}
			var html = '<li>'+
							'<div><a href="'+ url +'">'+ picurl +'</a></div>'+
							'<a href="'+ url +'"><p class="title">'+ art_data.title +'</p></a>'+
					  '</li>';
			$(".headline_list ul").append(html);

		}
	//时间戳转换为天	
	function StringtotimeD(time){
		time  = time * 1000;  
	    var datetime = new Date();			    
	    datetime.setTime(time);      			   
	    var day = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate(); 	      
	    return day;  
	}  
	//时间戳转换为月
	function StringtotimeM(time){
		time  = time * 1000;   
	    var datetime = new Date();			    
	    datetime.setTime(time);			      
	    var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1; 			   	       
	    return month+"月";  
	}  
	</script>


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
				<?php if (is_array($article_list)){ $j = 0; $num = count($article_list); foreach ($article_list as $key=>$val){  $j++; ?>
					<?php if($j == 8 || ($num<=8 && $j == $num)){ ?>
						<!--最后一个笔记-->
						<li>
							<!--笔记图片-->
							<a href="javascript:;" data-toggle="modal" data-target="#note-detail" class="notepic">
								<?php $pic = explode(';',$val['pic']); ?>
								<img src="<?php echo download_pic($pic[0],300); ?>"/>
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
					<?php }else{ ?>
						<li  class="show_this_note_modal" data-id="<?php echo $val['note_id']; ?>">
							<!--笔记图片-->
							<a href="javascript:;" class="notepic">
								<?php $pic = explode(';',$val['pic']); ?>
								<img src="<?php echo download_pic($pic[0],300); ?>"/>
							</a>
							<!--图片下面的东西-->
							<div class="noteinfo">
								<!--笔记详情-->
								<a href="javascript:;">
									<p>
										<?php echo msubstr($val['description'],0,120); ?>
									</p>
								</a>
								<!--头像，用户名-->
								<div>
									<?php if(empty($val['avatar'])){ ?>
										<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />
									<?php }else{ ?>
										<img src="<?php echo $val['avatar']; ?>" />
									<?php } ?>
									<h3><?php echo $val['nickname']; ?></h3>
								</div>
							</div>
						</li>
					<?php } ?>

				<?php }} ?>

    		</ul>     		
    	</div>
    	<!--点击笔记弹出的-->
			<div class="modal fade" taria-hidden="true" id="note-detail">
				<div class="modal-dialog">
					<div class="modal-content">	
						<div class="modal-header">
							<!--<img src="<?php /*echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; */?>/recouse/images/f5.png" />
							<div class="info">
								<p>用户名</p>
								<p>2016/12/21 09:59</p>
							</div>
							<img class="comment" src="<?php /*echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; */?>/recouse/images/comment.png">
							<span>20</span>-->
						</div>	
											  	
					  	<div class="modal-body">
							<!--笔记详情-->
							<!--内容多时出现滚动条-->
							<p class="note_content">
								<!--疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看
	    						见过卡结果疯啦司空见惯卡怪啊睡觉噶空间更舒服案件管理卡就噶时间啊看见过卡结果-->

							</p>
							<!--笔记图片,最少一张，最多五张,全部排列下去-->
							<div class="note_pic">

							</div>
							<!--评论-->
							<a href="<?php echo mobile_url('appdown',array('name'=>'shopwap')); ?>" target="_blank">
    							<img class="todownapp" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/hl_03.jpg">
    						</a>	
    						<span style="margin-top: 20px;display: block;">评论 <span class="note_comment"></span></span>
    						<!--评论列表-->
    						<ul class="note_comment_list">
    							<!--一个li是一条评论-->
    							<!--<li>
    								<div class="meninfo">
    									<img src="<?php /*echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; */?>/recouse/images/tx.jpg" />
    									<span class="">爱炫富的小哥</span>
    									<span class="commenttime">
    										2016/12/20 11:53
    									</span>
    								</div>
    								<div class="cdetail">
    									这个多少钱？
    								</div>
    							</li>   		-->
    						</ul>					
						</div>
					</div>
				</div>
			</div>   		
	</div>
	<!--用来存当前page-->
	<input type="hidden" value="2" id="page"/>

	<script>
		$(document).delegate('.show_this_note_modal','click',function(){
			var id  = $(this).data('id');
			var url = "<?php echo mobile_url('article',array('op'=>'ajax_note'));?>";
			url = url + "&id="+ id;
			$.getJSON(url,function(data){
				if(data.errno == '200'){
					var obj = data.message;
					if(obj.avatar == ''){
						var face = "<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/userface.png"
					}else{
						var face = obj.avatar;
					}
					var comment_logo = "<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/comment.png";
					$("#note-detail").modal('show');
					var html_head = '<img src="'+ face +'" />'+
									'<div class="info">'+
										'<p>'+ obj.nickname +'</p>'+
										'<p>'+ Stringtotime(obj.createtime) +'</p>'+
									'</div>'+
									'<img class="comment" src="'+ comment_logo +'" />'+
									'<span>'+ obj.comment_num +'</span>';
					var piclist = obj.pic;
					var perpic = piclist.split(";"); //字符串截取，成为数组
					var picurl = "";
					for(var j=0;j<perpic.length;j++){
						if(perpic[j] != ""){
							picurl += '<img src="'+perpic[j]+'"/>';
						}
					}
					$("#note-detail .modal-header").html(html_head);
					$("#note-detail .note_content").html(obj.description);
					$("#note-detail .note_pic").html(picurl);
					$("#note-detail .note_comment").html(obj.comment_num);

					var comment_list = obj.article_comment;
					var comment_list_html = '';
					for(var i= 0; i<comment_list.length; i++){
						var item = comment_list[i];
						if(item.avatar == ''){
							var face = "<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/userface.png"
						}else{
							var face = item.avatar;
						}
						comment_list_html =  comment_list_html +'<li>'+
												'<div class="meninfo">'+
													'<img src="'+  face +'" />'+
													'<span class="">'+ item.nickname +'</span>'+
													'<span class="commenttime">'+ Stringtotime(item.createtime) +'</span>'+
												'</div>'+
												'<div class="cdetail">'+ item.comment +'</div>'+
											'</li> ';
					}
					$("#note-detail .note_comment_list").html(comment_list_html);
				}
			},'json')
		})

		var index = 1; //默认开关状态是打开
		//点击查看更多笔记，直接在当前页出现隐藏的笔记，点击一次出三行
		$(".lookmore").click(function(){
			if( index == 1){
				index = 0; //关闭开关
				var page = $("#page").val(); //第一次传的是2
				var url = "<?php echo mobile_url('article_list',array('op'=>'note'));?>"
				$.post(url, {'page' : page,'nextpage' : 'ajax'}, function(s){
					if(s.errno != 200){
						//如果没有数据

					}else{
						$("#page").val(++page);
						var art_data = s.message;
						for(var i = 0;i < art_data.length;i++){
							//循环拼接 html下一页数据
							LoadHtml(art_data[i]);
						}
						index = 1;  //加载完后重新打开开关
					}
				}, 'json');
			}
		})
		
		function LoadHtml(art_data){
			var piclist = art_data.pic;
			var picurl = "";
			if(piclist.length > 0){
				var perpic = piclist.split(";"); //字符串截取，成为数组
				picurl     = perpic[0];
			}
			if(art_data.avatar == ''){
				var face = '<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/userface.png';
			}else{
				var face = art_data.avatar;
			}
			var note = '<li class="show_this_note_modal" data-id="'+ art_data.note_id +'">'+
							'<a href="javascript:;"  class="notepic">'+
								'<img src="'+ picurl +'"/>'+
							'</a>'+
							'<div class="noteinfo">'+
								'<a href="javascript:;">'+
									'<p>'+ art_data.description +'</p>'+
								'</a>'+
								'<div>'+
									'<img src="'+ face +'" />'+
									'<h3>'+ art_data.nickname +'</h3>'+
								'</div>'+
							'</div>'+
						'</li>';
			$(".viewport .notelist ul li:last-child").before(note);
		}

		var id = "<?php echo $_GP['id'];?>";
		if(id != ''){
			$(".show_this_note_modal").each(function(){
				var this_id = $(this).data('id');
				if(this_id == id){
					$(this).click();
				}
			})
		}
		//将后台返回的时间戳格式化为时间格式
		function Stringtotime(time){
			time = time*1000;
			var datetime = new Date();
			datetime.setTime(time);
			var year = datetime.getFullYear();
			var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;
			var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();
			var hour = datetime.getHours() < 10 ? "0" + datetime.getHours() : datetime.getHours();
			var minute = datetime.getMinutes() < 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();
			var sec = datetime.getSeconds() < 10 ? "0" + datetime.getSeconds() :  datetime.getSeconds(); 
			return year + "/" + month + "/" + date + " &nbsp " + hour + ":" + minute + ":" + sec + "";
		}
	</script>
	<?php } ?>


	<?php  include themePage('footer'); ?>		      	

</body>
<script>

</script>

</html>
