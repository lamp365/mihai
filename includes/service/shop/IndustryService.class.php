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
     * 根据 区域code 查询每个行业下的数据情况
     * 获取已激活未删除的分类，并统计店铺数，并查询地区，可以理解为多表连接(分了两次查询来合并结果)
     * @return array
     */
    public function getCategoryAndCount($rc_region_code) {
        $resultWhere = ' where gc_pid !=0 and gc_deleted = 0';
        $industry = mysqld_selectall("select * from ".table($this->myTableName)." {$resultWhere}");

        //**************查找每个行业下 店铺的已有个数************//
        //**************根据店铺的 行业 id 与 配送范围 区code ************//
        $region_category = array();
        foreach($industry as $one){
            $region_indus = mysqld_select("select rc_region_limit from ".table('region_category')." where rc_industry_id={$one['gc_id']} and rc_region_code={$rc_region_code}");

            $sql1 = "select count(sts_id) from ".table('store_shop')." where sts_category_p2_id={$one['gc_id']} and sts_region='{$rc_region_code}'";
            $store_num = mysqld_selectcolumn($sql1);
            if ($region_indus['rc_region_limit']) {
                $one['remain'] = max(0,$region_indus['rc_region_limit'] - $store_num);  //以免减掉后出现有变成负数的情况
            } else {
                $one['remain'] = max(0,$one['gc_limit'] - $store_num);
            }
            $one['rc_region_limit'] = intval($region_indus['rc_region_limit']);
            $one['cat_num']  = $store_num;
            $region_category[] = $one;
        }
        return $region_category;
    }

    /**
     * 通过 二级行业id 和 区code  得到已经入住的商铺已经有多少家了 剩余还能入住多少家
     * @param $rc_region_code
     * @param $gc_id   二级行业id
     * @return bool|mixed
     */
    public function getStoreNumByIndustryAndCode($rc_region_code,$gc_id) {
        $resultWhere  = "where gc_id ={$gc_id} and gc_deleted = 0";
        $industry     = mysqld_select("select * from ".table($this->myTableName)." {$resultWhere}");
        $region_indus = mysqld_select("select rc_region_limit from ".table('region_category')." where rc_industry_id={$gc_id} and rc_region_code={$rc_region_code}");

        $sql1 = "select count(sts_id) from ".table('store_shop')." where sts_category_p2_id={$gc_id} and sts_region='{$rc_region_code}'";
        $store_num = mysqld_selectcolumn($sql1);

        if ($region_indus['rc_region_limit']) {
            $industry['remain'] = max(0,$region_indus['rc_region_limit'] - $store_num);  //以免减掉后出现有变成负数的情况
        } else {
            $industry['remain'] = max(0,$industry['gc_limit'] - $store_num);
        }
        $industry['rc_region_limit'] = intval($region_indus['rc_region_limit']);
        $industry['cat_num']         = $store_num;
        return $industry;
    }

    public function getCategoryShopCount($region_code, $cat_p2_id = '') {
        if(empty($cat_p2_id)){
            $data = $this->getCategoryAndCount($region_code);
        }else{
            $temp  = $this->getStoreNumByIndustryAndCode($region_code,$cat_p2_id);
            $data[] = $temp;
        }

        $cat_key_num_value = array();
        foreach ($data as $value) {
            $cat_key_num_value[$value['gc_id']] = $value;
        }
        return $cat_key_num_value;
    }

    /**
     * 比较乱 换成 以上的操作写法
     * @param $region_code
     * @param string $cat_p2_id
     * @return array
     */
    public function getCategoryShopCount2($region_code, $cat_p2_id = '') {
        $where = "1=1 ";
        $region_code && $where .= "AND rc_region_code = " . $region_code;
        $cat_p2_id && $where .= " AND sts_category_p2_id = " . $cat_p2_id;
        //**************修正数量问题************//
        $sql = " SELECT 	sts_category_p2_id,sts_id,sts_region,COUNT(*) as cat_num,B.gc_id,C.rc_region_limit,B.gc_pid,	B.gc_limit
            FROM " . table('store_shop') . " A "
            . " LEFT JOIN " . table($this->myTableName) . " B ON sts_category_p2_id = gc_id "
            . " LEFT JOIN " . table('region_category') . " C ON rc_industry_id = gc_id "
            . " where ".$where
            . " GROUP BY sts_category_p2_id,sts_region  ORDER BY cat_num DESC ";

        $step2 = mysqld_selectall($sql);
        $cat_key_num_value = array();
        foreach ($step2 as $value) {
            if ($value['rc_region_limit']) {
                $value['remain'] = $value['rc_region_limit'] - $value['cat_num'];
            } else {
                $value['remain'] = $value['limit'] - $value['cat_num'];
            }
            $cat_key_num_value[$value['sts_category_p2_id']] = $value;
        }
        return $cat_key_num_value;
    }
    
    //
     public function getIndustryInfo($gc_id = 0,$fields='*') {
         $sql = "select {$fields} from ".table($this->myTableName)." where gc_id = {$gc_id}";
         $rs  = mysqld_select($sql);
         return $rs;
     }
    
}
