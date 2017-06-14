<?php
namespace seller\controller;
use  seller\controller;

class shopuser extends base
{
    private $shopPic            = array();
    private $member          = array();
    private $relation          = array();
    public function __construct()
    {
        parent::__construct();
        
        $this->member  = new \service\seller\memberService();     //
    }
    
    public function index(){
        $_GP = $this->request;
        
        $data = $this->member->getMemberLists($_GP);
        
        include page('shopruler/shopuser_list');
    }
    
}
?>