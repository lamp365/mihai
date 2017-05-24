<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue"> 银行信息录入 </h3>
    <div class="wrap jj">
        <div class="well form-search">
            <div class="search_type cc mb10">
            <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return checkInfo();">
                <div class="mb10">
                        <br/>
                        <br/>
                        <label class="col-sm-2 control-label no-padding-left" for="input-search">银行：</label>
                        <select name="bank" id="">
                            <option value="0">请选择银行</option>
                            <?php
                                foreach($select_bank as $name) {
                                    $selected ='';
                                    if($name == $bank['bank'])  $selected = "selected";
                                    echo "<option value='{$name}' {$selected}>{$name}</option>";
                                }
                            ?>
                        </select>
                        <br><br>
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-left" for="input-search">图标(46*46)：</label>
                            <div class="col-sm-9">
                                <?php  if(!empty($bank['card_icon'])) { ?>
                                <div class="fileupload-preview" style="width: 60px; height: 60px;">

                                    <img src="<?php  echo $bank['card_icon'];?>" style="width: 60px;height: 60px;" onerror="$(this).remove();">

                                </div>
                                <br/>
                                <?php  } ?>

                                <input name="card_icon" id="card_icon" type="file" />
                            </div>

                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-left" for="input-search">背景图：</label>
                            <div class="col-sm-9">
                                <?php  if(!empty($bank['card_bg'])) { ?>
                                    <div class="fileupload-preview" style="max-width: 360px; height: 60px;">

                                        <img src="<?php  echo $bank['card_bg'];?>" style="max-width: 360px; height: 60px;" onerror="$(this).remove();">

                                    </div>
                                    <br/>
                                <?php  } ?>

                                <input name="card_bg" id="card_bg" type="file" />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2  control-label no-padding-left" >背景颜色：</div>
                            <div class="col-sm-9">
                                <input name="bg_color" class="color" type="text"  value="<?php echo $bank['bg_color']?>">
                            </div>
                        </div>

                        <br/>
                        <br/>
                        <div class="form-group">
                            <div class="col-sm-2" ></div>
                            <div class="col-sm-9">
                                <input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
                            </div>
                        </div>
                </div>
            </form>
            </div>
        </div>
    </div>
<?php  include page('footer');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/jscolor/jscolor.js"></script>
    <script>
        function checkInfo(){
            if($("select[name='bank']").val() == 0){
                alert('请选择银行!');
                return false;
            }
            if(S("#card_icon").val() == ''){
                alert('请上传图标!');
                return false;
            }
            if(S("#card_bg").val() == ''){
                alert('请上传背景图!');
                return false;
            }
            if(S("input[name='bg_color']").val() == ''){
                alert('请选择色值！');
                return false;
            }
            return true;
        }
    </script>