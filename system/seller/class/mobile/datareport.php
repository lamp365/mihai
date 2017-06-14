<?php
namespace seller\controller;
use  seller\controller;

class datareport extends base
{
	public function index()
	{
		$_GP = $this->request;
		$memInfo = get_member_account();
		$dataService     = new \service\seller\datareportService();
		//获取 已发货的 数据   已经支付的数据  转化率
		$sales_data_info = $dataService->sales_data_info($memInfo['store_sts_id'],'today',$_GP);
		if(!$sales_data_info){
			ajaxReturnData(0,$dataService->getError());
		}
		//获取待收货的金额
		$wait_income = $dataService->wait_income($memInfo['store_sts_id']);
		//访问量占比
		$visted_rate = $dataService->visted_rate($memInfo['store_sts_id']);
		$data = array(
			'sales_data_info'   => $sales_data_info,
			'wait_income'       => $wait_income,
			'visted_rate'       => $visted_rate,
		);
		include page('account/datareport');
	}

	public function ajaxSalesInfo()
	{
		$_GP = $this->request;
		$memInfo = get_member_account();
		$dataService     = new \service\seller\datareportService();
		//获取 已发货的 数据   已经支付的数据  转化率
		$sales_data_info = $dataService->sales_data_info($memInfo['store_sts_id'],$_GP['type'],$_GP);
		if(!$sales_data_info){
			ajaxReturnData(0,$dataService->getError());
		}
		ajaxReturnData(1,'获取成功',$sales_data_info);
	}

}