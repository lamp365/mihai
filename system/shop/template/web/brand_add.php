<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">品牌</h3>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
            <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" >
                <div class="mb10">
                        <br>
                        <label class="col-sm-2 control-label no-padding-left">品牌：</label>
                        <input name="u_brand" type="text" id="input-search" value="<?php echo $this_brand['brand']?>">
                        <br><br>
                        <label class="col-sm-2 control-label no-padding-left">国家：</label>
                        <select  style="margin-right:15px;" id="pcate" name="country"  autocomplete="off">
                        <?php if (!$isEdit) { ?>
                            <option value="nil">请选择</option>
                            <?php foreach ($country as $c) { ?>
                                <option value="<?php echo $c['id'] ?>"><?php echo $c['name'] ?></option>
                            <?php } ?>
                        <?php }else{ ?>
                            <?php foreach ($country as $c) { ?>
                                <?php if ($this_brand['country_id'] == $c['id']) { ?>
                                    <option value="<?php echo $c['id'] ?>"><?php echo $c['name'] ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        </select>
                        <br><br>
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-left" for="input-search">品牌图标(300*300)：</label>
                            <div class="col-sm-2">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-preview thumbnail" style="width: 300px; height: 300px;">
                            <?php  if(!empty($this_brand['icon'])) { ?>
                                <img src="<?php  echo $this_brand['icon'];?>" alt="" onerror="$(this).remove();">
                            <?php  } ?>
                            </div>
                            <div>
                            <input name="thumb" id="thumb" type="file" />
                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                            </div>
                        </div></div>
					  </div>
                      <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-left" for="input-search">品牌宣传图(300*300)：</label>
                            <div class="col-sm-2">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-preview thumbnail" style="width: 300px; height: 300px;">
                            <?php  if(!empty($this_brand['brand_public'])) { ?>
                                <img src="<?php  echo $this_brand['brand_public'];?>" alt="" onerror="$(this).remove();">
                            <?php  } ?>
                            </div>
                            <div>
                            <input name="brand_public" id="brand_public" type="file" />
                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                            </div>
                        </div></div>
					  </div>
					  <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-left" for="input-search">品牌广告图(750*350)：</label>
                            <div class="col-sm-2">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-preview thumbnail" style="width: 750px; height: 350px;">
                            <?php  if(!empty($this_brand['brand_ad'])) { ?>
                                <img src="<?php  echo $this_brand['brand_ad'];?>" alt="" onerror="$(this).remove();">
                            <?php  } ?>
                            </div>
                            <div>
                            <input name="brand_ad" id="brand_ad" type="file" />
                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                            </div>
                        </div></div>
					  </div>






						 <div class="form-group">
						    <label class="col-sm-2 control-label no-padding-left" > 属性 </label>
                            <div class="col-sm-9">
                                <input type="checkbox" name="recommend" <?php if ( $this_brand['recommend'] == 1 ){ echo 'checked'; } ?>/>推荐
								<input  name="isindex"  <?php if ( $this_brand['isindex'] == 1 ){ echo 'checked'; } ?> type="checkbox">首页显示
                            </div>
                        </div>
                       	<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >简单描述：</label>
										<div class="col-sm-9">
				                            <textarea  id="description" name="description"  style="width:100%;height:100px;"><?php  echo $this_brand['description'];?></textarea>           
										</div>
	        	            </div>
		
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >详细描述：<br/><span style="font-size:12px">(建议图片宽不超过640px)</span></label>

										<div class="col-sm-9">
                                               <textarea  id="container" name="content" style="width:100%;height:200px;" ><?php  echo $this_brand['content'];?></textarea>
										</div>
		             </div>


                        <div class="form-group">
                            <div class="col-sm-9">
                                <label class="col-sm-2 control-label no-padding-left" ></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <button type="submit" class="btn btn-primary span2" name="submit" value="submit"><i class="icon-edit"></i> 提 交 </button>
                            </div>
                        </div>
                </div>
            </form>
            </div>
        </div>
    </div>
<?php  include page('footer');?>