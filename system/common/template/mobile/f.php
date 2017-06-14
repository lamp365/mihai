<?php 
$article_foot = getArticle(5,1);
?>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT; ?>addons/common/fontawesome3/css/font-awesome.min.css" />	
<div class="public-footer">
	<div class="article clearfix">  
		<div class="article-left">
			占个位置而已
		</div>
		<div class="article-right">  
	    <?php if (is_array($article_foot)){ foreach ($article_foot as $val){ ?>
			<dl>
	            <dt><?php echo $val['name']; ?></dt>
			    <?php if (is_array($val['article'])){ foreach ($val['article'] as $value){ ?>
	                 <dd><a target="_blank" href="<?php  echo mobile_url('article',array('name'=>'addon8','id'=>$value['id']))?>" ><?php echo $value['title']; ?></a></dd>
				<?php }} ?> 
			</dl>
		<?php }}?>
		</div>
	</div>
	<div style="margin-top:15px;">© 2004-2016  All Rights Reserved. 福建觅海 版权所有</div>
	<div style="margin-top:5px;"><!--@php echo $cfg['shop_icp']; @--></div>
</div>
<div id="alterModal" class="alertModalBox"></div>
</body>
</html>