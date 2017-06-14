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
     * 获得单条region表信息
     *   */
    public function getOneRegion($where = array(),$param="*"){
        if (empty($where)) return false;
        $regionModel= new region_model();
        return $regionModel->getOne($where,$param);
    }
}