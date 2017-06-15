<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class shopPriceService extends \service\publicService {
    private $memberData;
    private $table;
    private  $goodstype;
    private  $shopdish;
    
    function __construct() {
       $this->memberData      = get_member_account();
       $this->table           = table('shop_dish');
       $this->table_price     = table('dish_spec_price');
       $this->goodstype       = new \service\seller\goodstypeService();    //规格操作对象
       $this->shopdish        = new \service\seller\ShopDishService();   //宝贝操作对象
   }
   
   public function deletePrice($data){
        $delStatus = $this->goodstype->delete_completely($data['item_id']);
            
        //通过gtypeid获取对应的宝贝id
       if($delStatus > 0)
       {
           //通过gtypeid获取对应的宝贝id
           $dishIdArr = $this->shopdish->getSpecDish($data['gtype_id']);
           $dishIds   = '';

           foreach($dishIdArr as $v)
           {
               $dishIds .= $v['id'].',';
           }
           $dishIds = rtrim($dishIds,',');

           //获取可能受影响的价格项
           $dishPriceData = $this->shopdish->dishPrice($dishIds);
           $ids = '';
           if(is_array($dishPriceData) && count($dishPriceData) > 0){
            foreach($dishPriceData as $v){
                $spec_key_arr = array();
                $spec_key_arr = explode('_', $v['spec_key']);
                foreach($spec_key_arr as $vv)
                {
                    if($vv == $data['item_id'])
                    {
                      $ids .= $v['id'].',';  
                    }
                }
            }
            $ids = rtrim($ids,',');

            if($ids != '')
            {
             $rs = $this->shopdish->deleteDishPrice($ids);
            }
           }
           
       }
       else{
           $rs = 0;
       }
       return $rs;
   }
   
   
   
} 
?>