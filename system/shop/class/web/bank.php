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
}