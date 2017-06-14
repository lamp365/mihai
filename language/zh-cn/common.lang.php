<?php

/*
  Author: DreamCrush
  命名规范：例common.lang.php则以下都是以COMMON_作为前缀
 */


//**************通用语言包请写在在下面,前缀请以文件名作为规范********************//
$return_result['COMMON_UPDATE_SUCCESS']     = '修改成功';
$return_result['COMMON_ADD_SUCCESS']        = '添加成功';
$return_result['COMMON_DELETE_SUCCESS']     = '删除成功';
$return_result['COMMON_OPERATION_SUCCESS']  = '操作成功';
$return_result['COMMON_OPERATION_FAIL']     = '操作失败';
$return_result['COMMON_PLEASE_LOGIN']       = '请先登录';



//**************短信相关********************//
$return_result['COMMON_SMS_SEND_SUCCESS']       = '验证码已发送成功';
$return_result['COMMON_SMS_SEND_FAIL']          = '验证码发送失败';
$return_result['COMMON_PHONE_ERROR']            = '手机格式有误';
$return_result['COMMON_PHONE_EXIST']            = '该手机号已注册过';
$return_result['COMMON_PHONECODE_ERROR']        = '验证码有误';
$return_result['COMMON_PHONECODE_TIMEOOUT']        = '验证码已经过期';
$return_result['COMMON_SMS_IS_ALREADY_SEND']    = '请不要频繁发送验证码';

//***************登录注册*********************//
$return_result['COMMON_PWD_NOTNULL']            = '密码不能为空';
$return_result['COMMON_SIGNIN_SUCCESS']         = '注册成功';
$return_result['COMMON_SIGNIN_FAIL']            = '注册失败';
$return_result['COMMON_PWD_NOTSAME']            = '密码不一致';
$return_result['COMMON_USER_FORBIDEN']          = '该账号被禁用';
$return_result['COMMON_USER_PWD_ERROR']         = '密码有误';
$return_result['COMMON_USER_NOT_EXIST']         = '用户不存在';
$return_result['COMMON_LOGIN_SUCCESS']          = '登录成功';


//********************验证信息***********************
$return_result['COMMON_NAME_NOTNULL']          = '@@@不能为空';
$return_result['COMMON_NAME_NOTEXIST']         = '@@@不存在';
$return_result['COMMON_PARAME_ERR']            = '参数有误!';
$return_result['COMMON_NOTBE_SELLER']          = '您还不是卖家用户！';

return $return_result;
