<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">品牌列表</h3>
    <div class="wrap jj">
        <div class="well form-search">
            
        <div class="table_list">
            <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                <thead id='table_head'>
                    <tr>
                        <th class="text-center" >编号</th>
						<th class="text-center" >品牌图片</th>
                        <th class="text-center" >品牌名</th>
                        <th class="text-center" >国家</th>
						<th class="text-center">宣传图</th>
						<th class="text-center">广告图</th>
						<th class="text-center" >批发</th>
						<th class="text-center" >代发</th>
                        <th class="text-center" >属性</th>
                        <th class="text-center" >操作</th>
                    </tr>
                </thead>
                <tbody id='table_body'>
                    <?php foreach ($brand as $b) { ?>
                        <tr>
                            <td style="text-align:center;"><?php  echo $b['id'];?></td>
							<td style="text-align:center;"><img src="<?php  echo $b['icon'];?>" height="50" ></td>
                            <td style="text-align:center;"><?php  echo $b['brand'];?></td>
                            <td style="text-align:center;"><?php if (!empty($b['country_img'])){ ?><img src="<?php  echo $b['country_img'];?>" height="46" ><?php } ?></td>
							<td style="text-align:center;"><?php if (!empty($b['brand_public'])){ ?><img src="<?php  echo $b['brand_public'];?>" height="50" ><?php } ?></td>
							<td style="text-align:center;"><?php if (!empty($b['brand_ad'])){ ?><img src="<?php  echo $b['brand_ad'];?>" height="50" ><?php } ?></td>
							<td style="text-align:center;"><span brand_id="<?php  echo $b['id'];?>" brand_type="pifa" class="glyphicon <?php echo $b['pifa']==1?'glyphicon-ok':'glyphicon-remove'; ?>"></span></td>
							<td style="text-align:center;"><span brand_id="<?php  echo $b['id'];?>" brand_type="daifa" class="glyphicon <?php echo $b['daifa']==1?'glyphicon-ok':'glyphicon-remove'; ?>"></span></td>
							<td style="text-align:center;">  <?php  if($b['recommend']==1) { ?>
                                                <span class='label label-success'>推荐</span>
                                                 <?php  } ?><?php  if($b['isindex']==1) { ?>
                                                <span class='label label-success'>首页</span>
                                                <?php  } ?></td>
                            <td style="text-align:center;">
                                <?php if(isHasPowerToShow('shop','brand','edit','edit')){ ?>
                                    <a class="btn btn-xs btn-info" href="<?php  echo web_url('brand', array('op'=>'edit','id' => $b['id']))?>"><i class="icon-edit"></i>修改</a>&nbsp;&nbsp;
                                <?php } ?>
                                <?php if(isHasPowerToShow('shop','brand','delete','delete')){ ?>
                                    <a class="btn btn-xs btn-danger" href="<?php  echo web_url('brand', array('op'=>'delete','id' => $b['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>删除</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){
            $(".glyphicon").on("click",function(){
                var $this = $(this);
                var brand_type = $this.attr("brand_type");
                var brand_id = $this.attr("brand_id");
                $.post("",{ajax:brand_type,id:brand_id},function(data){
                    if( data.errno == 200 ){
                        if( data.message.daifa == 1){
                            $this.removeClass("glyphicon-remove");
                            $this.addClass("glyphicon-ok");
                        }
                        if( data.message.daifa == 0){
                            $this.removeClass("glyphicon-ok");
                            $this.addClass("glyphicon-remove");
                        }
                        if( data.message.pifa == 1){
                            $this.removeClass("glyphicon-remove");
                            $this.addClass("glyphicon-ok");
                        }
                        if( data.message.pifa == 0){
                            $this.removeClass("glyphicon-ok");
                            $this.addClass("glyphicon-remove");
                        }
                    }
                },"json");
            })
        });
    </script>
<?php  include page('footer');?>