<?php
/**
 *模型层
 *Author:严立超 
 *   
 **/
namespace model;

class StoreShopModel extends \model\publicModel
{
    public function test(){
        $info = $this->db->fetch("select * from ".table('store_shop'));
        ppd($info);
        return $info;
    }
}