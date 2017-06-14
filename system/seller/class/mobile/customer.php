<?php
namespace seller\controller;
use api\controller\base;
use  seller\controller;

class customer extends base
{
	public $request = '';
        private $member = '';
                
        public function __construct(){
            parent::__construct();
            
            $this->member  = new \service\seller\memberService();     //
        }

	//订单列表
	public function index()
	{
            $_GP = $this->request;
            
            //$_GP['page'] = 1;
            $data = $this->member->getMemberList($_GP);

            include page('shopruler/customer');
	}    
}