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

class ShopCategoryService extends \service\publicService {

    /**
     * 插入分类之前 先验证 数据是否有问题 以及是否分类已经存在
     * @param $data
     * @param $type 1 add 2 edit
     * @return bool
     */
    public function formValidateBeforeAddCate($data,$type=1,$parentid=0)
    {
        if(!trim($data['cat_name'])){
            $this->error =  LANG('名称必填，不能为空');
            return false;
        }
        if(empty($data['cat_name'])){
            $this->error =  LANG('分类名不能为空');
            return false;
        }
        $memInfo = get_member_account();

        $where =  " where store_shop_id = {$memInfo['store_sts_id']} and status =1 and parentid = 0";
        if(empty($data['pid'])){
            //是空 在所有父分类下 不能重复重现
            $where .= " and pid=0";
        }else{
            //不为空 该分类是子类  在此父分类下 所有子类中不能重复出现
            $where .= " and pid={$data['pid']}";
        }
        
        if($parentid > 0)
        {
            $where .= " and parentid != {$parentid}";
        }
        
        //如果是编辑则需要排除自身
        if($type != 1)
        {
            $where .= " and id != {$data['id']}";
        }
        
        $where .= " and name ='".trim($data['cat_name'])."'";
        $find  = mysqld_select("SELECT id FROM " . table('store_shop_category') . " {$where}");
        if($find){
            $this->error =  LANG('此分类名已存在');
            return false;
        }
        return true;
    }
    
    /**
     * 添加或者编辑店铺分类
     * @param $data
     * @return bool|string
     */
    public function do_addCate($data)
    {
        $memInfo = get_member_account();
        $res_data=array(
            'name'          =>  trim($data['cat_name']),
            'pid'           =>  $data['pid'],
            'store_shop_id' =>  $memInfo['store_sts_id'] ,
            'p_ccate'       =>  intval($data['p_ccate']),
            'p_ccate2'      =>  intval($data['p_ccate2'])
        );

        if($data['id']){
            mysqld_update('store_shop_category',$res_data, array('id' => intval($data['id'])));
            $cat_id = $data['id'];
        }else{
            mysqld_insert('store_shop_category', $res_data);
            $cat_id = mysqld_insertid();
        }
        if(!$cat_id){
            $this->error = '操作失败！';
            return false;
        }
        return $cat_id;
    }

    public function delCate($cateid)
    {
        if(empty($cateid)){
            $this->error = '参数有误！';
            return false;
        }
        $member = get_member_account();
        $cateinfo = mysqld_select("select id,pid,store_shop_id from ".table('store_shop_category')." where id={$cateid}");
        if($cateinfo['store_shop_id'] != $member['store_sts_id']){
            $this->error = '不存在该分类';
            return false;
        }
        // 1、有子类，先提示删除子类
        // 2、没有子类 或者当前是子类 判断所删除的 该类 是否有产品 关联过  有 更新status 为 0  否则 直接删除
        if(empty($cateinfo['pid'])){
            $findCate = mysqld_select("select id from ".table('store_shop_category')." where pid={$cateid}");
            if($findCate){
                $this->error = '请先删除子类';
                return false;
            }
            $where = "where sts_id={$member['store_sts_id']} and store_p1={$cateid}";
        }else{
            //如果是子类
            $where = "where sts_id={$member['store_sts_id']} and store_p2={$cateid}";
        }

        $findDish = mysqld_select("select id from ".table('shop_dish')." {$where} ");
        if($findDish){
            //设置显示不可见
            mysqld_update('store_shop_category',array('status'=>0),array('id'=>$cateid));
        }else{
            //直接删除
            mysqld_delete('store_shop_category',array('id'=>$cateid));
        }
        return true;

    }
    /**
     * 获取分类树形结构
     * @return array
     */
   /* public function getAllCategoryStruct($region_code='') {
        $pid_eq_zero = ' where enabled =1 and deleted = 0 and parentid=0 ';
        $top = mysqld_selectall("SELECT id,name FROM " . table('shop_category') ."as A " . $pid_eq_zero);
        foreach ($top as $value) {
            $top_data[$value['id']] = $value;
        }
        //如果输入区域则返回此区域分类店铺统计
        $cat_key_num= $region_code? $this->getCategoryShopCount($region_code):array();
        $pid_not_zero = ' where enabled =1 and deleted = 0 and parentid<>0 ';
        $sub = mysqld_selectall("SELECT id,name,parentid,A.limit FROM " . table('shop_category') ."as A " . $pid_not_zero);
        foreach ($sub as $value) {
            if($cat_key_num){//如果输入区域则返回此区域分类店铺统计
                $value['shop_exist_count'] = $cat_key_num[$value['id']]['cat_num']?$cat_key_num[$value['id']]['cat_num']:0;
                $value['remain'] =$value['remain'];
            }
            $top_data[ $value['parentid'] ] && $top_data[ $value['parentid'] ]['sub'][$value['id']] = $value;//避免二级分类下挂到不显示或已删除的一级分类下
        }
        return $top_data;
    }*/

     /**
     * 获取分类树形结构
     * @return array
     */
    public function getTopShopCategory() {
        $pid_not_zero = ' where enabled =1 and deleted = 0 and parentid=0 ';
        $result = mysqld_selectall("SELECT id,name,parentid,A.limit FROM " . table('shop_category') ."as A " . $pid_not_zero);
        return $result;
    }
    /**
     * 获取已激活未删除的分类，并统计店铺数，并查询地区，可以理解为多表连接(分了两次查询来合并结果)
     * @return array
     */
   /* public function getCategoryAndCount($where = array()) {
        $resultWhere = ' where enabled =1 and deleted = 0';
        if ($where['rc_region_code']) {
            $region_cat_where = "AND rc_region_code = " . $where['rc_region_code'];
            unset($where['rc_region_code']);
        }
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $resultWhere .=' and ' . $key . $value . " ";
            }
        }

        $sql = " SELECT  A.*,C.rc_region_limit
            FROM " . table('shop_category') . " A "
            . " LEFT JOIN " . table('region_category') . " C ON rc_shop_cate_id = id " . $region_cat_where
            . " $resultWhere ORDER BY rc_region_limit DESC ";
        $region_category = mysqld_selectall($sql);

        $step2 = mysqld_selectall(" SELECT 		sts_category_p2_id,sts_id,sts_region,COUNT(*) as cat_num,B.id,B.parentid,	B.limit
            FROM " . table('store_shop') . " A "
            . " LEFT JOIN " . table('shop_category') . " B ON sts_category_p2_id = id " 
            . " where sts_category_p2_id is not null AND sts_info_status=0 "
            . " GROUP BY sts_category_p2_id,sts_region  ORDER BY cat_num DESC ");
        $cat_key_num_value = array_column($step2, 'cat_num', 'id');
        foreach ($region_category as $key => $value) {
            $value['cat_num'] = $cat_key_num_value[$value['id']] ? $cat_key_num_value[$value['id']] : 0;
            $region_category[$key] = $value;
        }
        return $region_category;
    }*/

    /*public function getCategoryShopCount($region_code,$cat_p2_id='') {
        $region_code && $where = "AND rc_region_code = " . $region_code;
        $cat_p2_id   && $where .= " AND sts_category_p2_id = " . $cat_p2_id;

        $sql= " SELECT 	sts_category_p2_id,sts_id,sts_region,COUNT(*) as cat_num,B.id,C.rc_region_limit,B.parentid,	B.limit
            FROM " . table('store_shop') . " A "
            . " LEFT JOIN " . table('shop_category') . " B ON sts_category_p2_id = id " 
            . " LEFT JOIN " . table('region_category') . " C ON rc_shop_cate_id = id " 
            . " where sts_category_p2_id is not null AND sts_info_status=0  ".$where
            . " GROUP BY sts_category_p2_id,sts_region  ORDER BY cat_num DESC ";
//        ppd($sql);
        $step2 = mysqld_selectall($sql);

        foreach ($step2 as  $value) {
            if($value['rc_region_limit']){
                $value['remain'] =$value['rc_region_limit']-$value['cat_num'];
            }else{
                $value['remain'] =$value['limit']-$value['cat_num'];
            }
            $cat_key_num_value[$value['id']]=$value;
        }
        return $cat_key_num_value;
    }*/
    
    //获取店铺所属的一级二级店铺分类
    public function getShopCategoryTree(){
        $redata = array();
        $memInfo = get_member_account();

        $oneCategoryData = mysqld_selectall('select id,name as cat_name,pid,pid as parentid,store_shop_id,status from '.table('store_shop_category').'  where store_shop_id = '.$memInfo['store_sts_id'].' and status = 1 order by sort asc');
        $list = $cate_ids = array();
        shopCategoryTree2($list,$oneCategoryData,0,$cate_ids);
        if(!empty($list)){
            foreach($list as &$item){
                if(empty($item['twoCategory'])) continue;
                $item['twoCategory'] = array_values($item['twoCategory']);
            }
            $redata['oneCategory'] = array_values($list);
        }
        return $redata;
    }
    
    //根据店铺分类ID获取名称
    public function getShopCategoryName($id=0,$fields='name'){
        $cateinfo = mysqld_select("select {$fields} from ".table('store_shop_category')." where id={$id}");
        return $cateinfo['name'];
    }
    /**
     * 根据父栏目id获得店铺子栏目名称
     * @param string $cat_id  */
    public function getStoreCategoryName($cat_id='0'){
        $member = get_member_account(1);
        $sts_id = $member['store_sts_id'];
        $pCategory = mysqld_selectall("SELECT id,name,pid from ".table('store_shop_category')." where store_shop_id=:sts_id and pid=:cat_id and status=1",array('sts_id'=>$sts_id,'cat_id'=>$cat_id));
        return $pCategory;
    }
    
    //批量更新分类
    public function upStoreCategorySort($data){
        $redata = array();
        
        $status = mysqld_update('store_shop_category',$data, array('id' => intval($data['id'])));
        return $status;
    }
    
    //获取店铺一级分类
    public function oneStoreCategory(){
        $memInfo = get_member_account();
        $oneCategoryData = mysqld_selectall('select id,name as cat_name,pid,store_shop_id,status from '.table('store_shop_category').'  where store_shop_id = '.$memInfo['store_sts_id'].' and pid = 0 and status = 1');
        
        return $oneCategoryData;
    }
    
    //获取店铺二级分类
    public function twoStoreCategory($data){
        $memInfo = get_member_account();
        $twoCategoryData = mysqld_selectall('select id,name as cat_name,pid,store_shop_id,status from '.table('store_shop_category').'  where store_shop_id = '.$memInfo['store_sts_id'].' and pid = '.intval($data['pid']).' and status = 1');
        
        return $twoCategoryData;
    }

    //批量添加分类
    public function batAddCategory($the_data)
    {
        $memInfo = get_member_account();
        if($the_data['ids'] == ''){
            $this->error = '请选择分类！';
            return false;
        }
        $categoryOneIds = rtrim($the_data['ids'],',');

        //获取分类信息
        $systemCategory     = get_systemCategoryTreeByids($categoryOneIds);  //系统分类

        $storeCategory_info  = get_storeCategoryGroup();                 //店铺分类
        $storeCategory_one   = $storeCategory_info['oneCate'];
        $storeCategory_two   = $storeCategory_info['twoCate'];

        //如果分类名字 存在，则不产生新的。
        foreach($systemCategory as $sys_firstCate){
            $firstCateName = $sys_firstCate['name'];
            $sys_p1        = $sys_firstCate['id'];
            $store_p1      = 0;
            foreach($storeCategory_one as $item1){
                if($item1['cat_name'] == $firstCateName){
                    $store_p1 = $item1['id'];
                }
            }

            $sys_secondCate = $sys_firstCate['twoCategory'];
            if(empty($sys_secondCate)){
                continue;
            }

            if(empty($store_p1)){
                //创建分类1
                $insertData1             = array();
                $insertData1['cat_name'] = $firstCateName;
                $insertData1['pid']      = 0;
                $insertData1['p_ccate']  = $sys_firstCate['id'];
                $store_p1 = $this->do_addCate($insertData1);
                if(!$store_p1)  return false;
            }

            foreach($sys_secondCate as $son_cate){
                $sys_p2     = $son_cate['id'];
                $store_p2   = 0;
                $secondCateName = $son_cate['name'];
                foreach($storeCategory_two as $item2){
                    if($item2['cat_name'] == $secondCateName){
                        $store_p2 = $item2['id'];
                    }
                }

                if(empty($store_p2)){
                    //创建分类2
                    $insertData2             = array();
                    $insertData2['cat_name'] = $secondCateName;
                    $insertData2['pid']      = $store_p1;
                    $insertData2['p_ccate']  = $son_cate['id'];
                    $insertData2['p_ccate2'] = $son_cate['parentid'];
                    $store_p2 = $this->do_addCate($insertData2);
                    if(!$store_p2)  return false;
                }

                //异步操作分类下对应商品的添加 根据系统分类$sys_p1 $sys_p2获取对应的商品 。
                //再导入到dish表中，按照 store_p1 store_p2 。
                $parame = array(
                      'sys_p1'   => $sys_p1,
                      'sys_p2'   => $sys_p2,
                      'store_p1' => $store_p1,
                      'store_p2' => $store_p2,
                      'sts_id'   => $memInfo['store_sts_id'],
                );

                $url = mobile_url('asyn_action',array('op'=>'batAddCategory'));
                asyn_doRequest($url,$parame);
            }

        }

        return true;
    }
}
