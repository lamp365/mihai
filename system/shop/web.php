<?php
defined('SYSTEM_IN') or exit('Access Denied');
class shopAddons  extends BjSystemModule {
		public function do_noticemail()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_exchange_rate()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_adv()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_brand()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_country()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_taxs()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_thirdlogin()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_hottopic()
	{
        $this->__web(__FUNCTION__);
	}
	public function do_zhifu()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_dispatch()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_config()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_orderbat()
	{	
		$this->__web(__FUNCTION__);
	}
	public function do_order()
	{
    $this->__web(__FUNCTION__);
	}
	public function do_goods()
	{
		$this->__web(__FUNCTION__);
	}
		public function do_goods_comment()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_upload()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_disharea()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_category()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_mess(){
        $this->__web(__FUNCTION__);
	}
	public function do_dish(){
        $this->__web(__FUNCTION__);
	}
	public function do_area(){
        $this->__web(__FUNCTION__);
	}
	public function do_mess_area(){
        $this->__web(__FUNCTION__);
	}
	public function do_spec() {
		$this->__web(__FUNCTION__);
 	}
	public function do_picdelete() {
		$this->__web(__FUNCTION__);
	}
	public function do_specitem() {
		$this->__web(__FUNCTION__);
	}
	public function do_good_setting()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_bank(){
		$this->__web(__FUNCTION__);
	}
	
	public function do_app_version(){
		$this->__web(__FUNCTION__);
	}

	public function do_groupbuy(){
		$this->__web(__FUNCTION__);
	}

	public function do_app_banner(){
		$this->__web(__FUNCTION__);
	}
	public function do_purchase(){
		$this->__web(__FUNCTION__);
	}
	
	public function do_note(){
		$this->__web(__FUNCTION__);
	}
	
	public function do_app_video(){
		$this->__web(__FUNCTION__);
	}
	
	public function do_headline(){
		$this->__web(__FUNCTION__);
	}
	
	public function do_app_weixin(){
		$this->__web(__FUNCTION__);
	}

	public function do_img_mange(){
		$this->__web(__FUNCTION__);
	}
	
	public function do_app_topic(){
		$this->__web(__FUNCTION__);
	}
	
	public function do_app_topic_banner(){
		$this->__web(__FUNCTION__);
	}
	

  	public function setOrderCredit($openid,$id , $minus = true,$remark='') {
  	 			$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id='{$id}'");
       		if(!empty($order['credit']))
       		{
            if ($minus) {
            	member_credit($openid,$order['credit'],'addcredit',$remark);
                
            } else {
               member_credit($openid,$order['credit'],'usecredit',$remark);
            }
          }
    }
	// 建议不再去用了，该方法要处理的业务，已经没有价值了
    public function setOrderStock($id , $minus = true) {
    	updateOrderStock($id,$minus);
    }
   /***
    * 已经没用了，可以删掉
	public function oneSetOrderStock($id , $minus = true) {
		oneUpdateOrderStock($id,$minus);
	}*/
}


