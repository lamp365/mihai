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

class ShopSystemCategoryService extends \service\publicService
{
    private $memberData;
    private $table;
    
    function __construct() {
       parent::__construct();
       $this->memberData = get_member_account();
       $this->table      = table('shop_category');
       $this->table_goods           = table('shop_goods');
       $this->table_store_shop_category      = table('store_shop_category');
   }
   
   public function ShopCateGroupList($fields='id,name,parentid'){
       $data = array();
       $two_data = array();
       $sql = "SELECT {$fields} FROM ".$this->table.' where parentid = 0';
       $one  = mysqld_selectall($sql);
       $ids = '';
       foreach($one as $v)
       {
           $data[$v['id']]['oneCategory'] = $v;
           $ids .=$v['id'] .',';
       }
       $ids = rtrim($ids,',');

       $sql_two = "SELECT id,name,parentid FROM ".$this->table." where parentid in ({$ids})";
       $two  = mysqld_selectall($sql_two);
       
       foreach($two as $k=>$v){
           $data[$v['parentid']]['oneCategory']['twoCategory'][] = $v;
       }
       $data = array_values($data);
       return $data;
   }
   
   public function ShopCateIdsGroupList($ids='',$fields='id,name,parentid'){
       $data = array();
       $two_data = array();
       $sql = "SELECT {$fields} FROM ".$this->table." where id in ({$ids})";
       $one  = mysqld_selectall($sql);
       $ids = '';
       foreach($one as $v)
       {
           $data['oneCategory'][$v['id']] = $v;
           $ids .=$v['id'] .',';
       }
       $ids = rtrim($ids,',');

       $sql_two = "SELECT id,name,parentid FROM ".$this->table." where parentid in ({$ids})";
       $two  = mysqld_selectall($sql_two);
       
       foreach($two as $k=>$v){
           $data['oneCategory'][$v['parentid']]['twoCategory'][] = $v;
       }
       $data['oneCategory'] = array_values($data['oneCategory']);
       return $data;
   }
   
   public function ShopCateGroupListTwo($fields='id,name as cat_name,parentid'){
       $data = array();
       $two_data = array();
       
       $where = '';
       
       /*
       //显示已经导入过得分类id
       $sql_member = 'select p_ccate,p_ccate2 from '.$this->table_store_shop_category." where store_shop_id = {$this->memberData['store_sts_id']} and p_ccate2 = 0 and p_ccate > 0";
       $rs_member = mysqld_selectall($sql_member);
       $pCcte2Str = '';
       foreach($rs_member as $v){
           $pCcte2Str .= $v['p_ccate'].',';
       }
       $pCcte2Str = rtrim($pCcte2Str,',');
       
       if($pCcte2Str != '')
       {
           $where .= " and id not in ({$pCcte2Str})";
       }
       */

       $sql = "SELECT {$fields} FROM ".$this->table." where parentid = 0 and industry_p2_id = {$this->memberData['sts_category_p2_id']} {$where}";
       $one  = mysqld_selectall($sql);
       $ids = '';
       foreach($one as $v)
       {
           $data['oneCategory'][$v['id']] = $v;
           $ids .=$v['id'] .',';
       }
       $ids = rtrim($ids,',');
       if($ids != '')
       {
           $where = "where parentid in ({$ids})";
       }
       $sql_two = "SELECT id,name as cat_name,parentid FROM ".$this->table." {$where}";
       $two  = mysqld_selectall($sql_two);
       foreach($two as $k=>$v){
           $data['oneCategory'][$v['parentid']]['twoCategory'][] = $v;
       }

       if($data['oneCategory'] != '')
       {
            $data['oneCategory'] = array_values($data['oneCategory']);
       }
       else{
           $data['oneCategory'] =  array();
       }
 
       return $data;
   }
   
    //根据店铺分类ID获取名称
    public function getShopSystemCategoryName($id=0,$fields='name'){
        $cateinfo = mysqld_select("select {$fields} from ".$this->table." where id={$id}");
        return $cateinfo['name'];
    }
    
    //通过分类名称判断该分类是否存在
    public function checkNameId($name='',$fields='id'){
         $cateinfo = mysqld_select("select {$fields} from ".$this->table." where name={$name}");
        return $cateinfo['id'];
    }
    
    public function count_category_one_goods($ids){
        $where = '';
        
        $sql_gids = "select gid from ".table('shop_dish')." where sts_id = {$this->memberData['store_sts_id']} and gid > 0";
        $rs_gids  = mysqld_selectall($sql_gids);
        $gidStr = '';
        foreach($rs_gids as $v){
            $gidStr .= $v['gid'].',';
        }
        $gidStr = rtrim($gidStr,',');
        if($gidStr != '')
        {
            $where .= " and id not in ($gidStr)";
        }
        
       $sql = "select count(0) as total,pcate from ".$this->table_goods." where pcate in ({$ids}) {$where} GROUP BY pcate";
       $rs = mysqld_selectAll($sql);
       return $rs;
    }
    
    public function count_category_two_goods($ids){
        $where = '';
        
        $sql_gids = "select gid from ".table('shop_dish')." where sts_id = {$this->memberData['store_sts_id']} and gid > 0";
        $rs_gids  = mysqld_selectall($sql_gids);
        $gidStr = '';
        foreach($rs_gids as $v){
            $gidStr .= $v['gid'].',';
        }
        $gidStr = rtrim($gidStr,',');
        if($gidStr != '')
        {
            $where .= " and id not in ($gidStr)";
        }
        
       $sql = "select count(0) as total,ccate from ".$this->table_goods." where ccate in ({$ids}) {$where} GROUP BY ccate";
       $rs = mysqld_selectAll($sql);
       return $rs;
    }
    
}