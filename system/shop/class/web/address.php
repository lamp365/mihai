<?php
namespace shop\controller;

class address extends \common\controller\basecontroller
{
    public function index()
    {
        $_GP = $this->request;
        include page('yunfei/address_list');
    }
}


