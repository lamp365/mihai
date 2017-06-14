<?php
namespace seller\controller;
use  seller\controller;

class account extends base
{
	public $request = '';

	//我的财务
	public function account()
	{
		$_GP = $this->request;
		$memInfo   = get_member_account();
		$storeInfo = member_store_getById($memInfo['store_sts_id']);
		$storeInfo['recharge_money'] = FormatMoney($storeInfo['recharge_money'],0);
		$storeInfo['freeze_money']   = FormatMoney($storeInfo['freeze_money'],0);
		//查看提现审核中的金额
		$tixian_money = mysqld_select("select sum(fee) as money from ".table('gold_teller')." where sts_id={$memInfo['store_sts_id']} and status=0");
		$tixian_money = FormatMoney($tixian_money['money'],0);

		//更具等级获取保证金
		$baozhengjin = mysqld_select("select money from ".table('store_shop_level')." where rank_level={$storeInfo['sts_shop_level']}");
		$baozhengjin = FormatMoney($baozhengjin['money'],'0');

		//待收款合计 也就是未确认收货的
		$order_info = mysqld_select("select count(id) as order_num,sum('price') as price from ".table('shop_order')." where sts_id={$memInfo['store_sts_id']} and status=2");
		$order_info['price'] = FormatMoney($order_info['price'],0);

		$accountService = new \service\seller\accountService();
		//获取20条账单记录
		$paylog_info = $accountService->get_goldrecord($memInfo['store_sts_id']);
		$paylog_data = $paylog_info['paylog'];
		//销售额折线图
		$money_lie    = $accountService->dashborder_money_line($memInfo['store_sts_id']);
		//获取本月的 总支出 与 总收入
		$month_paylog = $accountService->month_paylog_total($memInfo['store_sts_id']);

		include page('account/account');
	}

	//提现详情页
	public function outgold()
	{
		$_GP = $this->request;
		$accountService = new \service\seller\accountService();
		$out_info   = $accountService->outgold();
		//获取法人的 所有银行卡
		$storeService   = new \service\seller\StoreShopService();
		$bank_list      = $storeService->get_bank_list();
		if(!is_array($bank_list)){
			message($storeService->getError(),refresh(),'error');
		}
		if(empty($bank_list['all'])){
			message('请先设置提款账户！',mobile_url('shop',array('op'=>'zhanghu')));
		}
		include page('account/outgold');
	}
	//提现提交
	public function do_outgold()
	{
		$_GP = $this->request;
		$accountService = new \service\seller\accountService();
		$res   = $accountService->do_outgold($_GP);
		if($res){
			message("系统正在审核中",refresh(),'success');
		}else{
			message($accountService->getError(),refresh(),'error');
		}
	}

	public function record()
	{
		$_GP = $this->request;
		//获取提款账单
		$accountService = new \service\seller\accountService();
		$data = $accountService->get_cash_record($_GP);
		include page('account/record');
	}
        

   //商品分析
    public function product_analysis()
	{
		$_GP = $this->request;
		include page('account/product_analysis');
	}
}