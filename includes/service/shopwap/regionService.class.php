<?php
/**
地区service层
 */
namespace service\shopwap;
use service\publicService;
use model\region_model;
class regionService extends publicService
{
    
    /**
     * 根据region_code取出region_name
     *   */
    public function getRegionNByCode($code){
        if (empty($code)) return '';
        $regionModel= new region_model();
        $return = $regionModel->getOne(array('region_code'=>$code),'region_name');
        return $return;
    }
    
    
    
    
}