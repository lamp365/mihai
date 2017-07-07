<?php
/**
微信限时购栏目
 */
namespace service\wapi;

class categoryService extends \service\publicService
{
    /**
     * 判断所有商品行业的数目
     *   */
    public function checkAllInsNum(){
        $where = " status=1 and store_count > 0 AND industry_p2_id>0";
        $param = "id,sts_id,store_p1,store_p2,industry_p2_id";
        $orderby = "id DESC";
        $groupby = "industry_p2_id";
        $ShopDishModel = new \model\shop_dish_model();
        $return = $ShopDishModel->getAllShopDish($where,$param,$orderby,$groupby);
        if (empty($return)) return '';
        else return count($return);
    }
    /**
     * 取所有商品的行业名称
     *   */
    public function getAllInsName($jd='',$wd=''){
        //根据经纬度获取在该区域配送的店铺
        $storeShop = new \service\shopwap\storeShopService();
        $return = $storeShop->getStoreByJdAndWd($jd,$wd);
        if (empty($return)) return false;
        
        $ShopDishModel = new \model\shop_dish_model();
        //根据店铺找行业名称
        $table1 = $ShopDishModel->table_name;
        $table2 = 'shop_goods';
        foreach ($return as $v){
            $ids[] = " a.sts_id = {$v['sts_id']} ";
        }
        $ids = implode(' or ' , $ids);
        $where1 = " a.status=1 and a.store_count > 0 and a.gid > 0 ";
        $where1 .= ' and ('.$ids.')';
        
        $sql = "SELECT b.pcate,b.ccate, b.industry_p2_id from ".table($table1)." AS a LEFT JOIN ".table($table2)." AS b ON a.gid = b.id where ".$where1;
        $res = $ShopDishModel->fetchall($sql);
        if (!empty($res)) return $res;
    }
    /**
     * 取所有的一级栏目名称
     *   */
    /* public function getAllP1CatName($jd='',$wd=''){
        //根据经纬度获取在该区域配送的店铺
        $storeShop = new \service\shopwap\storeShopService();
        $return = $storeShop->getStoreByJdAndWd($jd,$wd);
        if (empty($return)) return false;
        
        $ShopDishModel = new \model\shop_dish_model();
        //根据店铺找行业名称
        $table1 = $ShopDishModel->table_name;
        $table2 = 'store_shop_category';
        foreach ($return as $v){
            $ids[] = " a.sts_id = {$v['sts_id']} ";
        }
        $ids = implode(' or ' , $ids);
        $where1 = " a.status=1 and a.store_count > 0";
        $where1 .= ' and ('.$ids.')';
        
        $sql = "SELECT b.id,b.name from ".table($table1)." AS a INNER JOIN ".table($table2)." AS b ON a.store_p1 = b.id where ".$where1." group by a.store_p1";
        $res = $ShopDishModel->fetchall($sql);
        if (!empty($res)) return $res;
    } */
    /**
     * 根据活动中的行业id取活动的一级栏目名称
     *   */
    public function getAllCat1NameByIns($ins_id){
        if (empty($ins_id)) return '';
        $shopGoodsModel = new \model\shop_goods_model();
        //根据店铺找行业名称
        $table1 = $shopGoodsModel->table_name;
        $table2 = 'shop_category';
        
        $where1 = " a.industry_p2_id='$ins_id' ";
        
        $sql = "SELECT b.id,b.name, b.thumb from ".table($table1)." AS a LEFT JOIN ".table($table2)." AS b ON a.pcate = b.id where ".$where1." group by a.pcate";
        $res = $shopGoodsModel->fetchall($sql);
        if (!empty($res)) return $res;
    }
    /**
     * 根据活动中的一级栏目id取活动的二级栏目名称
     *   */
    public function getAllCat2NameByCat1($catid,$jd='',$wd=''){
        if (empty($catid)) return '';
        $shopGoodsModel = new \model\shop_goods_model();
        //根据店铺找行业名称
        $table1 = $shopGoodsModel->table_name;
        $table2 = 'shop_category';
        
        $where1 = " a.pcate='$catid' ";
        
        $sql = "SELECT b.id,b.name, b.thumb from ".table($table1)." AS a LEFT JOIN ".table($table2)." AS b ON a.ccate = b.id where ".$where1." group by a.ccate";
        $res = $shopGoodsModel->fetchall($sql);
        if (!empty($res)) return $res;
    }
    
    
    
/**********************************************************************************************/    
    /**
     * 判断参加限时购活动的行业的数目
     *   */
    public function checkInsNum($ac_id){
        if (empty($ac_id)) return '';
        $where = array(
            'ac_action_id'   => $ac_id,
            'ac_dish_status' => 1
        );
        $param = "ac_dish_id,ac_in_id,ac_p1_id";
        $orderby = "ac_dish_id DESC";
        $groupby = "ac_in_id";
        $actDishModel = new \model\activity_dish_model();
        $return = $actDishModel->getAllActivtyDish($where,$param,$orderby,$groupby);
        if (empty($return)) return '';
        else return count($return);
    }
    /**
     * 取参加活动的行业名称
     *   */
    public function getActInsName($ac_id,$jd='',$wd=''){
        if (empty($ac_id)) return '';
        $actDishModel = new \model\activity_dish_model();
	    $table1 = $actDishModel->table_name;
	    $table2 = 'industry';
	    $where = " a.ac_action_id = '$ac_id' and a.ac_dish_status=1 ";
	    $sql_where = get_area_condition_sql($jd,$wd);
	    if ($sql_where){
	        $where .= $sql_where;
	    }
	    $sql = "SELECT a.ac_in_id as id,b.gc_name as name from ".table($table1)." AS a LEFT JOIN ".table($table2)." AS b ON a.ac_in_id = b.gc_id where ".$where." group by a.ac_in_id";
	    $return = $actDishModel->fetchall($sql);
        if (empty($return)) return '';
        foreach ($return as $val){
            if (empty($val['id'])) continue;
            $temp['type'] = 1;
            $temp['id'] = $val['id'];
            $temp['name'] = $val['name'];
            $data[] = $temp;
        }
        return $data;
        
    }
    /**
     * 取参加活动的一级栏目名称
     *   */
    public function getP1CatName($ac_id,$jd='',$wd=''){
        if (empty($ac_id)) return '';
        $actDishModel = new \model\activity_dish_model();
	    $table1 = $actDishModel->table_name;
	    $table2 = 'shop_category';
	    $where = " a.ac_action_id = '$ac_id' and a.ac_dish_status=1 ";
	    $sql_where = get_area_condition_sql($jd,$wd);
	    if ($sql_where){
	        $where .= $sql_where;
	    }
	    $sql = "SELECT a.ac_p1_id as id,b.name from ".table($table1)." AS a LEFT JOIN ".table($table2)." AS b ON a.ac_p1_id = b.id where ".$where." group by a.ac_p1_id";
	    $return = $actDishModel->fetchall($sql);
        if (empty($return)) return '';
        foreach ($return as $val){
            if (empty($val['id'])) continue;
            $temp['type'] = 2;
            $temp['id'] = $val['id'];
            $temp['name'] = $val['name'];
            $data[] = $temp;
        }
        return $data;
    }
    /**
     * 取参加活动的二级栏目名称
     *   */
    public function getP2CatName($ac_id){
        if (empty($ac_id)) return '';
        $actDishModel = new \model\activity_dish_model();
	    $table1 = $actDishModel->table_name;
	    $table2 = 'shop_category';
	    $where = " a.ac_action_id = '$ac_id' and a.ac_dish_status=1 ";
	    $sql = "SELECT a.ac_p2_id as id,b.name from ".table($table1)." AS a LEFT JOIN ".table($table2)." AS b ON a.ac_p2_id = b.id where ".$where." group by a.ac_p2_id";
	    $return = $actDishModel->fetchall($sql);
        if (empty($return)) return '';
        foreach ($return as $val){
            if (empty($val['id'])) continue;
            $temp['id'] = $val['id'];
            $temp['name'] = $val['name'];
            $data[] = $temp;
        }
        return $data;
    }
    /**
     * 根据活动中的行业id取活动的一级栏目名称
     *   */
    public function getCat1NameByActIns($ins_id,$jd='',$wd='',$ac_id){
        if (empty($ins_id) || empty($ac_id)) return '';
        $actDishModel = new \model\activity_dish_model();
	    $table1 = $actDishModel->table_name;
	    $table2 = 'shop_category';
	    $where = " a.ac_action_id = '$ac_id' and a.ac_in_id = '$ins_id' and a.ac_dish_status=1 ";
	    $sql_where = get_area_condition_sql($jd,$wd);
	    if ($sql_where){
	        $where .= $sql_where;
	    }
	    $sql = "SELECT b.id,b.name,b.thumb from ".table($table1)." AS a LEFT JOIN ".table($table2)." AS b ON a.ac_p1_id = b.id where ".$where." group by a.ac_p1_id";
	    $return = $actDishModel->fetchall($sql);
        if (empty($return)) return '';
        foreach ($return as $val){
            if (empty($val['id'])) continue;
            $temp['type'] = 1;
            $temp['id'] = $val['id'];
            $temp['name'] = $val['name'];
            $temp['thumb'] = $val['thumb'];
            $data[] = $temp;
        }
        return $data;
    }
    /**
     * 根据活动中的一级栏目id取活动的二级栏目名称
     *   */
    public function getCat2NameByActP1Cat($p1_id,$jd='',$wd='',$ac_id){
        if (empty($p1_id) || empty($ac_id)) return '';
        $actDishModel = new \model\activity_dish_model();
	    $table1 = $actDishModel->table_name;
	    $table2 = 'shop_category';
	    $where = " a.ac_action_id = '$ac_id' and a.ac_p1_id = '$p1_id' and a.ac_dish_status=1 ";
	    $sql_where = get_area_condition_sql($jd,$wd);
	    if ($sql_where){
	        $where .= $sql_where;
	    }
	    $sql = "SELECT b.id,b.name,b.thumb from ".table($table1)." AS a LEFT JOIN ".table($table2)." AS b ON a.ac_p2_id = b.id where ".$where." group by a.ac_p2_id";
	    $return = $actDishModel->fetchall($sql);
        if (empty($return)) return '';
        foreach ($return as $val){
            if (empty($val['id'])) continue;
            $temp['type'] = 2;
            $temp['id'] = $val['id'];
            $temp['name'] = $val['name'];
            $temp['thumb'] = $val['thumb'];
            $data[] = $temp;
        }
        return $data;
    }   
}