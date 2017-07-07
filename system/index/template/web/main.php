<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
     <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
     <META HTTP-EQUIV="Expires" CONTENT="0"> 
    <title><?php  echo empty($settings['shop_title'])?'小物网络':$settings['shop_title'];?></title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <meta name="description" content="<?php  echo empty($settings['shop_description'])?'小物网络':$settings['shop_description'];?>" />
    <meta name="keywords" content="<?php  echo empty($settings['shop_keyword'])?'小物网络':$settings['shop_keyword'];?>">
    <link href="<?php echo RESOURCE_ROOT;?>/addons/common/bootstrap3/css/bootstrap.min.css" rel="stylesheet" />
    <link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/fontawesome3/css/font-awesome.min.css" />
    <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/bootstrap3/js/bootstrap.min.js"></script>

    <script src="<?php echo RESOURCE_ROOT;?>/addons/index/js/ace/ace-elements.min.js"></script>
    <script src="<?php echo RESOURCE_ROOT;?>/addons/index/js/ace/ace.min.js"></script>
    <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/index/css/ace/ace.min.css" />
    <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/index/css/ace/ace-rtl.min.css" />
    <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/index/css/ace/ace-skins.min.css" />

    <!--[if lte IE 8]>
    <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/index/css/ace/ace-ie.min.css" />
    <![endif]-->

    <!-- inline styles related to this page -->

    <!-- ace settings handler -->
    <script src="<?php echo RESOURCE_ROOT;?>/addons/index/js/ace/ace-extra.min.js"></script>
    <!--[if IE 7]>
    <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/fontawesome3/font-awesome-ie7.min.css">
    <![endif]-->

    <style>
        .main-container:after{
            background: url(<?php echo RESOURCE_ROOT;?>/addons/index/css/ace/body_bg.png) repeat-x left top #EAF0F5;
        }
        body{
            background-color: #F8FAFC;
            height: 100%;
        }
		.navs{
            background:#5D4384;
			height:40px;
			line-height:40px;
		}
        .head-second-nav{
            color: #fff;
            width: auto;
            overflow: hidden;
            float: left;
        }
        .breadcrumb{
            float: left;
            margin: 12px 22px 0 12px;
        }
        .breadcrumbs{
            background-color: #FFFFFF;overflow: auto;
        }
        .ace-nav>li.open>a{
            background-color: #FFF!important;
            color: #555!important;
        }
        .ace-nav>li.open.light-blue>a{
            background-color: #FFF!important;
            color: #555!important;
        }
        .ace-nav>li>a{
			height:40px;
			line-height:40px;
            background-color: #5D4384;
            color: #FFF;
        }
        .ace-nav>li{
            border-left: none;
        }
        .light-blue,.ace-nav>li.light-blue>a{
           background-color: #FFF!important; 
        }
        .user-info small{
            display: inline-block;
            font-size: 12px;
        }
        .navbar .navbar-header{
            position: relative;
        }
        .ace-nav>li.login-out{
            position: absolute!important;
            right: 0;
            bottom: -25px;
            height: 25px;
            line-height: 25px;
            font-size: 12px;
        }

        .login-out .icon-off{
            font-size: 12px!important;
        }
        .head-second-nav ul{
            margin: 0;
            padding: 0;
            overflow: hidden;
            padding-left: 5px;
            box-sizing: border-box;
        }
        .head-second-nav ul li{
            list-style: none;
            float: left;
            padding: 0 10px;
            cursor: pointer;
            height: 24px;
            line-height: 24px;
            font-size: 12px;
            background: #0d775f;
            margin-left: 6px;
        }
        .head-second-nav ul li a{
            color: #fff;
            text-decoration: none;
        }
        .head-second-nav ul li:hover{
            background-color: #39a3a7;
        }
        .nav-active{
            background-color: #39a3a7; 
        }
        .navbar{
            height: 70px;
        }
        .ace-nav>li>a:hover, .ace-nav>li>a:focus{
            background-color: #FFF;
			color:#555;
        }
        .navbar-brand{
            line-height: 50px;
			padding:0 0 0 10px;
           
            float: none;
            color: #fff
        }
        .navbar-brand:hover{
            color: #fff
        }
        .user-info{
            top: 9px;
        }
        .navbar-header.pull-left{
            width: 700px;
        }
        .label-default {
            background-color: #777!important;
            line-height: 1.3;
        }
        .label-default a{
            color: #fff
        }
        /*侧边导航颜色修改 begin*/
        .sidebar{
            background-color: #2e353d;
        }
        .sidebar:before,.sidebar-collapse:before,.sidebar-collapse,.sidebar-collapse>[class*="icon-"]{
            background-color: #2e353d;
        }
        .nav-list>li a>.arrow{
            color: #fff;
        }
        .nav-list>li a:hover>.arrow{
            color: #fff;
        }
        .sidebar-collapse{
            border-bottom: none;
        }
        .sidebar-collapse:before{
            border-top: 1px solid #9e9e9e;
        }
        .nav-list>li{
            border-top: none;
            border-bottom: 1px solid #484848;
        }
        .nav-list>li>a:hover:before{
            background-color: #b5b5b5;
            height: 37px;
            margin-top: 1px;
        }
        .nav-list>li>a:hover{
            background-color: #2e353d;
        }
        .nav-list>li.open{
            border-bottom: none;
        }
        .nav-list>li .submenu{
            border-top: none;

        }
        .nav-list>li.open>a:hover:before{
            background-color: #2e353d;
        }
        .nav-list>li .submenu>li>a:hover {
            color: #fff;
        }
        .nav-list>li .submenu>li a>[class*="icon-"]:first-child{
            background: none;
        }
        .nav-list>li>a{
            background-color: #2e353d;
            color: #fff;
        }
        .nav-list>li>a:link{
            background-color: #2e353d;
            color: #fff;
        }
        .nav-list>li>a:visited{
            background-color: #2e353d;
            color: #fff;
        }
        .nav-list>li>a:active{
            background-color: #2e353d;
            color: #fff;
        }
        .nav-list>li>a:hover{
            background-color: #2e353d;
            color: #fff;
        }
        .nav-list>li .submenu{
            background-color: #181c20;
        }

        .nav-list>li>.submenu>li:first-child>a{
            border-top: none;
        }
        .nav-list>li a:hover>.arrow, .nav-list>li.active>a>.arrow, .nav-list>li.open>a>.arrow{
            color: #fff;
        }
        .nav-list .open>a, .nav-list .open>a:hover, .nav-list .open>a:focus{
            background-color: #2e353d;
        }
        .nav-list>li .submenu>li>a{
            color: #fff;
            border-bottom: 1px solid #484848;
            border-top: none;
        }
        .nav-list>li.open>a{
            background-color: #2e353d;
            color: #fff;
        }
        .nav-list>li:not(open)>a:hover{
            background-color: #4f5b69;
            color: #fff;
            -webkit-transition: all 1s ease;
            -moz-transition: all 1s ease;
            -o-transition: all 1s ease;
            -ms-transition: all 1s ease;
            transition: all 1s ease;
        }
        /*侧边导航颜色修改 end*/
       .toback{
       		cursor: pointer;
       }
       .toback:hover{
       	text-decoration: underline;
       }
       .left-nav-tap{
        float: left;
        width: auto;
       }
	   .left-nav-tap ul{
           margin:0;
	   }
       .left-nav-tap li span{
          margin-left:4px;
	   }
       .left-nav-tap li{
        float: left;
        list-style: none;
        padding: 0 15px;
        border-left: 1px solid #cad2e2;
        color: #555;
        font-size: 13px;
        border-bottom: 1px solid #fff;
        cursor: pointer;
       }
       .left-nav-tap li:hover{
          background-color: #fff;
       }
       .left-nav-tap li a{
        color: #555;
        text-decoration: none;
       }
       .left-nav-tap li:last-child{
        border-right: 1px solid #cad2e2;
       }
       .nav-list>li{
        display: none;
       }
       .nav-list li.jichu,.nav-list li.public-tap{
        display: block;
       }

       .left-nav-tap .left-nav-checked{
           background-color: #fff;
          border-bottom: 1px solid;
       }
    </style>
    <script type="text/javascript">
    	function navtoggle(stitle)
    	{
    		if(stitle=='')
    		{
    		   stitle='控制台';	
    		}else{
                var str = new Array();
				str=stitle.split(">");
                if ( str.length > 0){
                   stitle = str[1];
                        //document.getElementById('activenow').innerText= " > " + str[i];
				}
			}
    		document.getElementById('activeworker').innerText=stitle;    		
    		
    	}
    	
    try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    

</script>
</head>
<body>
<div class="main-container" id="main-container">
    <div class="navs">
	 <div class="head_logo" style="float:left;height:40px;line-height:40px;">
                <a href="" style="float:left;color:#fff;text-decoration:none;margin-left:13px;font-size:20px;">
                      <i class="icon-home"></i>&nbsp;<?php  echo empty($settings['shop_title'])?'小物网络':'后台管理';?>
                </a>
      </div>
	  
      <div class="head-second-nav">
         <ul>
             <?php if(!empty($top_menu)){   foreach($top_menu as $one_menu){ $url = web_url($one_menu['moddo'],array('name'=>$one_menu['modname'],'op'=>$one_menu['modop']));  ?>
            <li><a target="main" href="<?php echo $url; ?>"><?php echo $one_menu['moddescription'] ?></a></li>
             <?php }} ?>
        </ul>
     </div>
	  <div class="pull-right" role="navigation">
                    <ul class="nav ace-nav">
                        <li class="Larger">
                            <a class="dropdown-toggle"  href="<?php  echo WEBSITE_ROOT.'index.php';?>" target="_blank">
                                <i class="icon-mobile-phone"></i>
                                <span>商城首页</span>
                            </a>
                        </li>
                        <li class="Larger">
                            <a class="dropdown-toggle" onclick="navtoggle('修改密码')" href="<?php  echo create_url('site',array('name' => 'index','do' => 'changepwd'))?>" target="main">
                                 <i class="icon-key"></i>
                                <span>修改密码</span>
                            </a>
                        </li>
                        <li class="Larger">
                            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                                <span>
								      <i class="icon-user"></i>
                                      <small>欢迎光临,</small>
                                      <?php echo $username ?>                             
								</span>

                                <i class="icon-caret-down"></i>
                            </a>

                            <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">

                                <li>
                                    <a onclick="navtoggle('退出系统')" href="<?php  echo create_url('site',array('name' => 'public','do' => 'logout'))?>">
                                        <i class="icon-off"></i>
                                        退出
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="Larger">
                           
                            <a class="dropdown-toggle" onclick="navtoggle('退出系统')" href="<?php  echo create_url('site',array('name' => 'public','do' => 'logout'))?>" >
                                <i class="icon-off"></i>
                                <span>退出系统</span>
                            </a>
                           
                        </li>
                    </ul><!-- /.ace-nav -->
                </div><!-- /.navbar-header -->
  </div>
    <script type="text/javascript">
        try{ace.settings.check('main-container' , 'fixed')}catch(e){}
    </script>
    <div class="main-container-inner">
        <a class="menu-toggler" id="menu-toggler" href="#">
            <span class="menu-text"></span>
        </a>
        <div class="sidebar" id="sidebar">
            <script type="text/javascript">
                try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
            </script>
            <div class="sidebar-shortcuts" id="sidebar-shortcuts">

                <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                    <span class="btn btn-success"></span>

                    <span class="btn btn-info"></span>

                    <span class="btn btn-warning"></span>

                    <span class="btn btn-danger"></span>
                </div>
            </div><!-- #sidebar-shortcuts -->
          
            <ul class="nav nav-list">

                <?php if (checkAdmin() ||in_array("shop-dish",$menurule)) { ?>
                    <li class="shangpin">
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-magnet"></i>
                            <span class="menu-text"> 出售中的宝贝</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::SHOP_SALE_MANGE] as $row){
                                    $zi = "出售中的宝贝 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }
                             ?>

                        </ul>
                    </li>
                <?php }?>




                <?php if (checkAdmin() ||in_array("shop-category",$menurule)) { ?>
                    <li class="shangpin">
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-shopping-cart"></i>
                            <span class="menu-text"> 产品库管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::PRODUCT_MANGE] as $row){
                                    $zi = "商品管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }

                            ?>
                        </ul>
                    </li>
                <?php }?>

                <?php if (checkAdmin() ||in_array("shop-img",$menurule)) { ?>
                    <li class="shangpin">
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-shopping-cart"></i>
                            <span class="menu-text">图片管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::IMG_MANGE] as $row){
                                    $zi = "图片管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }

                            ?>
                        </ul>
                    </li>
                <?php }?>


                <?php if (checkAdmin() ||in_array("shop-order",$menurule)) { ?>
                    <li class="dingdan">
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-tasks"></i>
                            <span class="menu-text"> 订单管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::ORDER_MANGE] as $row){
                                    $zi = "订单管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }
                             ?>
                        </ul>
                    </li>
                <?php }
                if(is_array($modulelist)) {
                    foreach($modulelist as $key=>$module) {
                        if(checkAdmin() ||in_array($module['name'],$menurule)){
                            ?>
                            <li class="xiaoshou">
                                <!-- 导航第一级 -->
                                <a href="#" class="dropdown-toggle">
                                    <i class="<?php  echo (empty($module['icon'])||$module['icon']=='icon-flag')?'icon-sitemap':$module['icon'] ?>"></i>
                                    <span class="menu-text"> <?php  echo $module['title'] ?></span>
                                    <b class="arrow icon-angle-down"></b>
                                </a>

                                <ul class="submenu">
                                    <?php if (checkAdmin()) { ?>
                                        <?php  foreach($module['menus'] as $menu) { ?>

                                            <li>
                                                <a href="<?php  echo $menu['href'] ?>" target="main" >
                                                    <i class="icon-double-angle-right"></i>
                                                    <?php  echo $menu['title'] ?>
                                                </a>
                                            </li>

                                        <?php  } ?>
                                    <?php  }else{
                                        foreach($parentMenuList[$key] as $row){
                                            $zi = "{$module['title']} - > {$row['moddescription']}";
                                            if(empty($row['modop'])){
                                                $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                            }else{
                                                $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                            }
                                            echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                        }
                                    } ?>
                                </ul>
                            </li>
                        <?php }  }}   ?>


              

                <?php if (checkAdmin() ||in_array("shop-set",$menurule)) { ?>
                    <li class="jichu">
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-tasks"></i>
                            <span class="menu-text"> 个性管理</span>
                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::GEXING_MANGE] as $row){
                                    $zi = "个性管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }

                            ?>
                        </ul>
                    </li>
                <?php   }  ?>


                <?php if (checkAdmin() ||in_array("member-list",$menurule)) { ?>
                    <li class="huiyuan">
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-group"></i>
                            <span class="menu-text"> 会员管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::MEMBER_MANGE] as $row){
                                    $zi = "会员管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }

                            ?>

                        </ul>
                    </li>
                <?php }?>


                <?php if (checkAdmin() ||in_array("bonus-bonus",$menurule)) { ?>
                    <li class="yingxiao">
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-gift"></i>
                            <span class="menu-text"> 营销管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::YINGXIAO_MANGE] as $row){
                                    $zi = "营销管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }
                            ?>
                        </ul>
                    </li>
                <?php }?>
                <?php if (checkAdmin() ||in_array("action-config",$menurule)) { ?>
                    <li class="yingxiao">
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-gift"></i>
                            <span class="menu-text"> 活动管理 </span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::ACTION_MANGE] as $row){
                                    $zi = "活动管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }
                            ?>
                        </ul>
                    </li>
                <?php }?>
                <?php if (checkAdmin() ||in_array("shop-config",$menurule)) { ?>

                    <li class="jichu">
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-cogs"></i>
                            <span class="menu-text"> 商城配置</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::SHOP_MANGE] as $row){
                                    $zi = "商城配置 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }

                            ?>
                        </ul>
                    </li>
                <?php }?>

                <?php if (checkAdmin() ||in_array("template-set",$menurule)) { ?>
                    <li class="jichu">
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-cogs"></i>
                            <span class="menu-text"> 模板设置</span>
                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::TEMPLATE_MANGE] as $row){
                                    $zi = "模板设置 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }

                            ?>
                        </ul>
                    </li>
                <?php  } ?>

                <?php if (checkAdmin() ||in_array("weixin-weixin",$menurule)) { ?>
                    <li class="xitong">
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-comments"></i>
                            <span class="menu-text"> 微信设置</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::WECHAT_MANGE] as $row){
                                    $zi = "微信设置 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }

                            ?>
                        </ul>
                    </li>
                <?php }?>

                
                 <?php if (checkAdmin() ||in_array("app-manage",$menurule)) { ?>
                    <li class="xitong">
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-mobile-phone"></i>
                            <span class="menu-text"> app管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::APP_MANGE] as $row){
                                    $zi = "app管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                            <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                                <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                            </a></li>
                                            ";
                                }
                            ?>
                        </ul>
                    </li>
                <?php }?>
                <?php if (checkAdmin() ||in_array("user-user",$menurule)) { ?>
                    <li class="xitong">
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-info-sign"></i>
                            <span class="menu-text"> 权限管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                                foreach($parentMenuList[MenuEnum::ROLE_MANGE] as $row){
                                    $zi = "权限管理 - > {$row['moddescription']}";
                                    if(empty($row['modop'])){
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                    }else{
                                        $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                    }
                                    echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                                }

                            ?>

                        </ul>
                    </li>
                <?php }?>


                <?php  if(is_array($modulelist)) { foreach($modulelist as $module) {
                    if(($module['name']!='addon6'&&$module['name']!='addon7'&&$module['name']!='addon8'&&$module['name']!='addon9'))
                    {
                        if($module['icon']=='yingxiao'||$module['name']=='addon2'||$module['name']=='addon3'||$module['name']=='addon4'||$module['name']=='addon10'||$module['name']=='addon12'||$module['name']=='addon13'||$module['name']=='addon14'||$module['name']=='addon15'){
                            ?>
                            <?php
                        }else
                        {
                            ?>


                            <li>
                                <!-- 导航第一级 -->
                                <a href="#" class="dropdown-toggle">
                                    <i class="<?php  echo (empty($module['icon'])||$module['icon']=='icon-flag')?'icon-sitemap':$module['icon'] ?>"></i>
                                    <span class="menu-text"> <?php  echo $module['title'] ?></span>

                                    <b class="arrow icon-angle-down"></b>
                                </a>

                                <ul class="submenu">

                                    <?php  foreach($module['menus'] as $menu) { ?>

                                        <li>
                                            <a href="<?php  echo $menu['href'] ?>" target="main" >
                                                <i class="icon-double-angle-right"></i>
                                                <?php  echo $menu['title'] ?>
                                            </a>
                                        </li>


                                    <?php  } ?>
                                </ul>
                            </li>
                        <?php }  }}} ?>
                
                <?php if (checkAdmin() ||in_array("shangpu-shop",$menurule)) { ?>
                    <li class="shangpu">
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-comments"></i>
                            <span class="menu-text"> 商铺管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php
                            foreach($parentMenuList[MenuEnum::SHANGPU] as $row){
                                $zi = "商铺管理 - > {$row['moddescription']}";
                                if(empty($row['modop'])){
                                    $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo']));
                                }else{
                                    $url = create_url('site', array('name' => $row['modname'],'do' => $row['moddo'],'op'=>$row['modop']));
                                }
                                echo "
                                        <li><a href='".$url."' target='main' onclick=\"navtoggle('{$zi}')\">
                                            <i class='icon-double-angle-right'></i>{$row['moddescription']}
                                        </a></li>
                                        ";
                            }

                            ?>
                        </ul>
                    </li>
                <?php }?>

            </ul>
            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
            </div>

            <script type="text/javascript">
                try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
                $(".menu-min").find("head_").hide();
            </script>
        </div>

        <div class="main-content">
            <div class="breadcrumbs" id="breadcrumbs">
                <script type="text/javascript">
                    try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
                </script>

				<div class="left-nav-tap">
					<ul>
						<li class="jichu left-nav-checked"><i class="icon-cog"></i><span>基础设置<span></li>
						<li class="huiyuan"><i class="icon-user"></i><span>会员管理<span></li>
						<li class="dingdan"><i class="icon-file-text-alt"></i><span>订单管理<span></li>
						<li class="shangpin"><i class="icon-inbox"></i><span>商品管理<span></li>
						<li class="yingxiao"><i class="icon-signal"></i><span>营销管理<span></li>
						<li class="xiaoshou"><i class="icon-bar-chart"></i><span>销售报表<span></li>
						<li class="xitong"><i class="icon-unlock-alt"></i><span>系统管理<span></li>
                        <li class="shangpu"><i class="icon-unlock-alt"></i><span>商铺管理<span></li>
					</ul>
				</div>
			
               
                <div class="nav-search" id="nav-search">

                </div><!-- #nav-search -->
            </div>

            <div class="page-content" style="padding: 1px 13px 24px;">	
            	<ul class="breadcrumb breadcrumb2" style="margin-left: -2px;">
               		<li>
                        <i class="icon-home home-icon"></i>
                        <a  onclick="navtoggle('首页')" href="<?php  echo create_url('site', array('name' => 'index','do' => 'center'))?>" target="main">首页</a>
                    </li>
                    
                    <li class="active">
                    	<span id="activeworker">首页</span>
                    </li>
               	</ul>			
                <iframe  marginheight="0" marginwidth="0" width="100%" style="margin-top: 20px" frameborder="0" onload="reinitIframe()" scrolling="no"  name="main" id="main" src="<?php  echo create_url('site', array('name' => 'index','do' => 'center'))?>"></iframe>               
                <script type="text/javascript" language="javascript">

                    function reinitIframe(){
                        var iframeHeight = $("#main").contents().find("html").height();
                        var windowHeight = $(window).height()-200;
                        if(iframeHeight<windowHeight){
                            $("#main").height(windowHeight);
                        }else{
                            $("#main").height(iframeHeight);
                        }
                        
                    }
                    window.setInterval(reinitIframe,200);
                </script>

            </div>
        </div>


    </div>

    <!-- /.main-container-inner -->
<script type="text/javascript">
    $(function(){
        $(".modify").on("click",function(){
            $(".modify-modal").modal();
            $(".modify-sure").on("click",function(){
                var regEx = /^(([1-9]\d*)|\d)(\.\d{1,4})?$/;
                var exchange_rate_value = $(".modify-val").val();
                if( !regEx.test(exchange_rate_value) ){
                   alert("请输入正确的价格");
                   return false;
                }
                if( exchange_rate_value < 5){
                    exchange_rate_value = 6.8972;
                }
                   $.post("<?php  echo create_url('site', array('name' => 'shop','do' => 'exchange_rate','op'=>'set_exchange_rate'))?>",{exchange_rate_value:exchange_rate_value},function(data){
                        if(data.errno == 200){
                            $(".modify-modal").modal('hide');
                            $(".usa-to-rmb").text(exchange_rate_value);
                        }else{
                            alert(data.message);
                        }
                    },'json') ;   
            });
        });
        $(".modify-record").on("click",function(){
            $(".modify-record-modal").modal();
        });
        $(".head-second-nav li").click(function(){
            $(this).siblings("li").removeClass("nav-active");
            $(this).addClass("nav-active");
        })
    })
    function leftNavTap(){
        var li_class_length = 0;
        $(".left-nav-tap li").click(function(){
            var this_class_name = $(this).attr("class").split(" ");
            var i = 0;
            var arr_length = this_class_name.length;
            $(this).siblings("li").removeClass("left-nav-checked");
            $(this).addClass("left-nav-checked");
            $(".nav-list>li").hide();
            for(i=0;i<arr_length;i++){
                $(".nav-list>li."+this_class_name[i]+"").show();
            }
        });
        $(".nav-list>li").each(function(index,ele){
            li_class_length = $(ele).attr("class").length;
            if( li_class_length == 0 ){
                $(ele).show();
            } 
        })
    }
    leftNavTap();
    function leftClassIsNull(){
        var class_arr = [];
        var public_tap_index = 0;
        var checked_num = 0;
        var i = 0;
        $(".left-nav-tap li").each(function(index,ele){
            class_arr = $(ele).attr("class").split(" ");
            if(!$(".nav-list>li").hasClass(class_arr[0])){
                $(ele).remove();
            }
        });
        $(".left-nav-tap li").each(function(index2,ele2){
            if($(ele2).hasClass("left-nav-checked")){
                checked_num++;
            }
        });
        if( checked_num < 1 ){
            $(".left-nav-tap li:first-child").addClass("left-nav-checked");
            $(".left-nav-tap li:first-child").trigger("click");
        }
    }
    leftClassIsNull();
    $(".nav-list>li").on("click",function(e){
        e.stopPropagation();
        $(this).find(".submenu").slideToggle(200);
        $(this).siblings("li").find(".submenu").stop();
    });
    $(".submenu>li").on("click",function(e){
        e.stopPropagation();
    })
</script>
</div>
</body>
</html>