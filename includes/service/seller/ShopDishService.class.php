<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class ShopDishService extends \service\publicService {
    private $memberData;
    private $table;
    
    function __construct() {
       parent::__construct();
       $this->memberData   = get_member_account();
       $this->table        = table('shop_dish');
       $this->table_price  = table('dish_spec_price');
       
   }
   
   //获取dish列表
   public function getDishAll($fields='*',$store_p1=-1,$store_p2=-1){
       $dish_where = " where sts_id = {$this->memberData['store_sts_id']}";
       if($store_p1 > 0 && $store_p2 > 0)
       {
           $dish_where .= " and store_p1 = {$store_p1} and store_p2 = {$store_p2}";
       }
       $sql = "SELECT {$fields} FROM ".$this->table.$dish_where;
       $data  = mysqld_selectall($sql);
       return $data;
   }
   
    //批量获取宝贝
    public function getDishs($idStr,$fields='*'){
        $data = array();
        if($idStr == '')
        {
            return '必要参数不存在';
        }
        $sql  = "select {$fields} from {$this->table} where id in ({$idStr})";
        $data = mysqld_selectall($sql);
        return $data;
    }
    
    //排除宝贝
    public function getDelDishs($store_p1,$store_p2,$idStr,$fields='*'){
        $data = array();
        if($idStr == '')
        {
            return '必要参数不存在';
        }
        $sql  = "select {$fields} from {$this->table} where store_p1 = {$store_p1} and store_p2 = {$store_p2} and id not in ({$idStr})";
        $data = mysqld_selectall($sql);
        return $data;
    }
   
    //搜索宝贝
    public function searchDish($data=array(),$fields='*'){
        $sql = "select {$fields} from {$this->table} where sts_id = {$this->memberData['store_sts_id']} and title like '%{$data['key']}%' limit {$data['page']},{$data['limit']}";

        $redata = mysqld_selectall($sql);
        foreach($redata as $k=>$v){
            $redata[$k]['marketprice']  = FormatMoney($v['marketprice'],2);
        }
        $data_total = mysqld_select("select count(0) as total from {$this->table} where sts_id = {$this->memberData['store_sts_id']} and title like '%{$data['key']}%'");
        $redata['total'] = $data_total['total'];
        return $redata;
    }
    
    //获取宝贝详情
    public function getDishContent($data,$fields='*'){
        if($data['dish_id'] <= 0)
        {
            return false;
        }
        $sql = "select {$fields} from {$this->table} where id={$data['dish_id']}";

        $dishContent = mysqld_select($sql);
        
        return $dishContent;
    }
    
    //获取宝贝分页
    public function getDishPage($_GP,$fields='*'){
        $data = array();
        $_GP['page'] = max(1, intval($_GP['page']));
        $_GP['limit'] = $_GP['limit']>0?$_GP['limit']:10; 
        
        $wheres = '';
        $_GP['marketprice_less'] = $_GP['marketprice_less']>0?FormatMoney($_GP['marketprice_less']):0;
        $_GP['marketprice_many'] = $_GP['marketprice_many']>0?FormatMoney($_GP['marketprice_many']):'99999999';
        $wheres .= " and deleted = 0";
        $wheres .= " and marketprice BETWEEN {$_GP['marketprice_less']} AND {$_GP['marketprice_many']}";
        
        if($_GP['store_p1'] > 0){
            $wheres .= " and store_p1 = {$_GP['store_p1']}";
        }
        
        if($_GP['store_p2'] > 0){
            $wheres .= " and store_p2 = {$_GP['store_p2']}";
        }
        
        if($_GP['brands_id'] != '')
        {
            $wheres .= " and brands_id in ({$_GP['brands_id']})";
        }
        
        if(isset($_GP['status'])){
            if($_GP['status'] < 2)
            {
                $wheres .= " and status = {$_GP['status']}";
            }
        }
        
        $order = '';
        
        if($_GP['sales_num'] != '')
        {
            $order = "order by sales_num {$_GP['sales_num']}";
        }
        elseif($_GP['store_count'] != ''){
            $order = "order by store_count {$_GP['store_count']}";
        }
        else{
            $order = 'order by createtime desc,sort asc';
        }
        
        $limit = " LIMIT " . ($_GP['page'] - 1) * $_GP['limit'] . ',' . $_GP['limit'];
        $sql = "select {$fields} from {$this->table} where sts_id = {$this->memberData['store_sts_id']} {$wheres} {$order} {$limit}";
        $dishList = mysqld_selectall($sql);
        
        foreach($dishList as $k=>$v){
            $dishList[$k]['marketprice']  = FormatMoney($v['marketprice'],2);
        }
        
        $dishList['total'] = mysqld_select("select count(0) as total from {$this->table} where sts_id = {$this->memberData['store_sts_id']} {$wheres}");
        
        $dishList['total'] = intval($dishList['total']['total']);
        
        return $dishList;
    }
    
    
    //根据分类一级和二级分类id获取宝贝信息
    public function getPcontent($_GP,$fields='*'){
        $data = array();

        $sql  = "select {$fields} from {$this->table} where store_p2 = {$_GP['store_p2']}";
        $data = mysqld_selectall($sql);
        return $data;
    }
    //根据一级分类id获取所有宝贝信息
    public function getcontentByP1($_GP,$fields='*'){
        $data = array();
        $sql  = "select {$fields} from {$this->table} where store_p1 = :store_p1";
        $data = mysqld_selectall($sql,array('store_p1'=>$_GP['store_p1']));
        return $data;
    }
    
    //根据gtypeid返回对应的受影响宝贝数
    public function getGtypeCount($data){
        $redata = array();
        $sql  = "select id as dish_id from {$this->table} where gtype_id = :gtype_id";
        $redata = mysqld_selectall($sql,array('gtype_id'=>$data['gtype_id']));
        return $redata;
    }
    
    
    public function count_category_one_dish($ids){
       $sql = "select count(0) as total,store_p1 from squdian_shop_dish where store_p1 in ({$ids}) GROUP BY store_p1";
       $rs = mysqld_selectAll($sql);
       return $rs;
    }
    
    public function count_category_two_dish($ids){
       $sql = "select count(0) as total,store_p2 from squdian_shop_dish where store_p2 in ({$ids}) GROUP BY store_p2";
       $rs = mysqld_selectAll($sql);
       return $rs;
    }
    
    //判断某个店铺里是否已经存在从产品库里导入的商品
    public function checkGoods($gid){
        $sql  = "select id from {$this->table} where sts_id = {$this->memberData['store_sts_id']} and gid = {$gid}";
        $redata = mysqld_select($sql);

        if($redata['id'] > 0){
            return true;   
        }
        else{
            return false;
        }
    }
    
    //获取店铺里从商铺导入的所有产品ID
    public function getDishGoosIds($field='gid'){
        $sql = "select {$field} from {$this->table} where sts_id = {$this->memberData['store_sts_id']}";
        $redata = mysqld_selectall($sql);
        return $redata;
    }
    
    //返回模型影响的宝贝数
    public function getSpecDishCount($gtype_ids,$dish_id=0){
        if($gtype_ids == ''){
            return 0;
        }
        $wheres = '';
        if($dish_id > 0){
            $wheres .= " and id != {$dish_id}";
        }
        $sql  = "select count(0) as total from {$this->table} where gtype_id in ({$gtype_ids}) {$wheres}";
        $redata = mysqld_select($sql);
        
        return $redata;
    } 
    
    //返回模型影响的宝贝
    public function getSpecDish($gtype_id){
        if($gtype_id <= 0){
            return 0;
        }
        
        $sql  = "select id from {$this->table} where gtype_id = {$gtype_id} and sts_id = {$this->memberData['store_sts_id']}";
        $redata = mysqld_selectall($sql);
        return $redata;
    } 
    
    public function changeDishStatus($data){
        $id     = intval($data['dish_id']);
        $status = intval($data['status']);
        $sql    = "update {$this->table} set status = {$status} where id = {$id}";
        $redata = mysqld_query($sql);
        return $redata;
    }
    
    public function deleteDish($data){
        $id    = intval($data['dish_id']);
        $sql   = "update {$this->table} set deleted = 1 where id = {$id}";

        $redata = mysqld_query($sql);
        return $redata;
    }
    
    public function changeDishRecommand($data){
        $id     = intval($data['dish_id']);
        $isrecommand  = intval($data['isrecommand']);
        $sql    = "update {$this->table} set isrecommand = {$isrecommand} where id = {$id}";

        $redata = mysqld_query($sql);
        return $redata;
    }
    
    public function changeDishIsNew($data){
        $id     = intval($data['dish_id']);
        $isnew  = intval($data['isnew']);
        $sql    = "update {$this->table} set isnew = {$isnew} where id = {$id}";

        $redata = mysqld_query($sql);
        return $redata;
    }
    
    public function upChangeOrder($data){
        $id     = intval($data['dish_id']);
        $sort   = intval($data['sort']);
        $sql    = "update {$this->table} set sort = {$sort} where id = {$id}";

        $redata = mysqld_query($sql);
        return $redata;
    }
    
    //
    public function dishPrice($dish_ids,$fields="id,spec_key"){        
        $sql = "select {$fields} from {$this->table_price} where dish_id in ($dish_ids)";
        $redata = mysqld_selectall($sql);
        return $redata;
    }
    
    
    public function deleteDishPrice($ids){
        
        $sql   = "delete from {$this->table_price} where id in ({$ids});";
        
        $redata = mysqld_query($sql);
        
        return $redata;
    }
    
    public function getDishInfo($dish_id,$fields=''){
        $sql = "select {$fields} from {$this->table} where id = $dish_id";
        $redata = mysqld_select($sql);
        return $redata;
    }
    
    public function getIndexDishCount(){
        $sql = "select count(1) as indexDishCount from {$this->table} where sts_id = {$this->memberData['store_sts_id']} and is_index = 1";
        $redata = mysqld_select($sql);
        return $redata;
    }
    
} 
?>