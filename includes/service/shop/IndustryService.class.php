<?php

/**
 * Author: 王敬
 */

namespace service\shop;

class IndustryService extends \service\publicService {

    protected $myTableName = 'industry';
    /**
     * 获取分类树形结构
     * @return array
     */
    public function getAllDataStruct($region_code='',$appNoNeedIndex=0) {
        $where = ' where gc_deleted = 0 and gc_pid = 0';
        $data = mysqld_selectall("SELECT * FROM " . table($this->myTableName) . $where);
        foreach ($data as $cate) {
            $cate['sub'] = array();
            $return_data[$cate['gc_id']] = $cate; //1级分类
        }
        
        $where = ' where gc_deleted = 0 and gc_pid <> 0';
        $data = mysqld_selectall("SELECT * FROM " . table($this->myTableName) . $where);
        
        $cat_key_num= $region_code? $this->getCategoryShopCount($region_code):array();//如果输入区域则返回此区域分类店铺统计
       
        foreach ($data as $cate) {
            $cate['shop_exist_count'] = $cat_key_num[$cate['gc_id']]['cat_num']?$cat_key_num[$cate['gc_id']]['cat_num']:0;
            $cate['remain'] =$cat_key_num[$cate['gc_id']]['remain']?$cat_key_num[$cate['gc_id']]['remain']:$cate['gc_limit'];
            $return_data[$cate['gc_pid']]['sub'][$cate['gc_id']] = $cate; //2级分类
        }
        if($appNoNeedIndex){
            $newdata = array_merge($return_data,array());
            foreach ($newdata as $key => $value) {
                $tmp  =  array_merge($value['sub'],array());
                $value['sub'] = $tmp;
                $newdata[$key] = $value;
            }
            $return_data = $newdata;
        }
        return $return_data;
    }


    /**
     * 获取已激活未删除的分类，并统计店铺数，并查询地区，可以理解为多表连接(分了两次查询来合并结果)
     * @return array
     */
    public function getCategoryAndCount($where = array()) {
        $resultWhere = ' where gc_deleted = 0';
        if ($where['rc_region_code']) {
            $region_cat_where = "AND rc_region_code = " . $where['rc_region_code'];
            unset($where['rc_region_code']);
        }
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $resultWhere .=' and ' . $key . $value . " ";
            }
        }
        //**************按店铺数量排序，对用户比较友好************//
        $sql = " SELECT  A.*,C.rc_region_limit
            FROM " . table($this->myTableName) . " A "
            . " LEFT JOIN " . table('region_category') . " C ON rc_industry_id = gc_id " . $region_cat_where
            . " $resultWhere ORDER BY rc_region_limit DESC ";
        $region_category = mysqld_selectall($sql);
        //**************修正数量问题************//
        $step2 = $this->getCategoryShopCount( $where['rc_region_code'] );
        $cat_key_num_value = array_column($step2, 'cat_num', 'id');
        foreach ($region_category as $key => $value) {
            $value['cat_num'] = $cat_key_num_value[ $value['id'] ] ? $cat_key_num_value[$value['id']] : 0;
            $region_category[$key] = $value;
        }
        return $region_category;
    }

    public function getCategoryShopCount($region_code, $cat_p2_id = '') {
        $region_code && $where = "AND rc_region_code = " . $region_code;
        $cat_p2_id && $where .= " AND sts_category_p2_id = " . $cat_p2_id;
        //**************修正数量问题************//
        $sql = " SELECT 	sts_category_p2_id,sts_id,sts_region,COUNT(*) as cat_num,B.gc_id,C.rc_region_limit,B.gc_pid,	B.gc_limit
            FROM " . table('store_shop') . " A "
            . " LEFT JOIN " . table($this->myTableName) . " B ON sts_category_p2_id = gc_id "
            . " LEFT JOIN " . table('region_category') . " C ON rc_industry_id = gc_id "
            . " where sts_category_p2_id is not null AND sts_info_status=0  " . $where
            . " GROUP BY sts_category_p2_id,sts_region  ORDER BY cat_num DESC ";
//        ppd($sql);
        $step2 = mysqld_selectall($sql);

        foreach ($step2 as $value) {
            if ($value['rc_region_limit']) {
                $value['remain'] = $value['rc_region_limit'] - $value['cat_num'];
            } else {
                $value['remain'] = $value['limit'] - $value['cat_num'];
            }
            $cat_key_num_value[$value['id']] = $value;
        }
        return $cat_key_num_value;
    }

}
