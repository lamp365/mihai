<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class ShopSpecService extends \service\publicService {
    private $memberData;
    private $goodstype      = array();
    
    function __construct() {
       parent::__construct();
       $this->memberData        = get_member_account();
       $this->goodstype         = new goodstypeService();
   }
   
   public function specEdit($data){
       if($data['gtypeid'] <= 0)
       {
           $names = '';
           foreach($data['data'] as $v)
           {
               $names .= $v['name'];
           }
           
           //获取默认group_id
           $group_id    = $this->goodstype->checkGroupBeforeAddtype($data);
           
           $goodsArr = array();
           $goodsArr['gtype_name'] = $names;
           $gtypeid = $this->goodstype->add_goodstype($goodsArr,$group_id);
           //$this->memberData->['store_sts_id']
           $data['gtypeid'] = $gtypeid;
       }
       
       
       if(count($data['data']) > 0)
        {
            foreach($data['data'] as $v){
                
                    if($v['editstatus'] == 1 || $v['id'] == 0)
                    {
                        $addSpecData              = array();
                        $addSpecData['gtype_id']  = $data['gtypeid'];
                        $addSpecData['spec_name'] = $v['name'];

                        if($this->goodstype->checkSpec(1,$addSpecData))
                        {
                            continue;
                        }
                        else{
                            $insertSpecId = $this->goodstype->addspec($addSpecData);
                            $specId   = $insertSpecId;
                        }
                    }
                    elseif($v['editstatus'] == 2){
                        $editSpecData              = array();
                        $editSpecData['spec_id']   = $v['id'];
                        $editSpecData['spec_name'] = $v['name'];
                        $editSpecData['gtype_id']  = $data['gtypeid'];

                        if($this->goodstype->checkSpec(2,$editSpecData))
                        {
                            echo '编辑验证失败';exit;
                            continue;
                        }
                        else{
                            $editSpecData = array();
                            $editSpecData['spec_name'] = $v['name'];
                            $upstatus = $this->goodstype->editspec($editSpecData,$v['id']);
                            $specId   = $v['id'];
                        }
                    }elseif($v['editstatus'] == 3){
                        //删除配置项
                        $delstatus = $this->goodstype->delSpec($v['id']);
                        if($delstatus > 0)
                        {
                            $delStatus = $this->goodstype->delSpecnaPrice($v['id'],$data['gtypeid']);
                        }
                        //$upstatus = $this->goodstype->changespec($v['id']);
                        $specId   = $v['id'];
                    }
                    else{
                        $specId   = $v['id'];
                    }
                
                foreach($v['item'] as $vv){
                    if($vv['editstatus'] == 1 || $v['id'] == 0)
                    {
                        $addSpecItemData = array();
                        $addSpecItemData['spec_id']   = $specId;
                        $addSpecItemData['item_name'] = $vv['itemName'];

                        if($this->goodstype->checkSpecItem(1,$addSpecItemData))
                        {
                            continue;
                        }
                        else{
                            $insertSpecItemId = $this->goodstype->addspecitem($addSpecItemData);
                        }
                    }elseif($vv['editstatus'] == 2){
                        $upSpecItemData = array();
                        $upSpecItemData['spec_id']   = $specId;
                        $upSpecItemData['id']        = $vv['id'];
                        $upSpecItemData['item_name'] = $vv['itemName'];

                        if($this->goodstype->checkSpecItem(2,$upSpecItemData))
                        {
                            continue;
                        }
                        else{
                            $upStatus = $this->goodstype->editSpecItem($upSpecItemData);
                        }
                    }elseif($vv['editstatus'] == 3){
                        //删除扩展项
                        //$upstatus = $this->goodstype->changeSpecItem($vv['id']);
                        $delSpecItemStatus = $this->goodstype->delSpecItemPrice($vv['id'],$data['gtypeid']);
                        
                    }
                }
            }
        }
        
        return $data['gtypeid'];
   }
   
}