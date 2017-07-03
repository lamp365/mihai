<?php
/**
微信限时购栏目
 */
namespace service\wapi;

class categoryService extends \service\publicService
{
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