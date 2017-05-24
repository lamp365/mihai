<?php
$operation = !empty($_GP['op']) ? $_GP['op'] : 'index';
$objValidator   = new Validator();

if ($operation == 'index') {
    include page('interview');
}elseif ($operation == 'add') {
    if (empty($_GP['new_name'])) {
        message('姓名不能为空!',refresh(),'error');
    }
    if (!$objValidator->is($_GP['new_mobile'],'mobile')) {
        message('请输入正确的手机号!',refresh(),'error');
    }
    if (empty($_GP['new_job'])) {
        message('职位不能为空!',refresh(),'error');
    }
    if (empty($_GP['new_experience'])) {
        $experience = '0';
    }else{
        $experience = $_GP['new_experience'];
    }
    if (empty($_GP['new_hope_pay'])) {
        $hope_pay = '0';
    }else{
        $hope_pay = $_GP['new_hope_pay'];
    }
    if (empty($_GP['new_result'])) {
        $result = '0';
    }else{
        $result = $_GP['new_result'];
    }

    mysqld_insert("job",array('name'=>$_GP['new_name'],'mobile'=>$_GP['new_mobile'], 'job'=>$_GP['new_job'], 'experience'=>$experience, 'hope_pay'=>$hope_pay, 'result'=>$result));
    message('添加成功！', refresh(), 'success');
}elseif ($operation == 'edit') {
    $id = $_GP['id'];
    if (empty($id)) {
        message('该记录异常!',refresh(),'error');
    }
    if (empty($_GP['man_name'])) {
        message('姓名不能为空!',refresh(),'error');
    }
    if (!$objValidator->is($_GP['mobile'],'mobile')) {
        message('请输入正确的手机号!',refresh(),'error');
    }
    if (empty($_GP['job'])) {
        message('职位不能为空!',refresh(),'error');
    }
    if (empty($_GP['experience'])) {
        $experience = '0';
    }else{
        $experience = $_GP['experience'];
    }
    if (empty($_GP['hope_pay'])) {
        $hope_pay = '0';
    }else{
        $hope_pay = $_GP['hope_pay'];
    }
    if (empty($_GP['result'])) {
        $result = '0';
    }else{
        $result = $_GP['result'];
    }

    mysqld_update("job",array('name'=>$_GP['man_name'],'mobile'=>$_GP['mobile'], 'job'=>$_GP['job'], 'experience'=>$experience, 'hope_pay'=>$hope_pay, 'result'=>$result),array('id'=>$id));
    message('修改成功！', refresh(), 'success');
}elseif ($operation == 'del') {
    $id = $_GP['id'];

    if (!empty($id)) {
        mysqld_delete("job",array('id'=>$id));
        message('删除成功！', refresh(), 'success');
    }else{
        message('删除失败！', refresh(), 'error');
    }
}elseif ($operation == 'check') {
    $mobile = addslashes($_GP['mobile']);
    if (!empty($mobile)) {
        $re = mysqld_select("SELECT id, result, name, job FROM ".table('job')." WHERE mobile=".$mobile);
        if (!empty($re)) {
            if ($re['result'] == '0') {
                include page('interview_wait');
            }elseif ($re['result'] == '1') {
                include page('interview_result');
            }elseif ($re['result'] == '2') {
                include page('interview_fail');
            }else{
                message('找不到手机号，请检查输入是否正确！', refresh(), 'error');
            }
        }else{
            message('找不到手机号，请检查输入是否正确！', refresh(), 'error');
        }
    }else{
        message('找不到手机号，请检查输入是否正确！', refresh(), 'error');
    }
}elseif ($operation == 'admin_dex') {
    $all_man = mysqld_selectall("SELECT * FROM ".table('job')." ORDER BY id DESC");
    $result_ary = array(
        "0" => '等待结果',
        "1" => "录用",
        "2" => "未录用"
        );
    include page('logging_data');
}
