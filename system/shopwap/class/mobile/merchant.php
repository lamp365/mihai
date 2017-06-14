<?php
if (checksubmit('submit')) {
	 if ( empty($_GP['qymc']) || empty($_GP['mobile']) || empty($_GP['xspt']) || empty($_GP['zyxl']) || empty($_GP['yyzz'] ) || empty($_GP['frsf'] ) ){
          message('请正确填写信息','','error');
	 }
     $data = array(
         'info'=>serialize($_GP)
	 );
	 if (! empty($_FILES['license']['tmp_name'])) {
            $upload = file_upload($_FILES['license']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['license'] = $upload['path'];
    }
	if (! empty($_FILES['identityA']['tmp_name'])) {
            $upload = file_upload($_FILES['identityA']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['identityA'] = $upload['path'];
    }
	if (! empty($_FILES['identityB']['tmp_name'])) {
            $upload = file_upload($_FILES['identityB']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['identityB'] = $upload['path'];
    }
	mysqld_insert('merchant', $data);
	message('提交成功,我们将在7个工作日内审核并联系', 'refresh', 'success');
}
include themePage('merchant');