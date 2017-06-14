<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">新增热搜词</h3>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
            <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" >
                <div class="mb10">
                        <!-- <a href="{:U('Warnadmin/index', array('type' => 1)) }" class="btn btn-primary">
                            查看已读
                        </a>
                        <a href="{:U('Warnadmin/index', array('type' => 0)) }" class="btn btn-primary">
                            查看未读
                        </a>
                        <a href="{:U('Warnadmin/index', array('type' => 2)) }" class="btn btn-primary">
                            查看所有
                        </a> -->
                        
                        <br>
                        <label class="col-sm-2 control-label no-padding-left">分类：</label>
                        <select  style="margin-right:15px;" id="pcate" name="classify"  autocomplete="off">
                            <?php  if(!$isEdit) { ?>
                                <option value="nil">请选择分类</option>
                            <?php } ?>
                            <?php $c_search = get_classify_hot('0'); ?>
                            <?php if(empty($c_search) AND !$isEdit) { ?>
                                <option value="0">搜索栏</option>
                            <?php }elseif($this_hot['classify_id'] == '0'){ ?>
                                <option value="0" selected="selected">搜索栏</option>
                            <?php } ?>
                            <?php  if(is_array($category)) { foreach($category as $row) { ?>
                            <?php $c_row = get_classify_hot($row[id]); ?>
                            <?php  if($row['parentid'] == 0 AND empty($c_row) AND !$isEdit) { ?>
                            <option value="<?php  echo $row['id'];?>" <?php if($row['id'] == $this_hot['classify_id']) { ?> selected="selected"<?php } ?>><?php  echo $row['name'];?></option>
                            <?php  }elseif ($isEdit AND $row['id'] == $this_hot['classify_id']) {?>
                                <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $this_hot['classify_id']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                            <?php } ?>
                            <?php  } } ?>
                        </select>
                        <br><br>
                        <div class="form-group">
                            &nbsp;<label class="col-sm-2 control-label no-padding-left" >热搜词：<br>(每个词之间以分号;隔开)</label>
                            <textarea style="height:150px;"  id="description" name="description" cols="50"><?php if (empty($this_hot)) {
                                    echo '每个词之间以分号(;)隔开';
                                }else{
                                    echo $this_hot['hottopic'];
                                } ?></textarea>
                        </div>
                        <br><br>
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

<script type="text/javascript">
    <?php if (empty($this_hot)) { ?>
        var description = document.getElementById('description');
        description.onfocus = function(){
            if(this.innerHTML == '每个词之间以分号(;)隔开'){this.value = ''}
        };
         
        description.onblur = function(){
            if(this.value == ''){
                this.innerHTML = '每个词之间以分号(;)隔开'   
            }   
        };
    <?php } ?>
</script>
<?php  include page('footer');?>