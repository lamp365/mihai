<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">品牌列表 &nbsp;&nbsp; <a class="btn btn-primary  btn-sm" href="<?php echo web_url('brand',array('op'=>'add','name'=>'shop')); ?>">添加品牌</a></h3>&nbsp;

    <form action="" class="form-horizontal" method="post">
        <table class="table table-striped table-bordered table-hover">
            <tbody>
            <tr>
                <td style="background-color: #fff">
                    <li style="float:left;list-style-type:none;">
                        <select name="p1" id="getShopCategory_p1" class="get_category" onchange="getShop_sonCategroy(this,1)" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
                            <option value="">请选择分类</option>
                            <?php foreach($all_category as $item) {
                                if($item['id'] == $_GP['p1']){
                                    $sel = "selected";
                                }else{
                                    $sel = '';
                                }
                                echo "<option value='{$item['id']}' {$sel}>{$item['name']}</option>";
                            }
                            ?>
                        </select>
                        <select name="p2" id="getShopCategory_p2" class="get_category" onchange=""  style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
                            <option value="">请选择分类</option>
                            <?php foreach($first_son as $item2) {
                                if($item2['id'] == $_GP['p2']){
                                    $sel = "selected";
                                }else{
                                    $sel = '';
                                }
                                echo "<option value='{$item2['id']}' {$sel}>{$item2['name']}</option>";
                            }
                            ?>
                        </select>


                    </li>

                    <li style="list-style-type:none;">
                        <button class="btn btn-primary  btn-sm" style="margin-right:10px;"><i class="icon-search icon-large"></i> 搜索</button>
                    </li>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
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
    <?php echo $pager;?>
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