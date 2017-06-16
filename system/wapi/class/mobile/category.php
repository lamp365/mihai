<?php
/**
 *分类接口
 */

namespace wapi\controller;
class category extends base{

    public function index()
   {
       $_GP = $this->request;
       $info = $this->getIndustry();
       ppd($info);
   }
   

}