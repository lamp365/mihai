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

    $id = '';
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
        }
    }
    if(empty($id)){
        //该操作没有录入系统中的话，都可以看
        return true;
    }
//    file_put_contents('sql.txt',"{$modname}-{$moddo}-{$modop}-{$id}\n\r",FILE_APPEND);
    $hasPower = false;
    $relation = mysqld_select("select rolers_id from ".table('rolers_relation')." where uid={$_SESSION['account']['id']}");
    if(empty($relation))
        return checkAdmin();

    $user_rule = mysqld_select("select * from ". table('rolers') ." where id={$relation['rolers_id']}");
    if(!empty($user_rule['rule'])){   //空的话，说明该用户一个规则都没有设置，不可以查看
        $rule_arr = explode(',',$user_rule['rule']);
        if(in_array($id,$rule_arr)){
            //能找到的  就是要禁止不给看的
            $hasPower = false;
        }

    }else{
        $hasPower = checkAdmin();
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

    $relation = mysqld_select("select rolers_id from ".table('rolers_relation')." where uid={$_SESSION['account']['id']}");
    if(empty($relation))
        return checkAdmin();

    $fieldRule = mysqld_select("select * from ". table('rolers') ." where id={$relation['rolers_id']}");
    $hasPower = true;
    if(!empty($fieldRule['db_rule'])){
        $file_rule = json_decode($fieldRule['db_rule'],true);
        if(array_key_exists($table,$file_rule)){
            $fild_arr = $file_rule[$table];
            if(in_array($filed,$fild_arr)){
                //能找到，说明设置了，设置了反而是限制操作不让显示
                $hasPower = false;
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
            $memcache->set('SYSTEM_RULES',$system_rule,3600*7);
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


/**
 * @return bool
 * 用于左侧菜单，对于管理员root,全部可见。
 */
function checkAdmin(){
    $username     =	$_SESSION['account']['username'];
    if($username == 'root'){
        return true;
    }else{
        return false;
    }
}

/**
 * @param $uid
 * @return mixed|string
 * @content 根据用户id 获取对应的权限规则数组 或者没有返回空
 */
function getAdminHasRule($uid)
{
    if (empty($uid)) {
        return '';
    }
    $relation = mysqld_select("select rolers_id from ".table('rolers_relation')."  where uid={$uid}");
    if(empty($relation)){
        return '';
    }

    $rule = mysqld_select("select rule from  ".table('rolers')." where id={$relation['rolers_id']}");
    if(!empty($rule['rule'])){
        if(class_exists('Memcached')){
            $mem_key  = 'ADMIN_HAS_SYSTEM_RULES_'.$relation['rolers_id'];
            $memcache = new Mcache();
            $rule_arr = $memcache->get($mem_key);
            if(empty($rule_arr)){
                $rule_arr = mysqld_selectall("select * from ".table('rule')." where id in ({$rule['rule']}) order by cat_id asc,sort asc,id asc");
                $memcache->set($mem_key,$rule_arr,3600*7);
            }
        }else{
            $rule_arr = mysqld_selectall("select * from ".table('rule')." where id in ({$rule['rule']}) order by cat_id asc,sort asc,id asc");
        }
        return $rule_arr;
    }else{
        return '';
    }
}

//清空该管理员所拥有的权限规则
//参数为角色id
function cleanAdminHasRule($role_id){
    $mem_key  = 'ADMIN_HAS_SYSTEM_RULES_'.$role_id;
    if(class_exists('Memcached')){
        $memcache = new Mcache();
        $memcache->delete($mem_key);
    }

}

function diffUserRule($allRule,$userHasRule){
    if(empty($userHasRule)){
        //不用去除
        return $allRule;
    }
    $data_rule = array();
    foreach($allRule as $one){
        $data_rule[$one['id']] = $one;
    }

    foreach($userHasRule as $item){
        //用户有的权限 就是需要禁止的，那么从所有的节点中去除掉
        if(array_key_exists($item['id'],$data_rule)){
            unset($data_rule[$item['id']]);
        }
    }
    return $data_rule;
}

/**
 * 卖家是否有权限操作 一些其他的 字段权限  如金额修改  上下架修改
 * @param $field
 * @return bool
'status'     '修改上架',
'price'      '修改价格',
'commision'  '修改商品佣金',
 */
function sellerIsCanOperateField($field){
    if(empty($field)){
        return true;
    }
    $member = get_member_account();
    if($member['store_is_admin']){
        //管理员直接可以操作
        return true;
    }
    //获取该用户所属于的 权限分组
    $file_res = mysqld_select("select group_id from ".table('seller_rule_relation')." where sts_id={$member['store_sts_id']}  and openid='{$member['openid']}'");
    if(empty($file_res)){
        return true;
    }
    //更具group_id找到 对应的other_rule
    $other_rule = mysqld_select("select other_rule from ".table('seller_group')." where group_id={$file_res['group_id']}");
    if(empty($other_rule['other_rule'])){
        return true;
    }

    $rule_arr = explode(',',$other_rule['other_rule']);
    //能找到的说明是 需要被禁止操作的
    if(in_array($field,$rule_arr)){
        return false;
    }
    return true;
}

/**
 * @return int 1最高管理员 2店长  3店员
 * 店员一修改价格就会自动下架掉
 */
function checkSellerRoler()
{
    $member = get_member_account();
    if(!empty($member['store_is_admin'])){
        return 1;
    }
    $sts_id = $member['store_sts_id'];
    $openid = $member['openid'];
    $relation = mysqld_select("select group_id from ".table('seller_rule_relation')." where sts_id={$sts_id} and openid='{$openid}'");
    if(empty($relation)){
        //万一找不到  直接编辑的产品下架
        return 3;
    }
    //分组1 代表店长  分组2 代表 店员
    if($relation['group_id'] == 1){
        return 2;
    }else{
        return 3;
    }
}

/**
 * 检测当前是否还可以上架商品。根据商铺等级
 * 不可以再上架了返回false  否则返回还可以上架的个数
 * @param $curt_dish_num
 * @return bool
 */
function checkMakeDishStatusNum($curt_dish_num)
{
    $member = get_member_account();
    $sts_id = $member['store_sts_id'];
    $store  = member_store_getById($sts_id,'sts_shop_level');
    $rank_level = intval($store['sts_shop_level']);
    $level  = mysqld_select("select dish_num from ".table('store_shop_level')." where rank_level={$rank_level}");
    if($curt_dish_num <= $level['dish_num']){
        return $level['dish_num'] - $curt_dish_num;
    }else{
        return false;
    }
}