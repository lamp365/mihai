<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
        body{background-color: #F8FAFC;}
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
				for (var i = 0 ; i<str.length ; i++)
				{
                    if ( i == 0){
						stitle = '返回上一级';
                    }else{
                        document.getElementById('activenow').innerText= " > " + str[i];
					}
				}
			}
    		document.getElementById('activeworker').innerText=stitle;
    	}
    try{ace.settings.check('navbar' , 'fixed')}catch(e){}
</script>
</head>
<body scrolling="no" style="overflow:visible;">
<div class="navbar navbar-default" id="navbar">
    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <i class="icon-road"></i>
                    <span id='accountname'><?php  echo empty($settings['shop_title'])?'小物网络':$settings['shop_title'];?></span>
                </small>
            </a>
            <span style="display: inline-block;line-height: 50px;color: #fff">美元兑换人民币:<span class="usa-to-rmb">6.88</span></span>
            <!-- /.brand -->
        </div><!-- /.navbar-header -->

        <div class="navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav" style="height:45px">
                <li class="Larger">
                    <a class="dropdown-toggle"  href="<?php  echo WEBSITE_ROOT.'index.php';?>" target="_blank">
                        <i class="icon-mobile-phone"></i>
                        <span>商城首页</span>
                    </a>
                </li>
                <li class="Larger">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle modify">
                  
                            <small>修改汇率</small>                          
       
                        <!-- <i class="icon-caret-down"></i> -->
                    </a>
<!--                     <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a class="modify" href="javascript:;">
                                <i class="icon-off"></i>
                                <span class="modify-exchange-rate">修改汇率</span>
                            </a>
                            <a class="modify-record" href="javascript:;">
                                <i class="icon-off"></i>
                                <span class="modify-record-exchange-rate">汇率修改记录</span>
                            </a>
                        </li>
                    </ul> -->
                </li>
                <li class="Larger">
                    <a class="dropdown-toggle" onclick="navtoggle('修改密码')" href="<?php  echo create_url('site',array('name' => 'index','do' => 'changepwd'))?>" target="main">
                        <i class="icon-user"></i>
                        <span>修改密码</span>
                    </a>
                </li>
                <li class="Larger">
                    <a class="dropdown-toggle" onclick="navtoggle('退出系统')" href="<?php  echo create_url('site',array('name' => 'public','do' => 'logout'))?>" >
                        <i class="icon-off"></i>
                        <span>退出系统</span>
                    </a>
                </li>


                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
       				<span class="user-info">
									<small>欢迎光临,</small>
                        <?php echo $username ?>								</span>

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
            </ul><!-- /.ace-nav -->
        </div><!-- /.navbar-header -->
    </div><!-- /.container -->
</div>



<!-- 头部 end -->

<div class="main-container" id="main-container">
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
                <?php if (checkAdmin() || in_array("shop-mess",$menurule)) { ?>
                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-random"></i>
                            <span class="menu-text"> 换购管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?>        <!-- 子菜单 第二级-->
                                <li> <a  onclick="navtoggle('换购管理 > 换购列表')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'mess','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        换购列表
                                    </a>   </li>
                                <li> <a onclick="navtoggle('换购管理 > 添加新换购')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'mess','op'=>'post'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        添加新换购
                                    </a>   </li>

                                <li> <a onclick="navtoggle('换购管理 > 管理区间')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'area','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        管理区间
                                    </a>   </li>
                            <li style="display:none;"> <a onclick="navtoggle('换购管理 > 换购记录')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'mess','op'=>'comment'))?>" target="main">
                                    <i class="icon-double-angle-right"></i>
                                    换购记录
                                </a>   </li>
                            <?php } else{
                                foreach($parentMenuList[MenuEnum::TUAN_GOU_MANGE] as $row){
                                    $zi = "换购管理 - > {$row['moddescription']}";
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
                <?php }?>

                <?php if (checkAdmin() ||in_array("shop-dish",$menurule)) { ?>
                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-magnet"></i>
                            <span class="menu-text"> 出售中的宝贝</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?>                 <!-- 子菜单 第二级-->
                                <li> <a  onclick="navtoggle('出售中的宝贝 - > 宝贝列表')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'dish','op'=>'list'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        宝贝列表
                                    </a>   </li>
                                <li> <a onclick="navtoggle('出售中的宝贝 - > 添加新宝贝')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'dish','op'=>'post'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        添加新宝贝
                                    </a>   </li>
                                <li> <a onclick="navtoggle('出售中的宝贝 - > 仓库管理')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'disharea','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        仓库管理
                                    </a>   </li>
                                <li> <a onclick="navtoggle('出售中的宝贝 - > 评论管理')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'dish','op'=>'comment'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        评论管理
                                    </a>   </li>
                            <?php }else{
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
                            } ?>

                        </ul>
                    </li>
                <?php }?>




                <?php if (checkAdmin() ||in_array("shop-category",$menurule)) { ?>
                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-shopping-cart"></i>
                            <span class="menu-text"> 产品库管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?>                 <!-- 子菜单 第二级-->
                                <li> <a  onclick="navtoggle('商品管理 - > 商品列表')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'goods','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        产品库列表
                                    </a>   </li>
                                <li> <a onclick="navtoggle('商品管理 - > 添加新商品')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'goods','op'=>'post'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        添加新产品
                                    </a>   </li>
                                <li> <a onclick="navtoggle('商品管理 - > 管理分类')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'category','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>管理分类
                                    </a>
                                </li>
                                <li> <a onclick="navtoggle('商品管理 - > 品牌列表')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'brand','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>品牌列表
                                    </a>
                                </li>
                                <li> <a onclick="navtoggle('商品管理 - > 添加品牌')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'brand','op'=>'add'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>添加品牌
                                    </a>
                                </li>
                                <li> <a onclick="navtoggle('商品管理 - > 管理国家')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'country','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>管理国家
                                    </a>
                                </li>
                            <?php }else{
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

                            }?>
                        </ul>
                    </li>
                <?php }?>

                <?php if (checkAdmin() ||in_array("shop-order",$menurule)) { ?>
                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-tasks"></i>
                            <span class="menu-text"> 订单管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?>
                                <!-- 子菜单 第二级-->
                                <li> <a  onclick="navtoggle('订单管理 - > 所有订单')"  href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -99))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        所有订单
                                    </a>
                                </li>
                                <li> <a  onclick="navtoggle('批发订单 - > 所有订单')"  href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'purchase','op' => 'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        批发订单
                                    </a>
                                </li>
                                <li> <a  onclick="navtoggle('拼团订单 - > 所有订单')"  href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'groupbuy','op' => 'list'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        拼团订单
                                    </a>
                                </li>
                                <li> <a onclick="navtoggle('订单管理 - > 批量发货')" href="<?php  echo create_url('site', array('name' => 'shop','do'=>'orderbat','op' => 'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        批量发货
                                    </a>
                                </li>
                            <?php }else{
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
                            } ?>
                        </ul>
                    </li>
                <?php }
                if(is_array($modulelist)) {
                    foreach($modulelist as $key=>$module) {
                        if(checkAdmin() ||in_array($module['name'],$menurule)){
                            ?>
                            <li>
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


                <?php if (checkAdmin() ||in_array("shop-taxrate",$menurule)) { ?>
                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-money"></i>
                            <span class="menu-text"> 税率管理</span>
                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?> <!-- 子菜单 第二级-->
                                <li> <a  onclick="navtoggle('税率管理 - > 税率列表')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'taxs','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        税率列表
                                    </a>
                                </li>
                                <li> <a onclick="navtoggle('税率管理 - > 添加税率')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'taxs','op'=>'post'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        添加税率
                                    </a>
                                </li>

                            <?php }else{
                                foreach($parentMenuList[MenuEnum::TAXS_MANGE] as $row){
                                    $zi = "税率管理 - > {$row['moddescription']}";
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
                            }?>
                        </ul>
                    </li>
                <?php }?>

                <?php if (checkAdmin() ||in_array("shop-set",$menurule)) { ?>
                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-tasks"></i>
                            <span class="menu-text"> 个性管理</span>
                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?> <!-- 子菜单 第二级-->
                                <li> <a  onclick="navtoggle('个性管理 - > 热搜词列表')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'hottopic','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        热搜词列表
                                    </a>   </li>
                                <li> <a onclick="navtoggle('个性管理 - > 添加热搜词')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'hottopic','op'=>'add'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        添加热搜词
                                    </a>   </li>
                                <li> <a onclick="navtoggle('个性管理 - > 设置通用详情')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'good_setting','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        设置通用详情
                                    </a>
                                </li>
                            <?php }else{
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

                            }?>
                        </ul>
                    </li>
                <?php   }  ?>


                <?php if (checkAdmin() ||in_array("member-list",$menurule)) { ?>
                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-group"></i>
                            <span class="menu-text"> 会员管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?> <!-- 子菜单 第二级-->
                                <li>
                                    <a onclick="navtoggle('会员管理 - > 会员管理 ')"  href="<?php  echo create_url('site', array('name' => 'member','do' => 'list'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        会员管理
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('渠道商列表 - > 会员管理 ')"  href="<?php  echo create_url('site', array('name' => 'member','do' => 'purchase'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        渠道商列表
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('虚拟用户 - > 会员管理 ')"  href="<?php  echo create_url('site', array('name' => 'member','do' => 'dummy'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        虚拟用户
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('会员管理 - > 会员等级管理 ')"  href="<?php  echo create_url('site', array('name' => 'member','do' => 'rank'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        会员等级
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('会员管理 - > 余额提现申请 ')"  href="<?php  echo create_url('site', array('name' => 'member','do' => 'outchargegold'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        审核余额提现操作
                                    </a>
                                </li>
                            <?php }else{
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

                            }?>

                        </ul>
                    </li>
                <?php }?>



                <?php if (checkAdmin() ||in_array("bonus-bonus",$menurule)) { ?>
                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-gift"></i>
                            <span class="menu-text"> 营销管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?> <!-- 子菜单 第二级-->
                                <li> <a  onclick="navtoggle('营销管理 - > 优惠券管理')"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'bonus','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        优惠券管理
                                    </a>
                                </li>
                                <li> <a  onclick="navtoggle('营销管理 - > 促销免运费')"  href="<?php  echo create_url('site', array('name' => 'promotion','do' => 'promotion','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        促销免运费
                                    </a>
                                </li>
                            <?php }else{
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

                            }?>
                        </ul>
                    </li>
                <?php }?>

                <?php if (checkAdmin() ||in_array("shop-config",$menurule)) { ?>

                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-cogs"></i>
                            <span class="menu-text"> 商城配置</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?> <!-- 子菜单 第二级-->

                                <li> <a  onclick="navtoggle('商城配置 - > 商城基础设置')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'config'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        商城基础设置
                                    </a>
                                </li>
                                <li> <a  onclick="navtoggle('商城配置 - > 银行卡管理')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'bank'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        银行卡管理
                                    </a>
                                </li>
                                <li> <a  onclick="navtoggle('商城配置 - > 新订单邮件提醒')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'noticemail'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        新订单邮件提醒
                                    </a>
                                </li>
                                <li> <a  onclick="navtoggle('商城配置 - > 首页广告')"  href="<?php  echo create_url('site', array('name' => 'shop','do' => 'adv','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        首页广告
                                    </a>
                                </li>
                                <li> <a onclick="navtoggle('商城配置 - > 支付方式')" href="<?php  echo create_url('site', array('name' => 'modules','do' => 'payment','op'=>'list'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>支付方式
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('商城配置 - > 快捷登录')" href="<?php  echo create_url('site', array('name' => 'modules','do' => 'thirdlogin'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>快捷登录
                                    </a>
                                </li>
                                <li> <a onclick="navtoggle('商城配置 - > 配送方式')" href="<?php  echo create_url('site', array('name' => 'modules','do' => 'dispatch','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        配送方式
                                    </a>
                                </li>

                                <li> <a onclick="navtoggle('商城配置 - > app版本管理')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'app_version','op'=>'list'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        app版本管理
                                    </a>
                                </li>
                                
                                <li> <a onclick="navtoggle('商城配置 - > app端banner管理')" href="<?php  echo create_url('site', array('name' => 'shop','do' => 'app_banner','op'=>'list'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        app端banner管理
                                    </a>
                                </li>
                                
                            <?php }else{
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

                            }?>
                        </ul>
                    </li>
                <?php }?>

                <?php if (checkAdmin() ||in_array("template-set",$menurule)) { ?>
                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-cogs"></i>
                            <span class="menu-text"> 模板设置</span>
                            <b class="arrow icon-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?> <!-- 子菜单 第二级-->
                                <li>
                                    <a  onclick="navtoggle('模板设置 - > 商城主题模板')"  href="<?php  echo create_url('site', array('name' => 'shopwap','do' => 'themes','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>商城主题模板
                                    </a>
                                </li>
                                <li>
                                    <a  onclick="navtoggle('模板设置 - > 商城菜单')"  href="<?php  echo create_url('site', array('name' => 'shopwap','do' => 'shop_menu','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>商城菜单
                                    </a>
                                </li>
                                <li>
                                    <a  onclick="navtoggle('模板设置 - > 个人中心菜单')"  href="<?php  echo create_url('site', array('name' => 'shopwap','do' => 'fansindex_menu','op'=>'display'))?>" target="main">
                                        <i class="icon-double-angle-right"></i>
                                        个人中心菜单
                                    </a>
                                </li>
                            <?php }else{
                                foreach($parentMenuList[MenuEnum::TEMPLATE_MANGE] as $row){
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

                            }?>
                        </ul>
                    </li>
                <?php  } ?>

                <?php if (checkAdmin() ||in_array("weixin-weixin",$menurule)) { ?>
                    <li>
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-comments"></i>
                            <span class="menu-text"> 微信设置</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?> <!-- 子菜单 第二级-->
                                <li>
                                    <a onclick="navtoggle('微信设置 - > 微信号设置 ')"  href="<?php  echo create_url('site', array('name' => 'weixin','do' => 'setting'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        微信号设置
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('微信设置 - > 菜单管理 ')"  href="<?php  echo create_url('site', array('name' => 'weixin','do' => 'designer'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        菜单管理
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('微信设置 - > 自定义回复 ')"  href="<?php  echo create_url('site', array('name' => 'weixin','do' => 'rule'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        自定义回复
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('微信设置 - > 快速关注设置 ')"  href="<?php  echo create_url('site', array('name' => 'weixin','do' => 'guanzhu'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        快速关注设置
                                    </a>
                                </li>
                            <?php }else{
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

                            }?>
                        </ul>
                    </li>
                <?php }?>


                <?php if (checkAdmin() ||in_array("user-user",$menurule)) { ?>
                    <li>
                        <!-- 导航第一级 -->
                        <a href="#" class="dropdown-toggle">
                            <i class="icon-info-sign"></i>
                            <span class="menu-text"> 权限管理</span>

                            <b class="arrow icon-angle-down"></b>
                        </a>

                        <ul class="submenu">
                            <?php if (checkAdmin()) { ?> <!-- 子菜单 第二级-->
                                <li>
                                    <a onclick="navtoggle('权限管理 - > 管理员列表 ')"  href="<?php  echo create_url('site', array('name' => 'user','do' => 'user','op' => 'listuser'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        管理员列表
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('权限管理 - > 角色身份 ')"  href="<?php  echo create_url('site', array('name' => 'user','do' => 'user','op' => 'rolerlist'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        角色身份
                                    </a>
                                </li>
                                <li>
                                    <a onclick="navtoggle('权限管理 - > 菜单节点 ')"  href="<?php  echo create_url('site', array('name' => 'user','do' => 'user','op' => 'menudisplay'))?>" target="main" >
                                        <i class="icon-double-angle-right"></i>
                                        菜单节点
                                    </a>
                                </li>
                            <?php }else{
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

                            }?>

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
            </ul>
            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
            </div>

            <script type="text/javascript">
                try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
            </script>
        </div>

        <div class="main-content">
            <div class="breadcrumbs" id="breadcrumbs">
                <script type="text/javascript">
                    try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
                </script>
                <ul class="breadcrumb">
                    <li>
                        <i class="icon-home home-icon"></i>
                        <a  onclick="navtoggle('首页')" href="<?php  echo create_url('site', array('name' => 'index','do' => 'center'))?>" target="main">首页</a>
                    </li>
                    <li class="active"><span id="activeworker">首页</span></li>
                </ul><!-- .breadcrumb -->

                <div class="nav-search" id="nav-search">

                </div><!-- #nav-search -->
            </div>

            <div class="page-content" style="padding: 1px 13px 24px;">

                <iframe  scrolling="no" frameborder="0" height="100%" onload="iFrameHeight()" style="width:100%;min-height:1000px;" name="main" id="main" src="<?php  echo create_url('site', array('name' => 'index','do' => 'center'))?>"></iframe>
                <script type="text/javascript" language="javascript">
                    function iFrameHeight() {
                        var ifm= document.getElementById("main");
                        var subWeb = document.frames ? document.frames["main"].document :ifm.contentDocument;
                        if(ifm != null && subWeb != null) {
                            ifm.height = subWeb.body.scrollHeight + 60;
                        }
                    }
                    $(document).ready(function(){
                        iFrameHeight();
                    });
                </script>
            </div>
        </div>


    </div>
<!-- 修改汇率 -->
<div class='modal fade modify-modal' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'> 
                <button type='button' class='close' data-dismiss='modal'>
                    <span aria-hidden='true'>&times;</span>
                    <span class='sr-only'>Close</span>
                </button>
                <h4 class='modal-title' id='myModalLabel'>修改汇率</h4>
            </div>
            <div class='modal-body' style="text-align: center;">
                <div style="padding: 50px 0;">输入要修改的汇率:<input type="text" name="" value="" class="modify-val"><button type="button" class="modify-sure" value="" style="    color: #fff;
    background-color: #428bca;border:none;margin-left: 5px;height: 28px;line-height: 28px; border-color: #357ebd;">确定</button></div>
            </div>
        </div>
    </div>
</div>
<div class='modal fade modify-record-modal' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'> 
                <button type='button' class='close' data-dismiss='modal'>
                    <span aria-hidden='true'>&times;</span>
                    <span class='sr-only'>Close</span>
                </button>
                <h4 class='modal-title' id='myModalLabel'>修改汇率</h4>
            </div>
            <div class='modal-body'>
                <ul style="list-style: none;padding-left: 50px;">
                    <li><span>修改的时间</span>&nbsp;&nbsp;&nbsp;&nbsp;<span>修改的人</span></li>
                    <li><span>修改的时间</span>&nbsp;&nbsp;&nbsp;&nbsp;<span>修改的人</span></li>
                    <li><span>修改的时间</span>&nbsp;&nbsp;&nbsp;&nbsp;<span>修改的人</span></li>
                    <li><span>修改的时间</span>&nbsp;&nbsp;&nbsp;&nbsp;<span>修改的人</span></li>
                </ul>
            </div>
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
    })
</script>
</div>
</body>
</html>