<?php defined('SYSTEM_IN') or exit('Access Denied');defined('LOCK_TO_UPDATE') or exit('Access Denied');
$sql ="";
$weixin_rule = mysqld_select("SELECT * FROM " . table('rule') . " WHERE `modname`='weixin' and `moddo`='weixin' ");
if(empty($weixin_rule['modname']))
{
	$sql =$sql."
	delete from `squdian_rule` where `modname`='weixin' and `moddo`='weixin';
insert into `squdian_rule` (`moddescription`,`modname`,`moddo`)value('微信设置','weixin','weixin');
	";
		$user_list = mysqld_selectall("SELECT * FROM " . table('user') );
	foreach ($user_list as &$_tuser) {
		mysqld_delete('user_rule',array('uid'=>$_tuser['id'],'modname'=>'weixin','moddo'=>'weixin'));
		mysqld_insert('user_rule',array('uid'=>$_tuser['id'],'modname'=>'weixin','moddo'=>'weixin'));
	}
}
$alipay_rule = mysqld_select("SELECT * FROM " . table('rule') . " WHERE `modname`='alipay' and `moddo`='alipay' ");
if(empty($alipay_rule['modname']))
{
	$sql =$sql."
	delete from `squdian_rule` where `modname`='alipay' and `moddo`='alipay';
	insert into `squdian_rule` (`moddescription`,`modname`,`moddo`)value('支付宝服务窗设置', 'alipay', 'alipay');
	";
		$user_list = mysqld_selectall("SELECT * FROM " . table('user') );
	foreach ($user_list as &$_tuser) {
		mysqld_delete('user_rule',array('uid'=>$_tuser['id'],'modname'=>'alipay','moddo'=>'alipay'));
		mysqld_insert('user_rule',array('uid'=>$_tuser['id'],'modname'=>'alipay','moddo'=>'alipay'));
	}
}


$bonus_rule = mysqld_select("SELECT * FROM " . table('rule') . " WHERE `modname`='bonus' and `moddo`='bonus' ");
if(empty($bonus_rule['modname']))
{
	$sql =$sql."
	delete from `squdian_rule` where `modname`='bonus' and `moddo`='bonus';
insert into `squdian_rule` (`moddescription`,`modname`,`moddo`)value('促销管理','bonus','bonus');
	";
	$user_list = mysqld_selectall("SELECT * FROM " . table('user') );
	foreach ($user_list as &$_tuser) {
		mysqld_delete('user_rule',array('uid'=>$_tuser['id'],'modname'=>'bonus','moddo'=>'bonus'));
		mysqld_insert('user_rule',array('uid'=>$_tuser['id'],'modname'=>'bonus','moddo'=>'bonus'));
	}
}

$sql = $sql."
update `squdian_shop_order` set paytype=1 where paytypecode='gold';
update `squdian_shop_order` set paytype=3 where paytypecode='delivery';
update `squdian_shop_order` set paytype=2 where paytypecode='weixin';
update `squdian_shop_order` set paytype=2 where paytypecode='alipay';
update `squdian_shop_order` set paytype=2 where paytypecode='bank';


CREATE TABLE IF NOT EXISTS  `squdian_shop_goods_comment` (
  `createtime` int(10) NOT NULL,
  `optionname` varchar(100) DEFAULT NULL,
  `orderid` int(10) DEFAULT NULL,
  `ordersn` varchar(20) DEFAULT NULL,
  `openid` varchar(50) DEFAULT NULL,
  `comment` text,
  `rate` int(1) DEFAULT '0' COMMENT '0差评 1中评 2好评',
  `goodsid` int(10) DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `squdian_shop_pormotions` (
  `description` varchar(200) DEFAULT NULL COMMENT '描述(预留)',
  `endtime` int(10) NOT NULL COMMENT '束结时间',
  `starttime` int(10) NOT NULL COMMENT '开始时间',
  `condition` decimal(10,2) NOT NULL COMMENT '条件',
  `promoteType` int(11) NOT NULL COMMENT '0 按订单数包邮 1满额包邮',
  `pname` varchar(100) NOT NULL COMMENT '名称',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `squdian_gold_teller` (
  `createtime` int(10) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '0未审核-1拒绝1审核功成',
  `fee` decimal(10,2) NOT NULL,
  `openid` varchar(40) NOT NULL,
  `ordersn` varchar(20) DEFAULT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_gold_order` (
  `createtime` int(10) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL,
  `openid` varchar(40) NOT NULL,
 	`ordersn` varchar(20) NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_shop_diymenu` (
  `menu_type` varchar(10) NOT NULL,
  `torder` int(2) NOT NULL,
  `icon` varchar(30) NOT NULL,
  `url` varchar(350) NOT NULL,
  `tname` varchar(100) NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_rank_model` (
  `experience` int(11) DEFAULT '0',
  `rank_level` int(3) DEFAULT  '0' COMMENT '等级',
  `rank_name` varchar(50) DEFAULT NULL  COMMENT '等级名称',
  PRIMARY KEY (`rank_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_rank_phb` (
	`rank_level` int(11) DEFAULT '0',
  `rank_name` varchar(50) DEFAULT '',
  `realname` varchar(50) DEFAULT '' NOT NULL,
  `openid` varchar(50) DEFAULT '' NOT NULL,
  `rank_top` int(2) DEFAULT '0' COMMENT '名次',
  PRIMARY KEY (`rank_top`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `squdian_bonus_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(60) NOT NULL DEFAULT '',
  `type_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `send_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `deleted` int(1)  NOT NULL DEFAULT '0',
  `min_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `max_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `send_start_date` int(11) NOT NULL DEFAULT '0',
  `send_end_date` int(11) NOT NULL DEFAULT '0',
  `use_start_date` int(11) NOT NULL DEFAULT '0',
  `use_end_date` int(11) NOT NULL DEFAULT '0',
  `min_goods_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_bonus_user` (
  `bonus_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bonus_sn` varchar(20) DEFAULT '' NOT NULL,
  `openid` varchar(50) DEFAULT '' NOT NULL,
  `deleted` int(1)  NOT NULL DEFAULT '0',
  `isuse` int(1)  NOT NULL DEFAULT '0',
  `used_time` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `collect_time` int(10) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`bonus_id`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_bonus_good` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_type_id` mediumint(8) NOT NULL ,
  `good_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `squdian_alipay_rule` (
  `url` varchar(500) NOT NULL,
  `thumb` varchar(60) NOT NULL,
  `keywords` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `ruletype` int(11) NOT NULL COMMENT '1文本回复 2图文回复',
  `content` text,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_alipay_alifans` (
  `createtime` int(10) NOT NULL DEFAULT '0',
  `openid` varchar(50) DEFAULT NULL,
  `alipay_openid` varchar(50) NOT NULL,
  `follow` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否订阅',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(200) NOT NULL DEFAULT '',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别(0:保密 1:男 2:女)',
  PRIMARY KEY (`alipay_openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_shop_dispatch_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dispatchid` int(11) NOT NULL,
  `country` varchar(30) NOT NULL,
  `provance` varchar(30) DEFAULT '',
  `city` varchar(30) DEFAULT '',
  `area` varchar(30) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `squdian_dispatch` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(120) NOT NULL DEFAULT '',
  `sendtype` int(5) NOT NULL DEFAULT '1' COMMENT '0为快递，1为自提',
  `desc` text NOT NULL,
  `configs` text NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
if(!mysqld_fieldexists('modules', 'isdisable')) {
	$sql=$sql."ALTER TABLE ".table('modules')." ADD COLUMN `isdisable` int(1) DEFAULT '0' COMMENT '模块是否禁用';";
}
if(!mysqld_fieldexists('member', 'avatar')) {
	$sql=$sql."ALTER TABLE ".table('member')." ADD COLUMN `avatar` varchar(200) DEFAULT '' COMMENT '用户头像';";
}
if(!mysqld_fieldexists('member', 'experience')) {
	$sql=$sql."ALTER TABLE ".table('member')." ADD COLUMN `experience` int(11) DEFAULT '0' COMMENT '账户经验值';";
	$sql = $sql."update ".table('member')." set experience=credit;";
}

if(!mysqld_fieldexists('shop_goods', 'isfirst')) {
	$sql=$sql."ALTER TABLE ".table('shop_goods')." ADD COLUMN `isfirst` int(1) DEFAULT '0' COMMENT '首发';";
}

if(!mysqld_fieldexists('shop_goods', 'isjingping')) {
	$sql=$sql."ALTER TABLE ".table('shop_goods')." ADD COLUMN `isjingping` int(1) DEFAULT '0' COMMENT '精品';";
}

if(!mysqld_fieldexists('shop_order', 'hasbonus')) {
	$sql=$sql."ALTER TABLE ".table('shop_order')." ADD COLUMN `hasbonus` int(1) DEFAULT '0' COMMENT '是否使用优惠券';";
}
if(!mysqld_fieldexists('shop_order', 'bonusprice')) {
	$sql=$sql."ALTER TABLE ".table('shop_order')." ADD COLUMN `bonusprice` decimal(10,2) DEFAULT '0.00' COMMENT '优惠券抵消金额';";
}
if(!mysqld_fieldexists('member_paylog', 'account_fee')) {
	$sql=$sql."ALTER TABLE ".table('member_paylog')." ADD COLUMN `account_fee` decimal(10,2) NOT NULL COMMENT '账户剩余积分或余额';";
}

if(!mysqld_fieldexists('member_paylog', 'account_fee')) {
	$sql=$sql."ALTER TABLE ".table('member_paylog')." ADD COLUMN `account_fee` decimal(10,2) NOT NULL COMMENT '账户剩余积分或余额';";
}

if(!mysqld_fieldexists('shop_order_goods', 'iscomment')) {
	$sql=$sql."ALTER TABLE ".table('shop_order_goods')." ADD COLUMN `iscomment` int(1) DEFAULT '0' COMMENT '是否已评论0否1是';";
}

if(!mysqld_fieldexists('member', 'outgold')) {
	$sql=$sql."ALTER TABLE ".table('member')." ADD COLUMN `outgold` double NOT NULL DEFAULT '0' COMMENT '已提取余额';";
}
if(!mysqld_fieldexists('member', 'outgoldinfo')) {
	$sql=$sql."ALTER TABLE ".table('member')." ADD COLUMN `outgoldinfo` varchar(1000) DEFAULT '0' COMMENT '提款信息 序列化';";
}
mysqld_batch($sql); 
clear_theme_cache();

if(intval(SYSTEM_VERSION)<=20150724)
{
 	mysqld_update('shop_dispatch', array('express'=>'cac'),array('sendtype'=>1,'express'=>''));
	$shop_dispatchs = mysqld_selectall("select * from ".table('shop_dispatch')."  group by express");
	foreach($shop_dispatchs as $shop_dispatch)
	{
		$express_code=$shop_dispatch['express'];
			$shop_dispatch_area = mysqld_select("SELECT * FROM " . table('shop_dispatch_area') . " WHERE  dispatchid=:dispatchid and provance=:provance and city=:city and area=:area  limit 1", array('dispatchid'=>$shop_dispatch['id'],'country'=>'中国','provance'=>$shop_dispatch['provance'],'city'=>$shop_dispatch['city'],'area'=>$shop_dispatch['area']));
	 	if(empty($shop_dispatch_area['id']))
	 	{
		  mysqld_insert("shop_dispatch_area", array('dispatchid'=>$shop_dispatch['id'],'country'=>'中国','provance'=>$shop_dispatch['provance'],'city'=>$shop_dispatch['city'],'area'=>$shop_dispatch['area']));
		}						 	
		
		$dispatch = mysqld_select("SELECT * FROM " . table('dispatch') . " WHERE  code=:code limit 1", array(':code' =>$express_code));
	         
		if(empty($dispatch['id']))
		{
			if(file_exists(WEB_ROOT.'/system/modules/plugin/dispatch/'.$express_code.'/lang.php'))
			{
				require WEB_ROOT.'/system/modules/plugin/dispatch/'.$express_code.'/lang.php';
				      				 $data = array(
	                    'code' => $express_code,
	                    'name' => $_LANG['dispatch_'.$express_code.'_name'],
	                    'desc' => $_LANG['dispatch_'.$express_code.'_desc'],
	                    'enabled' => '1',
	                   'sendtype' => $_LANG['dispatch_'.$express_code.'_sendtype']
	                  );
										 mysqld_insert('dispatch', $data);
										 
										 
										 
										 
			}
			
		}
	 	
	}
}