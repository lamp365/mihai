<?php

$member=get_member_account();
$openid =$member['openid'] ;
$operation = $_GP ['op'];
$returnurl = urldecode($_GP['returnurl']);
switch ($operation)
{
	case 'ajax':
		file_put_contents('img.txt',serialize($_FILES));
		break;
	case 'update':		//更新

		if(validateIdentity($_GP['identity_number'],$_GP['identity_name']))
		{
			$identity_id = (int)$_GP['identity_id'];

			$data = array(
				'identity_number'     => $_GP['identity_number'],
				'identity_name' 	    => $_GP['identity_name'],
				'identity_front_image' => $_GP['identity_front_image'],
				'identity_back_image' => $_GP['identity_back_image'],
				'modifiedtime' 		=> date('Y-m-d H:i:s')
			);
			if(mysqld_update('member_identity', $data,array('openid' =>$openid,'identity_id'=>$identity_id)))
			{
				message('身份证更新操作成功！', refresh(), 'success');
			}
			else{
				message('身份证更新操作失败！', refresh(), 'error');
			}
		}

		break;


	case 'insert':		//新增
		if(validateIdentity($_GP['identity_number'],$_GP['identity_name']))
		{
			mysqld_update('member_identity', array('isdefault' => 0), array( 'openid' => $openid));
			$data = array('openid' 			=> $openid,
				'identity_number' 	=> $_GP['identity_number'],
				'identity_name' 	=> $_GP['identity_name'],
				'isdefault'			=> 1,
				'identity_front_image' => $_GP['identity_front_image'],
				'identity_back_image' => $_GP['identity_back_image'],
				'createtime' 		=> date('Y-m-d H:i:s'),
				'modifiedtime' 		=> date('Y-m-d H:i:s')
			);
			if(mysqld_insert('member_identity', $data))
			{
				message('身份证操作成功！', refresh(), 'success');
			}
			else{
				message('身份证操作失败！', refresh(), 'error');
			}
		}

		break;

	case 'edit':		//编辑页显示

		$identity_id = (int)$_GP['identity_id'];

		$identity = mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE openid = :openid and identity_id=:identity_id", array(':openid' => $openid,':identity_id'=>$identity_id));

		//身份证信息不存在时
		if(empty($identity))
		{
			header("location:" . create_url('mobile', array(
					'name' => 'shopwap',
					'do' => 'identity'
				)));
		}

		break;

	case 'remove':		//删除

		$identity_id = (int)$_GP['identity_id'];
		$identity = mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE openid = :openid and identity_id=:identity_id", array(':openid' => $openid,':identity_id'=>$identity_id));
		if($identity)
		{
			$data_where = array('openid' 		=> $openid,
				'identity_id' 	=> $identity_id
			);
			$data = array( 'status' => 1);
			//$identity_front_image = str_replace(WEBSITE_ROOT,'',$identity['identity_front_image']);
			//$identity_back_image  = str_replace(WEBSITE_ROOT,'',$identity['identity_back_image']);

			if(mysqld_update('member_identity', $data, $data_where))
			{
				/*
                //删除身份证正面图片
                if (!empty($identity['identity_front_image']) && file_exists(SYSTEM_WEBROOT . '/' . $identity['identity_front_image'])) {
                    @unlink(SYSTEM_WEBROOT . '/' . $identity_front_image);
                }
                //删除身份证反面图片
                if (!empty($identity['identity_back_image']) && file_exists(SYSTEM_WEBROOT . '/' . $identity['identity_back_image'])) {
                    @unlink(SYSTEM_WEBROOT . '/' . $identity_back_image);
                }
                */
				message('身份证删除操作成功！', refresh(), 'success');
			}
			else{
				message('身份证删除操作失败！', refresh(), 'error');
			}
		}

		break;
	case 'uploadIdenty':		//下订单后，进行操作身份证提交
		$identity_id = $_GP['id'];
		if(empty($identity_id)){
			die(showAjaxMess('1002','对不起参数有误！'));
		}
		if(empty($_GP['identity_front_image']) || empty($_GP['identity_back_image'])){
			die(showAjaxMess('1002','身份证不能为空！'));
		}

		$info = mysqld_select('select * from '.table('member_identity'). " where identity_id={$identity_id}");
		if(empty($info) || $info['openid'] != $openid){
			die(showAjaxMess(1002,'对不起，该身份记录不存在'));
		}

		$res = mysqld_update('member_identity',array(
			'identity_front_image' =>$_GP['identity_front_image'],
			'identity_back_image' => $_GP['identity_back_image'],
			'modifiedtime' 	=> date('Y-m-d H:i:s')
		),array('identity_id'=>$identity_id)
		);
		if($res){
			die(showAjaxMess(200,'上传成功！'));
		}else{
			die(showAjaxMess(1002,'对不起，操作失败！'));
		}
		break;
	case 'default':		//设置默认身份证
		mysqld_update('member_identity', array('isdefault' => 0), array('openid' =>$openid));
		$data = array('isdefault' 	=> 1,
			'modifiedtime' 	=> date('Y-m-d H:i:s'));
		mysqld_update('member_identity', $data,array('openid' =>$openid,'identity_id'=>intval($_GP['identity_id'])));
		message('默认身份证设置操作成功！', refresh(), 'success');
		break;
	default:
		break;
}


$arrIdentity = mysqld_selectall("SELECT * FROM " . table('member_identity') . " WHERE openid = :openid and status = 0 ", array(':openid' => $openid));

include_once themePage ( 'identity' );


/**
 * 表单验证
 *
 * @param unknown $idNum  身份证号码
 * @param $identity_name  身份证姓名
 * @return boolean
 */
function validateIdentity($idNum,$identity_name) {

	$objValidator = new Validator();

	if($idNum=='')
	{
		message('请输入您的身份证号码！', '', 'error');
		return false;
	}
	//身份证验证
	elseif(!$objValidator->identityNumberValidator($idNum))
	{
		message('身份证格式不正确！', refresh(), 'error');
		return false;
	}

	if($identity_name=='')
	{
		message('请输入您的身份证姓名！', refresh(), 'error');
		return false;
	}
	elseif (!preg_match('/^[\x{4e00}-\x{9fa5}]{2,20}$/u', $identity_name)) {

		message('身份证姓名必须为中文，2-20个字符！', refresh(), 'error');
		return false;
	}

	return true;
}
	