<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace shopwap\controller;
use  shopwap\controller;

class outchargegold{

	//这个值等价于$_GP
	public $request = '';

	public function __construct()
	{
		if (!checkIsLogin()){
			header("location:" . to_member_loginfromurl());
		}

	}

	public function index()
	{
		$_GP = $this->request;
		$member = get_member_account();
		$openid = $member['openid'];
		$member = member_get($openid);
		$Service    = new \service\shopwap\accountService();
		$bank_list  = $Service->get_bank_list();

		if(empty( $bank_list['all']))
		{
			message('请设置您的提款账户！',mobile_url('account'),'error');
		}

		if (!strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
			//如果不是微信端操作，直接提示 体现流程
			/*if(is_mobile_request()){
                include themePage('member/outcharge_guide');
                exit;
            }else{
                include themePage('member/outchargegold');
                exit;
            }*/

		}

		if (checksubmit('submit')) {
			$service = new \service\shopwap\accountService();
			$res   = $service->do_outgold($_GP);
			if($res){
				message("系统正在审核中",mobile_url('fansindex'),'success');
			}else{
				message($service->getError(),refresh(),'error');
			}
		}


		$applygold = mysqld_selectcolumn("select sum(fee) from ".table("gold_teller")." where status=0 and openid=".	$openid);
		$outgold   = mysqld_selectcolumn("select sum(fee) from ".table("gold_teller")." where status=1 and openid=".	$openid);

		$applygold  = empty($applygold)? 0 : $applygold;
		$outgold    = empty($outgold) ? 0 : $outgold;
		include themePage('member/outchargegold');
	}

	public function history()
	{
		$_GP   = $this->request;
	   $pindex = max(1, intval($_GP['page']));
	   $psize  = 20;
	   $list   = mysqld_selectall("select * from ".table("gold_teller")." where openid=:openid order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize ,array(":openid"=>$openid));
	   $total  = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('gold_teller') . " where  openid=:openid ",array(":openid"=>$openid));
	   $pager  = pagination($total, $pindex, $psize);

		include themePage('member/outchargegold_history');
	}
}