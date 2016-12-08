<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/10/14
 * Time: 11:57
 */
/**
 * @param $menuRule      存放用户可擦做的节点URl，并把分类url也家进去
 * @param $userRule      用于得到上级分类都有哪几个
 * @param string $menuParent    只是获取到用户 user_role中顶级菜单，用于遍历左侧菜单显示
 * @return array  结果返回的menuRule用于存放所有用户可操作节点的url    parentMenuList用于遍历左侧菜单的显示
 */
function getCatRuleUrl($menuRule,$userRule,$menuParent = ''){
    if(empty($userRule)){
        return $menuRule;
    }
    $catIdArr = $parentMenuList = array();
    foreach($userRule as  $rule){
        if(!in_array($rule['cat_id'],$catIdArr) && $rule['cat_id']!=0){
            $catIdArr[] = $rule['cat_id'];   //获取分类id
        }

    }

    if(!empty($menuParent)){
        foreach($menuParent as $row){
            $parentMenuList[$row['cat_id']][] = $row;
        }
    }

    //获取分类的URL
    if(!empty($catIdArr)){
        $cat_url = MenuEnum::$getMenuEnumUrl;
        foreach($catIdArr as $cat_id){
            $menuRule[] = $cat_url[$cat_id];
        }
    }
    return array('menuRule'=>$menuRule,'parentMenuList'=>$parentMenuList);
}


/**
 * @param $allrule
 * @return array
 * 将所有规则按照父类 子类进行分开数组
 */
function getRuleParentChildrenArr($allrule){
    $parent = $children = array();
    if(!empty($allrule)){
        foreach ($allrule as $index => $row) {
            if (! empty($row['pid'])) {
                $children[$row['pid']][$row['id']] = $row;
                unset($allrule[$index]);
            }
        }
        $cat = MenuEnum::$getMenuEnumValues;
        foreach($allrule as $row){
            if(array_key_exists($row['cat_id'],$cat)) {
                $row['cat_name'] = $cat[$row['cat_id']];
                $parent[$row['cat_id']][] = $row;
            }
        }
    }
    return array('parent'=>$parent,'children'=>$children);
}

/**
 * @param $modname
 * @param $moddo
 * @param $modop
 * @param string $act_type
 * @return bool
 * @content 是否有权限显示，对页面上一些编辑删除等是否有权显示
 */
function isHasPowerToShow($modname, $moddo, $modop, $act_type = '',$id=''){
    if(!empty($id) && $moddo == 'goods'){
        //对于产品库，未上架之前的产品都有权限操作。上架之后才判断是否 有权限
        $res = mysqld_select("select id,status from ". table('shop_goods') ." where id={$id}");
        if($res['status'] == 0)
            return true;
    }

    $system_rule = getSystemRule();
    foreach($system_rule as $row){
        if($row['modname'] == $modname && $row['moddo'] == $moddo && $row['modop'] == $modop){
            if(!empty($act_type)){
                if($row['act_type'] == $act_type){
                    $id = $row['id'];
                }
            }else{
                $id = $row['id'];
            }
        }else{
            return true;
        }
    }
//    file_put_contents('sql.txt',"{$modname}-{$moddo}-{$modop}-{$id}\n\r",FILE_APPEND);
    $hasPower = false;
    $user_rule = mysqld_selectall("select id,role_id from ". table('user_rule') ." where uid={$_SESSION['account']['id']}");
    if(!empty($user_rule)){   //空的话，说明该用户一个规则都没有设置，默认可以查看所有的
        foreach($user_rule as $val){
            if($val['role_id'] == $id ){
                $hasPower = true;
            }
        }
    }else{
        $hasPower = true;
    }
    return $hasPower;
}

/**
 * @param $table
 * @param $filed
 * @return bool
 * @content是否有权处理 部分字段
 */
function isHasPowerOperateField($table,$filed,$id=''){
    if(!empty($id) && $table== 'shop_goods'){
        //对于产品库，未上架之前的产品都有权限操作。上架之后才判断是否 有权限
        $res = mysqld_select("select id,status from ". table('shop_goods') ." where id={$id}");
        if($res['status'] == 0)
            return true;
    }
    $uid = $_SESSION['account']['id'];
    $fieldRule = mysqld_selectall("select id,db_name,db_rule from ". table('user_rule') ." where uid={$uid} and menu_db_type=2");
    $hasPower = true;
    if(!empty($fieldRule)){
        foreach($fieldRule as $row){
            if($row['db_name'] == $table){
                $db_rule = json_decode($row['db_rule']);
                if(in_array($filed,$db_rule)){
                    $hasPower = false;
                }
            }
        }
    }
    return $hasPower;
}
/**
 * @return array
 * 获取系统规则 rule
 */
function getSystemRule($uid=''){
    if(class_exists('Memcached')){
        $memcache = new Mcache();
        $system_rule = $memcache->get('SYSTEM_RULES');
        if(empty($system_rule)){
            $system_rule = mysqld_selectall('SELECT * FROM ' . table('rule') ." order by cat_id asc,sort asc,id asc");
            $memcache->set('SYSTEM_RULES',$system_rule,time()+3600*3);
        }
    }else{
        $system_rule = mysqld_selectall('SELECT * FROM ' . table('rule') ." order by cat_id asc,sort asc,id asc");
    }
    
    return $system_rule;
}

function cleanSystemRule(){
    //清空所有的权限规则
    if(class_exists('Memcached')){
        $memcache = new Mcache();
        $memcache->delete('SYSTEM_RULES');
    }

}
/**
 * @return array
 * 返回数据库表以及字段的信息
 */
function getDbTablesInfo($byLocal = true){
    if($byLocal){  //目前只有两张表部分字段，固可以使用数组映射来完成
        $DbFiledList = MenuEnum::$dbFilesRule;
    }else{
        $DbFiledList = mysqld_selectall("SHOW TABLE STATUS");  //获取所有的数据表
        foreach($DbFiledList as $key => $row){
            if($row['Name'] == "squdian_shop_goods" || $row['Name'] == "squdian_shop_dish"){
                //不做任何操作
            }else{
                unset($DbFiledList[$key]);
            }
        }
        foreach($DbFiledList as $key => $row){
            $table = $row['Name'];
            $result = mysqld_selectall("desc {$table}");   //查询所有字段
            $DbFiledList[$key]['showFiled'] = $result;
        }
    }
    return $DbFiledList;
}

function getUserHasDbRule($uid,$hebin=1){
    $result = mysqld_selectall("select * from ". table('user_rule') ." where menu_db_type=2 and uid={$uid}");
    if($hebin && !empty($result)){
        $data = array();
        foreach($result as $row){
            $data[$row['db_name']] = json_decode($row['db_rule']);
        }
        return $data;
    }else{
        return $result;
    }
}