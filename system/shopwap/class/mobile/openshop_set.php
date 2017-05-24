<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/22 0022
 * Time: 10:45
 */
/**
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_set&op=show&mark=show  设置首页
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_set&op=logo&mark=show   展示logo页面要修改
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_set&op=logo&mark=post   提交logo修改的数据
 */

$member     = get_member_account(true,true);
//$memberinfo = member_get($member['openid']);

$operation = empty($_GP['op']) ? 'show' : $_GP['op'];
$accesskey = getOpenshopAccessKey($member['openid']);
$shoperWebUrl = getShoperWebUrl($member['openid']);


switch($operation){
    case 'show':
        $shopInfo = mysqld_select("select id,openid,shopname,area,logo,level,mobile,notice,createtime from ". table('openshop') ." where openid=:openid",array(
            'openid' => $member['openid']
        ));
        if(empty($shopInfo))
            message('对不起，您还没有开店！');

        include themePage('openshop_set');
        break;

    case 'post':   //提交修改数据
        $logo = '';
        if($_FILES['logo']['error'] != 4){ //说明有文件
            $result = file_upload($_FILES['logo']);
            if (is_error($result)) {
                message($upload['message'], '', 'error');
            }
            $logo = $result['path'];
        }
        $data = array(
            'shopname'  => $_GP['shopname'],
            'mobile'  => $_GP['mobile'],
            'notice'  => $_GP['notice'],
            'area'  => $_GP['area'],
        );
        if(!empty($logo)){
            $data['logo']  = $logo;
        }
        mysqld_update('openshop',$data,array('openid'=>$member['openid']));
        $redirct = create_url('mobile',array('name'=>'shopwap', 'do'=>'openshop_set'));
        message('修改成功!',$redirct);

        break;

    case 'shopname':  //店铺名
        break;

    default:
        message('对不起，访问有误！','','error');
        break;
}



