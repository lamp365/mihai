<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/11/30
 * Time: 18:39
 */
$op = $_GP['op'];

switch($op){
    case 'ajax_keyword':   //模糊匹配自动完成搜索提示
        $dish = '';
        if(!empty($_GP['keyword'])){
            $sql = "select title from ".table('shop_dish')." where title LIKE '%{$_GP['keyword']}%' limit 5";
            $dish = mysqld_selectall($sql);
        }
       if(empty($dish)){
           $info = showAjaxMess(1002,'没有找到！');
       }else{
		   foreach( $dish as &$value ){
                $value  =  str_replace($_GP['keyword'], '<span style="color:red;">'.$_GP['keyword'].'</span>',$value);
		   }
		   unset($value);
           $info = showAjaxMess(200,$dish);
       }
	   echo $info;
	   exit;
    break;
}