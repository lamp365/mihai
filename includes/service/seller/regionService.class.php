<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/7
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\seller\goodsService();
if($a->todo()){
    //操作成功 则继续业务
}else{
    message($a->getError());
}
 */
namespace service\seller;

class regionService extends \service\publicService
{

    /**
     * 获取地区所有数据，可做缓存
     * @return array
     */
   public function getAllData()
   {
       $region_category = mysqld_selectall("SELECT * FROM " . table('region') . " where parent_id<>0 order by region_order ASC");
       return $region_category;
   }
   
    /**
     * 获取地区所有数据，可做缓存
     * @return array
     */
   public function getSubDatas( $pCode )
   {
       $pid = mysqld_selectcolumn("SELECT region_id FROM " . table('region') . " where region_code =".$pCode);
       $region_category = mysqld_selectall("SELECT * FROM " . table('region') . " where parent_id =".$pid);
       return $region_category;
   }

    public function getParentsByRegionCode($region_code)//反向查询出三级id和code
    {
        if(empty($region_code)) return array();
        $self_info =	 mysqld_select(
           "SELECT region_code,region_id,parent_id FROM ".table('region')." where region_code=$region_code"
        );
        $return_data['qu_id'] =$self_info['region_id'];
        
        $cityinfo =	 mysqld_select(
           "SELECT region_code,parent_id,region_id FROM ".table('region')." where region_id=".$self_info['parent_id']
        );
        $return_data['city'] =$cityinfo['region_code'];
        $return_data['city_id'] =$cityinfo['region_id'];
        
        $province_info =	 mysqld_select(
           "SELECT region_code,region_id FROM ".table('region')." where region_id=".$cityinfo['parent_id']
        );
        $return_data['province'] =$province_info['region_code'];
        $return_data['province_id'] =$province_info['region_id'];
//        ppd($return_data);
        return $return_data;
    }
   
   
    function recursiveRegionAssort($assortPid = 0) {
        $children = mysqld_selectall("SELECT * FROM " . table('region') . " where  parent_id = $assortPid order by region_order ASC");
        if ( $children ) {
            foreach ( $children as $value ) {
                $value['name']  = $value['region_name'];
                $value[ 'branch' ] = recursiveRegionAssort( $value[ 'region_id' ] );
                $arr[  ] = $value; //组合数组 
            }
        } else {

        }
        return $arr;
    }
   
   /**
     * 批量更新region_category，根据shop_category_id和region_code来判定新增还是修改
     * @return array
     */
    public function saveRegionCategroyLimit($region_code,$shop_category_id,$limit){ 
        
        $info =	 mysqld_select(
           "SELECT * FROM ".table('region_category')." where rc_region_code={$region_code} and  rc_industry_id={$shop_category_id}"
        );
       $data = array('rc_region_limit'=>$limit);
       if($info){
            mysqld_update('region_category', $data, array('rc_id' => $info['rc_id']));
        }else{
            $data['rc_industry_id']  = $shop_category_id;
            $data['rc_region_code']  =  $region_code;
            mysqld_insert('region_category', $data);
        }
    }
    /**
     * 根据条件查找所有的信息
     * 
     * */
    function getAllRegionByCondition($condition=array(),$param="*",$front="AND") {
        if (!empty($condition) || is_array($condition)){
            $condition = to_sqls($condition,$front);
            $result = mysqld_selectall("SELECT {$param}  FROM " . table('region') . " WHERE {$condition} ORDER BY region_order ASC");
            return $result;
        }
    }  
}