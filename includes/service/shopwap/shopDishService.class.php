<?php
/**
商品的service层
 */
namespace service\shopwap;
use service\publicService;
use \model\shop_dish_model;
class shopDishService extends publicService
{
    /**
     * 获得单条shop_dish表信息
     *   */
    public function getOneShopDish($where = array(),$param="*"){
        if (empty($where)) return false;
        $shopDishModel = new shop_dish_model();
        return $shopDishModel->getOne($where,$param);
    }
    /**
     * 获得多条shop_dish表信息
     *   */
    public function getAllShopDish($where = array(),$param="*",$orderby=false){
        $shopDishModel = new shop_dish_model();
        return $shopDishModel->getAll($where,$param,$orderby);
    }
}