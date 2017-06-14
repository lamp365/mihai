<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/10/19
 * Time: 18:01
 */
$op = empty($_GP['op']) ? 'list' : $_GP['op'];

switch($op){
    case 'list':
        $bank = mysqld_selectall("select * from ". table('bank_img'));
        include page('bank_list');
        break;
    case 'edit':
    case 'add':
        if(checksubmit('submit')){
            if(empty($_GP['bank']))
                message("请选择银行！",refresh(),'error');
            if(empty($_FILES['card_icon']))
                message("请上传图标！",refresh(),'error');
            if(empty($_FILES['card_bg']))
                message("请上传背景图！",refresh(),'error');

            $upload = file_upload($_FILES['card_icon']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $card_icon = $upload['path'];

            $upload = file_upload($_FILES['card_bg']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $card_bg = $upload['path'];

            $data = array(
                'bank'       => $_GP['bank'],
                'card_icon'  =>$card_icon,
                'card_bg'    => $card_bg,
                'bg_color'   => $_GP['bg_color'],
            );
            if(!empty($_GP['id'])){
                mysqld_update('bank_img', $data, array('id'=>$_GP['id']));
                $url = web_url('bank',array('op'=>'list'));
                message('修改成功！',$url);
            }else{
                mysqld_insert("bank_img",$data);
                if(mysqld_insertid()){
                    $url = web_url('bank',array('op'=>'list'));
                    message('添加成功！',$url);
                }else{
                    message('添加失败',refresh(),'error');
                }
            }

        }else{
            $bank = array();
            if(!empty($_GP['id'])){
                $bank = mysqld_select('select * from '. table('bank_img') ." where id={$_GP['id']}");
            }
            $select_bank = get_all_bank();
            include page('bank_add');
        }
        break;

    case 'delete':
        $id = $_GP['id'];
        if(empty($id)){
            message('对不起参数有误！',refresh(),'error');
        }else{
            mysqld_delete('bank_img',array('id'=>$id));
            message("删除成功！",refresh());
        }
        break;
    case 'setting':
        $sett = mysqld_selectall("SELECT * FROM ".table('config')." WHERE name IN ('com_gold','credit_ratio','com_credit','teller_limit')");
        include page('bank_setting');
        break;
    case 'post':
        $com_gold = $_GP['set_1'];
        $credit_ratio = $_GP['set_2'];
        $com_credit = $_GP['set_3'];
        $teller_limit = $_GP['set_4'];
        $com_gold = (float)$com_gold/100;

        $have_1 = mysqld_select("SELECT name FROM ".table('config')." WHERE name='com_gold'");
        if (!empty($have_1)) {
            mysqld_update('config',array('value' => $com_gold),array('name' => 'com_gold'));
        }else{
            mysqld_insert('config',array('value' => $com_gold,'name' => 'com_gold'));
        }

        $have_2 = mysqld_select("SELECT name FROM ".table('config')." WHERE name='credit_ratio'");
        if (!empty($have_2)) {
            mysqld_update('config',array('value' => $credit_ratio),array('name' => 'credit_ratio'));
        }else{
            mysqld_insert('config',array('value' => $credit_ratio,'name' => 'credit_ratio'));
        }

        $have_3 = mysqld_select("SELECT name FROM ".table('config')." WHERE name='com_credit'");
        if (!empty($have_3)) {
            mysqld_update('config',array('value' => $com_credit),array('name' => 'com_credit'));
        }else{
            mysqld_insert('config',array('value' => $com_credit,'name' => 'com_credit'));
        }

        $have_4 = mysqld_select("SELECT name FROM ".table('config')." WHERE name='teller_limit'");
        if (!empty($have_4)) {
            mysqld_update('config',array('value' => $teller_limit),array('name' => 'teller_limit'));
        }else{
            mysqld_insert('config',array('value' => $teller_limit,'name' => 'teller_limit'));
        }
        message("设置成功！",refresh());
        break;
}