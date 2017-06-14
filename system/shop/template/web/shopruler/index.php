<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/font-awesome/4.6.0/css/font-awesome.min.css">
<style type="text/css">
    .fa-hover{
        cursor: pointer;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        margin: 5px 0;
    }
    .fa-checkeds{
        color: #fff;
        background: #1d9d74;
    }
</style>
<br/>
    <ul class="nav nav-tabs" >
        <li style="width:7%" <?php  if(empty($_GP['op'])) { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('shopruler')?>">权限规则</a></li>
        <li style="width:7%" <?php  if($_GP['op'] == 'group') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('shopruler',  array('op' => 'group'))?>">权限分组</a></li>
    </ul>

    <h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">菜单节点</h3><span data-url="<?php echo web_url('shopruler',array('op'=>'addmenu'))?>" onclick="add_menu(this)" class="btn btn-primary">添加菜单</span> &nbsp;&nbsp;<a href="<?php echo web_url('shopruler',array('op'=>'cleanMenu'))?>" class="btn btn-danger">清除节点缓存</a>
    <table class="table table-striped table-bordered table-hover" style="margin-top: 15px;;">
        <thead >
        <tr>
            <th style="text-align:center;min-width:20px;"><input type="checkbox" class="btn btn-xs btn-info choose_all"><span class="box_zi">全选</span></th>
            <th style="text-align:center;min-width:20px;">ID</th>
            <th style="text-align:center; min-width:130px;">菜单名</th>
            <th style="text-align:center; min-width:13px;">规则</th>
            <th style="text-align:center; min-width:30px;">排序</th>
            <th style="text-align:center; min-width:130px;">操作</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach($menudata as  $row_arr) {   $arr = $row_arr['main']; ?>

                <tr>
                    <td colspan="4"><strong style="margin-left:10px;"><?php echo $arr['rule_name'];?></strong> &nbsp;&nbsp;
                        <span class="btn btn-xs btn-info" data-url="<?php echo  web_url('shopruler',array('op'=>'add_sonmenu','pid'=>$arr['rule_id']));?>" onclick="add_menu(this)">添加菜单</span>
                    </td>
                    <td style="text-align: center">
                        <input style="text-align: center;width: 60px;" type="number" data-rule_id="<?php  echo $arr['rule_id'];?>" min="<?php echo $arr['rule_id']+15;?>" max="<?php echo $arr['rule_id']+30?>" value="<?php echo $arr['sort'];?>" name="sort">
                    </td>
                    <td style="text-align: center">
                        <span class="btn btn-xs btn-info"  data-url="<?php  echo web_url('shopruler', array('op'=>'addmenu','rule_id' => $arr['rule_id']))?>" onclick="add_menu(this)" ><i class="icon-edit"></i>编辑菜单</span>&nbsp;&nbsp;
                        <span class="btn btn-xs btn-success" onclick="setIcon()">设置图标</span>
                    </td>
                </tr>

                <?php foreach($row_arr['child'] as $row){  $item = $row['main']; ?>

                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="child_box" name="id[]" value="<?php echo $item['rule_id'];?>" ></td>
                        <td style="text-align:center;"><?php echo $item['rule_id'];?></td>
                        <td style="text-align:center;"><?php echo $item['rule_name'];?></td>
                        <td style="text-align:center;"><?php echo rtrim($item['url'],'/');?></td>
                        <td style="text-align:center;">
                            <input style="text-align: center;width: 60px;" type="number" data-rule_id="<?php  echo $item['rule_id'];?>" min="<?php echo $arr['rule_id']*10;?>" max="<?php echo $arr['rule_id']*10+15;?>" value="<?php echo $item['sort'];?>" name="sort">
                        </td>
                        <td style="text-align:center;">
                            <a class="btn btn-xs btn-info"  href="<?php  echo web_url('shopruler', array('op'=>'sonmenuList','rule_id' => $item['rule_id']))?>"><i class="icon-edit"></i>子菜单</a>&nbsp;&nbsp;
                            <span class="btn btn-xs btn-info"  data-url="<?php  echo web_url('shopruler', array('op'=>'add_sonmenu','rule_id' => $item['rule_id']))?>" onclick="add_menu(this)"><i class="icon-edit"></i>编辑菜单</span>&nbsp;&nbsp;
                            <span class="btn btn-xs btn-success"  onclick="setIcon()">设置图标</span>
                            <a class="btn btn-xs btn-danger" href="<?php  echo web_url('shopruler', array('op'=>'delmenu','rule_id' => $item['rule_id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
                        </td>
                    </tr>
                <?php     }  } ?>
        </tbody>
    </table>
<!-- 设置图标弹出框 -->
<div class="set-icon modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">设置图标</h4>
      </div>
      <div class="modal-body">
           <div class="row fontawesome-icon-list">

      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-adjust" aria-hidden="true"></i> <span class="sr-only">Example of </span>adjust</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-american-sign-language-interpreting" aria-hidden="true"></i> <span class="sr-only">Example of </span>american-sign-language-interpreting</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-anchor" aria-hidden="true"></i> <span class="sr-only">Example of </span>anchor</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-archive" aria-hidden="true"></i> <span class="sr-only">Example of </span>archive</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-area-chart" aria-hidden="true"></i> <span class="sr-only">Example of </span>area-chart</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-arrows" aria-hidden="true"></i> <span class="sr-only">Example of </span>arrows</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-arrows-h" aria-hidden="true"></i> <span class="sr-only">Example of </span>arrows-h</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-arrows-v" aria-hidden="true"></i> <span class="sr-only">Example of </span>arrows-v</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-asl-interpreting" aria-hidden="true"></i> <span class="sr-only">Example of </span>asl-interpreting <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-assistive-listening-systems" aria-hidden="true"></i> <span class="sr-only">Example of </span>assistive-listening-systems</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-asterisk" aria-hidden="true"></i> <span class="sr-only">Example of </span>asterisk</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-at" aria-hidden="true"></i> <span class="sr-only">Example of </span>at</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-audio-description" aria-hidden="true"></i> <span class="sr-only">Example of </span>audio-description</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-automobile" aria-hidden="true"></i> <span class="sr-only">Example of </span>automobile <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-balance-scale" aria-hidden="true"></i> <span class="sr-only">Example of </span>balance-scale</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-ban" aria-hidden="true"></i> <span class="sr-only">Example of </span>ban</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bank" aria-hidden="true"></i> <span class="sr-only">Example of </span>bank <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bar-chart" aria-hidden="true"></i> <span class="sr-only">Example of </span>bar-chart</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bar-chart-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>bar-chart-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-barcode" aria-hidden="true"></i> <span class="sr-only">Example of </span>barcode</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bars" aria-hidden="true"></i> <span class="sr-only">Example of </span>bars</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-0" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-0 <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-1" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-1 <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-2" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-2 <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-3" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-3 <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-4" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-4 <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-empty" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-empty</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-full" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-full</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-half" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-half</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-quarter" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-quarter</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-battery-three-quarters" aria-hidden="true"></i> <span class="sr-only">Example of </span>battery-three-quarters</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bed" aria-hidden="true"></i> <span class="sr-only">Example of </span>bed</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-beer" aria-hidden="true"></i> <span class="sr-only">Example of </span>beer</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bell" aria-hidden="true"></i> <span class="sr-only">Example of </span>bell</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bell-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>bell-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bell-slash" aria-hidden="true"></i> <span class="sr-only">Example of </span>bell-slash</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bell-slash-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>bell-slash-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bicycle" aria-hidden="true"></i> <span class="sr-only">Example of </span>bicycle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-binoculars" aria-hidden="true"></i> <span class="sr-only">Example of </span>binoculars</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-birthday-cake" aria-hidden="true"></i> <span class="sr-only">Example of </span>birthday-cake</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-blind" aria-hidden="true"></i> <span class="sr-only">Example of </span>blind</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bluetooth" aria-hidden="true"></i> <span class="sr-only">Example of </span>bluetooth</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bluetooth-b" aria-hidden="true"></i> <span class="sr-only">Example of </span>bluetooth-b</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bolt" aria-hidden="true"></i> <span class="sr-only">Example of </span>bolt</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bomb" aria-hidden="true"></i> <span class="sr-only">Example of </span>bomb</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-book" aria-hidden="true"></i> <span class="sr-only">Example of </span>book</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bookmark" aria-hidden="true"></i> <span class="sr-only">Example of </span>bookmark</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bookmark-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>bookmark-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-braille" aria-hidden="true"></i> <span class="sr-only">Example of </span>braille</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-briefcase" aria-hidden="true"></i> <span class="sr-only">Example of </span>briefcase</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bug" aria-hidden="true"></i> <span class="sr-only">Example of </span>bug</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-building" aria-hidden="true"></i> <span class="sr-only">Example of </span>building</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-building-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>building-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bullhorn" aria-hidden="true"></i> <span class="sr-only">Example of </span>bullhorn</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bullseye" aria-hidden="true"></i> <span class="sr-only">Example of </span>bullseye</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-bus" aria-hidden="true"></i> <span class="sr-only">Example of </span>bus</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cab" aria-hidden="true"></i> <span class="sr-only">Example of </span>cab <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-calculator" aria-hidden="true"></i> <span class="sr-only">Example of </span>calculator</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-calendar" aria-hidden="true"></i> <span class="sr-only">Example of </span>calendar</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>calendar-check-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-calendar-minus-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>calendar-minus-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-calendar-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>calendar-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>calendar-plus-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-calendar-times-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>calendar-times-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-camera" aria-hidden="true"></i> <span class="sr-only">Example of </span>camera</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-camera-retro" aria-hidden="true"></i> <span class="sr-only">Example of </span>camera-retro</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-car" aria-hidden="true"></i> <span class="sr-only">Example of </span>car</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-caret-square-o-down" aria-hidden="true"></i> <span class="sr-only">Example of </span>caret-square-o-down</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-caret-square-o-left" aria-hidden="true"></i> <span class="sr-only">Example of </span>caret-square-o-left</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-caret-square-o-right" aria-hidden="true"></i> <span class="sr-only">Example of </span>caret-square-o-right</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-caret-square-o-up" aria-hidden="true"></i> <span class="sr-only">Example of </span>caret-square-o-up</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i> <span class="sr-only">Example of </span>cart-arrow-down</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cart-plus" aria-hidden="true"></i> <span class="sr-only">Example of </span>cart-plus</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cc" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-certificate" aria-hidden="true"></i> <span class="sr-only">Example of </span>certificate</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-check" aria-hidden="true"></i> <span class="sr-only">Example of </span>check</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-check-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>check-circle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-check-circle-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>check-circle-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-check-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>check-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-check-square-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>check-square-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-child" aria-hidden="true"></i> <span class="sr-only">Example of </span>child</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>circle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-circle-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>circle-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-circle-o-notch" aria-hidden="true"></i> <span class="sr-only">Example of </span>circle-o-notch</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-circle-thin" aria-hidden="true"></i> <span class="sr-only">Example of </span>circle-thin</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-clock-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>clock-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-clone" aria-hidden="true"></i> <span class="sr-only">Example of </span>clone</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-close" aria-hidden="true"></i> <span class="sr-only">Example of </span>close <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cloud" aria-hidden="true"></i> <span class="sr-only">Example of </span>cloud</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cloud-download" aria-hidden="true"></i> <span class="sr-only">Example of </span>cloud-download</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cloud-upload" aria-hidden="true"></i> <span class="sr-only">Example of </span>cloud-upload</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-code" aria-hidden="true"></i> <span class="sr-only">Example of </span>code</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-code-fork" aria-hidden="true"></i> <span class="sr-only">Example of </span>code-fork</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-coffee" aria-hidden="true"></i> <span class="sr-only">Example of </span>coffee</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cog" aria-hidden="true"></i> <span class="sr-only">Example of </span>cog</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cogs" aria-hidden="true"></i> <span class="sr-only">Example of </span>cogs</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-comment" aria-hidden="true"></i> <span class="sr-only">Example of </span>comment</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-comment-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>comment-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-commenting" aria-hidden="true"></i> <span class="sr-only">Example of </span>commenting</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-commenting-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>commenting-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-comments" aria-hidden="true"></i> <span class="sr-only">Example of </span>comments</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-comments-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>comments-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-compass" aria-hidden="true"></i> <span class="sr-only">Example of </span>compass</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-copyright" aria-hidden="true"></i> <span class="sr-only">Example of </span>copyright</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-creative-commons" aria-hidden="true"></i> <span class="sr-only">Example of </span>creative-commons</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-credit-card" aria-hidden="true"></i> <span class="sr-only">Example of </span>credit-card</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-credit-card-alt" aria-hidden="true"></i> <span class="sr-only">Example of </span>credit-card-alt</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-crop" aria-hidden="true"></i> <span class="sr-only">Example of </span>crop</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-crosshairs" aria-hidden="true"></i> <span class="sr-only">Example of </span>crosshairs</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cube" aria-hidden="true"></i> <span class="sr-only">Example of </span>cube</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cubes" aria-hidden="true"></i> <span class="sr-only">Example of </span>cubes</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-cutlery" aria-hidden="true"></i> <span class="sr-only">Example of </span>cutlery</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-dashboard" aria-hidden="true"></i> <span class="sr-only">Example of </span>dashboard <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-database" aria-hidden="true"></i> <span class="sr-only">Example of </span>database</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-deaf" aria-hidden="true"></i> <span class="sr-only">Example of </span>deaf</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-deafness" aria-hidden="true"></i> <span class="sr-only">Example of </span>deafness <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-desktop" aria-hidden="true"></i> <span class="sr-only">Example of </span>desktop</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-diamond" aria-hidden="true"></i> <span class="sr-only">Example of </span>diamond</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-dot-circle-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>dot-circle-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-download" aria-hidden="true"></i> <span class="sr-only">Example of </span>download</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-edit" aria-hidden="true"></i> <span class="sr-only">Example of </span>edit <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-ellipsis-h" aria-hidden="true"></i> <span class="sr-only">Example of </span>ellipsis-h</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-ellipsis-v" aria-hidden="true"></i> <span class="sr-only">Example of </span>ellipsis-v</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-envelope" aria-hidden="true"></i> <span class="sr-only">Example of </span>envelope</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-envelope-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>envelope-o</div>
    
      
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-envelope-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>envelope-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-eraser" aria-hidden="true"></i> <span class="sr-only">Example of </span>eraser</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-exchange" aria-hidden="true"></i> <span class="sr-only">Example of </span>exchange</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-exclamation" aria-hidden="true"></i> <span class="sr-only">Example of </span>exclamation</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>exclamation-circle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <span class="sr-only">Example of </span>exclamation-triangle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-external-link" aria-hidden="true"></i> <span class="sr-only">Example of </span>external-link</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-external-link-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>external-link-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-eye" aria-hidden="true"></i> <span class="sr-only">Example of </span>eye</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-eye-slash" aria-hidden="true"></i> <span class="sr-only">Example of </span>eye-slash</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-eyedropper" aria-hidden="true"></i> <span class="sr-only">Example of </span>eyedropper</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-fax" aria-hidden="true"></i> <span class="sr-only">Example of </span>fax</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-feed" aria-hidden="true"></i> <span class="sr-only">Example of </span>feed <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-female" aria-hidden="true"></i> <span class="sr-only">Example of </span>female</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-fighter-jet" aria-hidden="true"></i> <span class="sr-only">Example of </span>fighter-jet</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-archive-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-archive-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-audio-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-audio-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-code-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-code-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-excel-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-image-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-image-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-movie-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-movie-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-pdf-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-photo-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-photo-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-picture-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-picture-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-powerpoint-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-sound-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-sound-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-video-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-video-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-word-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-word-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-file-zip-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>file-zip-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-film" aria-hidden="true"></i> <span class="sr-only">Example of </span>film</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-filter" aria-hidden="true"></i> <span class="sr-only">Example of </span>filter</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-fire" aria-hidden="true"></i> <span class="sr-only">Example of </span>fire</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-fire-extinguisher" aria-hidden="true"></i> <span class="sr-only">Example of </span>fire-extinguisher</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-flag" aria-hidden="true"></i> <span class="sr-only">Example of </span>flag</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-flag-checkered" aria-hidden="true"></i> <span class="sr-only">Example of </span>flag-checkered</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-flag-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>flag-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-flash" aria-hidden="true"></i> <span class="sr-only">Example of </span>flash <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-flask" aria-hidden="true"></i> <span class="sr-only">Example of </span>flask</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-folder" aria-hidden="true"></i> <span class="sr-only">Example of </span>folder</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-folder-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>folder-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-folder-open" aria-hidden="true"></i> <span class="sr-only">Example of </span>folder-open</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-folder-open-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>folder-open-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-frown-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>frown-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-futbol-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>futbol-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-gamepad" aria-hidden="true"></i> <span class="sr-only">Example of </span>gamepad</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-gavel" aria-hidden="true"></i> <span class="sr-only">Example of </span>gavel</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-gear" aria-hidden="true"></i> <span class="sr-only">Example of </span>gear <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-gears" aria-hidden="true"></i> <span class="sr-only">Example of </span>gears <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-gift" aria-hidden="true"></i> <span class="sr-only">Example of </span>gift</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-glass" aria-hidden="true"></i> <span class="sr-only">Example of </span>glass</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-globe" aria-hidden="true"></i> <span class="sr-only">Example of </span>globe</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-graduation-cap" aria-hidden="true"></i> <span class="sr-only">Example of </span>graduation-cap</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-group" aria-hidden="true"></i> <span class="sr-only">Example of </span>group <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-grab-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-grab-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-lizard-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-lizard-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-paper-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-paper-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-peace-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-peace-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-pointer-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-pointer-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-rock-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-rock-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-scissors-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-scissors-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-spock-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-spock-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hand-stop-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hand-stop-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-handshake-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>handshake-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hard-of-hearing" aria-hidden="true"></i> <span class="sr-only">Example of </span>hard-of-hearing <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hashtag" aria-hidden="true"></i> <span class="sr-only">Example of </span>hashtag</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hdd-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hdd-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-headphones" aria-hidden="true"></i> <span class="sr-only">Example of </span>headphones</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-heart" aria-hidden="true"></i> <span class="sr-only">Example of </span>heart</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-heart-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>heart-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-heartbeat" aria-hidden="true"></i> <span class="sr-only">Example of </span>heartbeat</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-history" aria-hidden="true"></i> <span class="sr-only">Example of </span>history</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-home" aria-hidden="true"></i> <span class="sr-only">Example of </span>home</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hotel" aria-hidden="true"></i> <span class="sr-only">Example of </span>hotel <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hourglass" aria-hidden="true"></i> <span class="sr-only">Example of </span>hourglass</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hourglass-1" aria-hidden="true"></i> <span class="sr-only">Example of </span>hourglass-1 <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hourglass-2" aria-hidden="true"></i> <span class="sr-only">Example of </span>hourglass-2 <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hourglass-3" aria-hidden="true"></i> <span class="sr-only">Example of </span>hourglass-3 <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hourglass-end" aria-hidden="true"></i> <span class="sr-only">Example of </span>hourglass-end</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hourglass-half" aria-hidden="true"></i> <span class="sr-only">Example of </span>hourglass-half</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hourglass-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>hourglass-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-hourglass-start" aria-hidden="true"></i> <span class="sr-only">Example of </span>hourglass-start</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-i-cursor" aria-hidden="true"></i> <span class="sr-only">Example of </span>i-cursor</div>
    
      
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-image" aria-hidden="true"></i> <span class="sr-only">Example of </span>image <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-inbox" aria-hidden="true"></i> <span class="sr-only">Example of </span>inbox</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-industry" aria-hidden="true"></i> <span class="sr-only">Example of </span>industry</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-info" aria-hidden="true"></i> <span class="sr-only">Example of </span>info</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-info-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>info-circle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-institution" aria-hidden="true"></i> <span class="sr-only">Example of </span>institution <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-key" aria-hidden="true"></i> <span class="sr-only">Example of </span>key</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-keyboard-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>keyboard-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-language" aria-hidden="true"></i> <span class="sr-only">Example of </span>language</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-laptop" aria-hidden="true"></i> <span class="sr-only">Example of </span>laptop</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-leaf" aria-hidden="true"></i> <span class="sr-only">Example of </span>leaf</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-legal" aria-hidden="true"></i> <span class="sr-only">Example of </span>legal <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-lemon-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>lemon-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-level-down" aria-hidden="true"></i> <span class="sr-only">Example of </span>level-down</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="sr-only">Example of </span>level-up</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-life-bouy" aria-hidden="true"></i> <span class="sr-only">Example of </span>life-bouy <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-life-buoy" aria-hidden="true"></i> <span class="sr-only">Example of </span>life-buoy <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-life-ring" aria-hidden="true"></i> <span class="sr-only">Example of </span>life-ring</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-life-saver" aria-hidden="true"></i> <span class="sr-only">Example of </span>life-saver <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-lightbulb-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>lightbulb-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-line-chart" aria-hidden="true"></i> <span class="sr-only">Example of </span>line-chart</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-location-arrow" aria-hidden="true"></i> <span class="sr-only">Example of </span>location-arrow</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-lock" aria-hidden="true"></i> <span class="sr-only">Example of </span>lock</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-low-vision" aria-hidden="true"></i> <span class="sr-only">Example of </span>low-vision</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-magic" aria-hidden="true"></i> <span class="sr-only">Example of </span>magic</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-magnet" aria-hidden="true"></i> <span class="sr-only">Example of </span>magnet</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-mail-forward" aria-hidden="true"></i> <span class="sr-only">Example of </span>mail-forward <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-mail-reply" aria-hidden="true"></i> <span class="sr-only">Example of </span>mail-reply <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-mail-reply-all" aria-hidden="true"></i> <span class="sr-only">Example of </span>mail-reply-all <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-male" aria-hidden="true"></i> <span class="sr-only">Example of </span>male</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-map" aria-hidden="true"></i> <span class="sr-only">Example of </span>map</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-map-marker" aria-hidden="true"></i> <span class="sr-only">Example of </span>map-marker</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-map-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>map-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-map-pin" aria-hidden="true"></i> <span class="sr-only">Example of </span>map-pin</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-map-signs" aria-hidden="true"></i> <span class="sr-only">Example of </span>map-signs</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-meh-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>meh-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-microchip" aria-hidden="true"></i> <span class="sr-only">Example of </span>microchip</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-microphone" aria-hidden="true"></i> <span class="sr-only">Example of </span>microphone</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-microphone-slash" aria-hidden="true"></i> <span class="sr-only">Example of </span>microphone-slash</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-minus" aria-hidden="true"></i> <span class="sr-only">Example of </span>minus</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-minus-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>minus-circle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-minus-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>minus-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-minus-square-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>minus-square-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-mobile" aria-hidden="true"></i> <span class="sr-only">Example of </span>mobile</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-mobile-phone" aria-hidden="true"></i> <span class="sr-only">Example of </span>mobile-phone <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-money" aria-hidden="true"></i> <span class="sr-only">Example of </span>money</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-moon-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>moon-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-mortar-board" aria-hidden="true"></i> <span class="sr-only">Example of </span>mortar-board <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-motorcycle" aria-hidden="true"></i> <span class="sr-only">Example of </span>motorcycle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-mouse-pointer" aria-hidden="true"></i> <span class="sr-only">Example of </span>mouse-pointer</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-music" aria-hidden="true"></i> <span class="sr-only">Example of </span>music</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-navicon" aria-hidden="true"></i> <span class="sr-only">Example of </span>navicon <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>newspaper-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-object-group" aria-hidden="true"></i> <span class="sr-only">Example of </span>object-group</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-object-ungroup" aria-hidden="true"></i> <span class="sr-only">Example of </span>object-ungroup</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-paint-brush" aria-hidden="true"></i> <span class="sr-only">Example of </span>paint-brush</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-paper-plane" aria-hidden="true"></i> <span class="sr-only">Example of </span>paper-plane</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>paper-plane-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-paw" aria-hidden="true"></i> <span class="sr-only">Example of </span>paw</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-pencil" aria-hidden="true"></i> <span class="sr-only">Example of </span>pencil</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-pencil-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>pencil-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>pencil-square-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-percent" aria-hidden="true"></i> <span class="sr-only">Example of </span>percent</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-phone" aria-hidden="true"></i> <span class="sr-only">Example of </span>phone</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-phone-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>phone-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-photo" aria-hidden="true"></i> <span class="sr-only">Example of </span>photo <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-picture-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>picture-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-pie-chart" aria-hidden="true"></i> <span class="sr-only">Example of </span>pie-chart</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-plane" aria-hidden="true"></i> <span class="sr-only">Example of </span>plane</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-plug" aria-hidden="true"></i> <span class="sr-only">Example of </span>plug</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-plus" aria-hidden="true"></i> <span class="sr-only">Example of </span>plus</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-plus-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>plus-circle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>plus-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-plus-square-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>plus-square-o</div>
    
         <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-power-off" aria-hidden="true"></i> <span class="sr-only">Example of </span>power-off</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-print" aria-hidden="true"></i> <span class="sr-only">Example of </span>print</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-puzzle-piece" aria-hidden="true"></i> <span class="sr-only">Example of </span>puzzle-piece</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-qrcode" aria-hidden="true"></i> <span class="sr-only">Example of </span>qrcode</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-question" aria-hidden="true"></i> <span class="sr-only">Example of </span>question</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-question-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>question-circle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>question-circle-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-quote-left" aria-hidden="true"></i> <span class="sr-only">Example of </span>quote-left</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-quote-right" aria-hidden="true"></i> <span class="sr-only">Example of </span>quote-right</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-random" aria-hidden="true"></i> <span class="sr-only">Example of </span>random</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-recycle" aria-hidden="true"></i> <span class="sr-only">Example of </span>recycle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-refresh" aria-hidden="true"></i> <span class="sr-only">Example of </span>refresh</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-registered" aria-hidden="true"></i> <span class="sr-only">Example of </span>registered</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-remove" aria-hidden="true"></i> <span class="sr-only">Example of </span>remove <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-reorder" aria-hidden="true"></i> <span class="sr-only">Example of </span>reorder <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-reply" aria-hidden="true"></i> <span class="sr-only">Example of </span>reply</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-reply-all" aria-hidden="true"></i> <span class="sr-only">Example of </span>reply-all</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-retweet" aria-hidden="true"></i> <span class="sr-only">Example of </span>retweet</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-road" aria-hidden="true"></i> <span class="sr-only">Example of </span>road</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-rocket" aria-hidden="true"></i> <span class="sr-only">Example of </span>rocket</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-rss" aria-hidden="true"></i> <span class="sr-only">Example of </span>rss</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-rss-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>rss-square</div>
    
      
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-search" aria-hidden="true"></i> <span class="sr-only">Example of </span>search</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-search-minus" aria-hidden="true"></i> <span class="sr-only">Example of </span>search-minus</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-search-plus" aria-hidden="true"></i> <span class="sr-only">Example of </span>search-plus</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-send" aria-hidden="true"></i> <span class="sr-only">Example of </span>send <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-send-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>send-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-server" aria-hidden="true"></i> <span class="sr-only">Example of </span>server</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-share" aria-hidden="true"></i> <span class="sr-only">Example of </span>share</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-share-alt" aria-hidden="true"></i> <span class="sr-only">Example of </span>share-alt</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-share-alt-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>share-alt-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-share-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>share-square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-share-square-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>share-square-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-shield" aria-hidden="true"></i> <span class="sr-only">Example of </span>shield</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-ship" aria-hidden="true"></i> <span class="sr-only">Example of </span>ship</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-shopping-bag" aria-hidden="true"></i> <span class="sr-only">Example of </span>shopping-bag</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <span class="sr-only">Example of </span>shopping-basket</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <span class="sr-only">Example of </span>shopping-cart</div>
    
      
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sign-in" aria-hidden="true"></i> <span class="sr-only">Example of </span>sign-in</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sign-language" aria-hidden="true"></i> <span class="sr-only">Example of </span>sign-language</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sign-out" aria-hidden="true"></i> <span class="sr-only">Example of </span>sign-out</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-signal" aria-hidden="true"></i> <span class="sr-only">Example of </span>signal</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-signing" aria-hidden="true"></i> <span class="sr-only">Example of </span>signing <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sitemap" aria-hidden="true"></i> <span class="sr-only">Example of </span>sitemap</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sliders" aria-hidden="true"></i> <span class="sr-only">Example of </span>sliders</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-smile-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>smile-o</div>
    
     
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-soccer-ball-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>soccer-ball-o <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-alpha-asc" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-alpha-asc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-alpha-desc" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-alpha-desc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-amount-asc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-amount-desc" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-amount-desc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-asc" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-asc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-desc" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-desc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-down" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-down <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-numeric-asc" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-numeric-asc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-numeric-desc" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-numeric-desc</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sort-up" aria-hidden="true"></i> <span class="sr-only">Example of </span>sort-up <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-space-shuttle" aria-hidden="true"></i> <span class="sr-only">Example of </span>space-shuttle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-spinner" aria-hidden="true"></i> <span class="sr-only">Example of </span>spinner</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-spoon" aria-hidden="true"></i> <span class="sr-only">Example of </span>spoon</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>square</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-square-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>square-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-star" aria-hidden="true"></i> <span class="sr-only">Example of </span>star</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-star-half" aria-hidden="true"></i> <span class="sr-only">Example of </span>star-half</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-star-half-empty" aria-hidden="true"></i> <span class="sr-only">Example of </span>star-half-empty <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-star-half-full" aria-hidden="true"></i> <span class="sr-only">Example of </span>star-half-full <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-star-half-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>star-half-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-star-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>star-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sticky-note" aria-hidden="true"></i> <span class="sr-only">Example of </span>sticky-note</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>sticky-note-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-street-view" aria-hidden="true"></i> <span class="sr-only">Example of </span>street-view</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-suitcase" aria-hidden="true"></i> <span class="sr-only">Example of </span>suitcase</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-sun-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>sun-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-support" aria-hidden="true"></i> <span class="sr-only">Example of </span>support <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tablet" aria-hidden="true"></i> <span class="sr-only">Example of </span>tablet</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tachometer" aria-hidden="true"></i> <span class="sr-only">Example of </span>tachometer</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tag" aria-hidden="true"></i> <span class="sr-only">Example of </span>tag</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tags" aria-hidden="true"></i> <span class="sr-only">Example of </span>tags</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tasks" aria-hidden="true"></i> <span class="sr-only">Example of </span>tasks</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-taxi" aria-hidden="true"></i> <span class="sr-only">Example of </span>taxi</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-television" aria-hidden="true"></i> <span class="sr-only">Example of </span>television</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-terminal" aria-hidden="true"></i> <span class="sr-only">Example of </span>terminal</div>
    
      
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-thumb-tack" aria-hidden="true"></i> <span class="sr-only">Example of </span>thumb-tack</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-thumbs-down" aria-hidden="true"></i> <span class="sr-only">Example of </span>thumbs-down</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <span class="sr-only">Example of </span>thumbs-o-down</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <span class="sr-only">Example of </span>thumbs-o-up</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-thumbs-up" aria-hidden="true"></i> <span class="sr-only">Example of </span>thumbs-up</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-ticket" aria-hidden="true"></i> <span class="sr-only">Example of </span>ticket</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-times" aria-hidden="true"></i> <span class="sr-only">Example of </span>times</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-times-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>times-circle</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-times-circle-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>times-circle-o</div>
    
       
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tint" aria-hidden="true"></i> <span class="sr-only">Example of </span>tint</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-toggle-down" aria-hidden="true"></i> <span class="sr-only">Example of </span>toggle-down <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-toggle-left" aria-hidden="true"></i> <span class="sr-only">Example of </span>toggle-left <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-toggle-off" aria-hidden="true"></i> <span class="sr-only">Example of </span>toggle-off</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-toggle-on" aria-hidden="true"></i> <span class="sr-only">Example of </span>toggle-on</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-toggle-right" aria-hidden="true"></i> <span class="sr-only">Example of </span>toggle-right <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-toggle-up" aria-hidden="true"></i> <span class="sr-only">Example of </span>toggle-up <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-trademark" aria-hidden="true"></i> <span class="sr-only">Example of </span>trademark</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-trash" aria-hidden="true"></i> <span class="sr-only">Example of </span>trash</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-trash-o" aria-hidden="true"></i> <span class="sr-only">Example of </span>trash-o</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tree" aria-hidden="true"></i> <span class="sr-only">Example of </span>tree</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-trophy" aria-hidden="true"></i> <span class="sr-only">Example of </span>trophy</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-truck" aria-hidden="true"></i> <span class="sr-only">Example of </span>truck</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tty" aria-hidden="true"></i> <span class="sr-only">Example of </span>tty</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-tv" aria-hidden="true"></i> <span class="sr-only">Example of </span>tv <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-umbrella" aria-hidden="true"></i> <span class="sr-only">Example of </span>umbrella</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-universal-access" aria-hidden="true"></i> <span class="sr-only">Example of </span>universal-access</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-university" aria-hidden="true"></i> <span class="sr-only">Example of </span>university</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-unlock" aria-hidden="true"></i> <span class="sr-only">Example of </span>unlock</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-unlock-alt" aria-hidden="true"></i> <span class="sr-only">Example of </span>unlock-alt</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-unsorted" aria-hidden="true"></i> <span class="sr-only">Example of </span>unsorted <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-upload" aria-hidden="true"></i> <span class="sr-only">Example of </span>upload</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-user" aria-hidden="true"></i> <span class="sr-only">Example of </span>user</div>
    
       
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-user-plus" aria-hidden="true"></i> <span class="sr-only">Example of </span>user-plus</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-user-secret" aria-hidden="true"></i> <span class="sr-only">Example of </span>user-secret</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-user-times" aria-hidden="true"></i> <span class="sr-only">Example of </span>user-times</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-users" aria-hidden="true"></i> <span class="sr-only">Example of </span>users</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-video-camera" aria-hidden="true"></i> <span class="sr-only">Example of </span>video-camera</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-volume-control-phone" aria-hidden="true"></i> <span class="sr-only">Example of </span>volume-control-phone</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-volume-down" aria-hidden="true"></i> <span class="sr-only">Example of </span>volume-down</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-volume-off" aria-hidden="true"></i> <span class="sr-only">Example of </span>volume-off</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-volume-up" aria-hidden="true"></i> <span class="sr-only">Example of </span>volume-up</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-warning" aria-hidden="true"></i> <span class="sr-only">Example of </span>warning <span class="text-muted">(alias)</span></div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-wheelchair" aria-hidden="true"></i> <span class="sr-only">Example of </span>wheelchair</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-wheelchair-alt" aria-hidden="true"></i> <span class="sr-only">Example of </span>wheelchair-alt</div>
    
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-wifi" aria-hidden="true"></i> <span class="sr-only">Example of </span>wifi</div>
      <div class="fa-hover col-md-3 col-sm-4"><i class="fa fa-wrench" aria-hidden="true"></i> <span class="sr-only">Example of </span>wrench</div>
    
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" onclick="saveIcons()">保存</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php  include page('footer');?>
<script>

    $(".choose_all").click(function(){
        if(this.checked){
            $(".child_box").each(function(){
                this.checked = true;
            })
        }else{
            $(".child_box").each(function(){
                this.checked = false;
            })
        }

    });


    $("input[name=sort]").blur(function(){
        //如果输入的值大于设定的最大值，就显示最大值
        var maxval = parseInt($(this).attr("max"));
        var sort = parseInt($(this).val());
        var minval = $(this).attr("min");

        if(sort > maxval){
            $(this).val(maxval);
            sort = maxval;
        }
        //获取id
        var rule_id = $(this).data('rule_id');
        var url = "<?php echo web_url('shopruler',array('op'=>'menusort'));?>";
        url = url+"&rule_id="+rule_id+"&sort="+sort;
        $.getJSON(url,function(data){
            if(data.errno == 0){

            }
        },'json')
    });


    function add_menu(obj){
        var url = $(obj).data('url');
        $.ajaxLoad(url,{},function(){
            $('#alterModal').modal('show');
        })
    }
    /*设置图标*/
    function setIcon(){
        $(".set-icon").modal();
    }
    //保存图标
    function saveIcons(){
        var icon_class = $(".fa-checkeds").find("i").attr("class");
        var icon_url = "";
        $.post(icon_url,{icon_class:icon_class},function(data){

        },"json");
    }
    $(function(){
        /*选择图标*/
        $(".fa-hover").on("click",function(){
            $(".fa-hover").removeClass("fa-checkeds");
            $(this).addClass("fa-checkeds");
        });
    })
</script>
