<?php
/*
check集合
*/

// 检查提交
function checksubmit($action = 'submit')
{
    global $_CMS, $_GP;
    if (empty($_GP[$action])) {
        return FALSE;
    }
    if ((($_SERVER['REQUEST_METHOD'] == 'POST') && (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
        return TRUE;
    }
    return FALSE;
}

// 检查登陆
function checklogin()
{
    global $_CMS;
    if (($_CMS['module'] != 'public') && empty($_CMS['account'])) {
        message('会话已过期，请先登录！', create_url('site', array(
            'name' => 'public',
            'do' => 'logout'
        )), 'error');
    }
    return true;
}

// 检查权限
function checkrule($modname, $moddo, $modop)
{
    global $_CMS;
    $system_rule = getSystemRule();
    //正对于产品库，中如果还没上架，则不用判断权限，只有上架后才判断权限。

    if(!empty($_GET['id']) && $moddo == 'goods'){
        $res = mysqld_select("select id,status from ". table('shop_goods') ." where id={$_GET['id']}");
        if($res['status'] == 0)
            return true;
    }
    /**
     * 正确做法 A 根据模块方法名等找规则 rule
     * B 根据用户找规则id rule_id
     * C 进行id比对
     * 由于这里A情况由于架构逻辑的问题，很多地址模块名和方法名都一样会找出多条记录，故，不按常理做法走
     */
    $hasPower = false;
    $tmpdata  = array();
    $userIsHasRule = getAdminHasRule($_CMS['account']['id']);
    if(!empty($userIsHasRule)){
        foreach ($system_rule as $item) {
            if ($item['modname'] == $modname && $item['moddo'] == $moddo && $item['modop'] == $modop) {
                $tmpdata[] = $item;
            }
        }
        $hasPower = findOneRule($tmpdata,$userIsHasRule);

    }else{   //一条规则都没有则还没设置，默认都可以操作
        $hasPower = true;
    }
    return $hasPower;
}
/**
 * 从禁止中的url中，能找到的说明时候被禁止的 返回false
 * @param $tmpdata
 * @param $userIsHasRule
 * @return bool
 */
function findOneRule($tmpdata,$userIsHasRule)
{
    $hasPower = true;
    if(count($tmpdata) == 0){   //如果没有找到对应的默认可以看
        $hasPower = true;
    }else if(count($tmpdata) > 1){  //可能有添加和编辑  地址是一样的 只是多了一个id
        if(empty($_GET['id'])){  //是添加
            foreach($tmpdata as $key => $val){
                if($val['act_type'] != 'add')   //不是添加的都去掉
                    unset($tmpdata[$key]);
            }
        }else{
            foreach($tmpdata as $key => $val){
                if($val['act_type'] == 'add')    //是添加的去掉
                    unset($tmpdata[$key]);
            }
        }
        sort($tmpdata);  //重置key
    }

    if(!empty($tmpdata)){
        foreach($userIsHasRule as $row){
            if($row['id'] == $tmpdata[0]['id']){
                //记录管理员行为日志
                recoderAdminBehaveLog($tmpdata);
                $hasPower =  false;
            }
        }
    }
    return $hasPower;
}

function hasrule($modname, $moddo, $modop='')
{
    if (checkrule($modname, $moddo, $modop) == false) {
        message("您没有权限操作此功能");
    }
}

/**
 * @content 记录后台管理员的行为日志表
 * @param $ruledata
 */
function recoderAdminBehaveLog($ruledata){
    //获取ip
    $ip   = getClientIP();
    $area = getAreaByIp($ip);
    $name = '';
    if($ruledata[0]['pid']!=0){
        $prule = mysqld_select('select moddescription from '.table('rule')." where id={$ruledata[0]['pid']}");
        $name  = $prule['moddescription'];
    }

    $data = array(
      'name'    => $name,
      'act_id'  => intval($_GET['id']),
      'uid'     => $_SESSION['account']['id'],
      'rolername'=> getAdminRolers($_SESSION['account']['id']),
      'modname' => $ruledata[0]['modname'],
      'moddo'   => $ruledata[0]['moddo'],
      'modop'   => $ruledata[0]['modop'],
      'message' => $ruledata[0]['moddescription'],
      'ip'      => $ip,
      'area'    => $area,
      'createtime'=>time()
    );
    mysqld_insert('admin_behave_log',$data);

}