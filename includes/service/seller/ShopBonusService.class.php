<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class ShopBonusService extends \service\publicService {
    private $memberData;
    private $table_c;
    private $table_cm;
    
    function __construct() {
        parent::__construct();
       $this->memberData = get_member_account();
       $this->table_c      = table('store_coupon');
       $this->table_cm     = table('store_coupon_member');
   }

    //添加优惠券
    public function addCoupon($data){
        $insertData = array();
        
        $insertData['coupon_img']          = $data['coupon_img'];
        $insertData['coupon_name']          = $data['coupon_name'];
        $insertData['payment']              = $data['payment'];
        $insertData['usage_mode']           = $data['usage_mode'];
        $insertData['coupon_amount']        = FormatMoney($data['coupon_amount']);
        $insertData['amount_of_condition']  = FormatMoney($data['amount_of_condition']);
        $insertData['release_quantity']     = $data['release_quantity'];
        $insertData['create_time']          = time();
        $insertData['store_shop_id']        = $this->memberData['store_sts_id'];
        $insertData['receive_start_time']   = strtotime($data['receive_start_time']);
        $insertData['receive_end_time']     = strtotime($data['receive_end_time']);
        $insertData['use_start_time']       = strtotime($data['use_start_time']);
        $insertData['use_end_time']         = strtotime($data['use_end_time']);
        $insertData['store_category_idone']  = intval($data['oneCategory']);
        $insertData['store_category_idtwo']  = intval($data['twoCategory']);
        $insertData['store_shop_dishid']     = $data['store_shop_dishid_enter']!=''?json_encode($data['store_shop_dishid_enter']):'';
        $insertData['inventory']     = 0;//被领取数量，开始时为0
        $insertData['get_limit']     = $data['get_limit'];
        $id =  mysqld_insert('store_coupon', $insertData);
        $id =  mysqld_insertid();
        return $id;
    }
    
    //获取店铺优惠券列表
    public function couponList($pindex,$psize,$wheres='',$fieldstr='scid,payment,coupon_amount,amount_of_condition,release_quantity,create_time,store_category_idone,store_category_idtwo,store_shop_id,usage_mode,receive_start_time,receive_end_time,use_start_time,use_end_time,coupon_name,inventory',$order='ORDER BY use_end_time asc'){
        $result          = array();
        $store_where     = " where store_shop_id = {$this->memberData['store_sts_id']} {$wheres} ";
        $sql = "SELECT {$fieldstr} FROM " . $this->table_c  . $store_where;
        $limit  = ("LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $result['data']  = mysqld_selectall($sql . $order . ' ' . $limit);
        
        $sql_count = "SELECT count(0) FROM " . $this->table_c  . $store_where;
        $result['total'] = mysqld_selectcolumn($sql_count);
        
        return $result;
    }
    
    //获取单条店铺优惠券信息
    public function getOneCoupon($id=0,$fields='*'){
        $data = array();
        $sql  = "select {$fields} from {$this->table_c} where scid = {$id} limit 1";
        $data = mysqld_select($sql);
        return $data;
    }
    
    //更新优惠券
    public function upCoupon($data,$id){
        $upData = array();
        
        $upData['coupon_img']           = $data['coupon_img'];
        $upData['coupon_name']          = $data['coupon_name'];
        $upData['payment']              = $data['payment'];
        $upData['usage_mode']           = $data['usage_mode'];
        $upData['coupon_amount']        = FormatMoney($data['coupon_amount']);
        $upData['amount_of_condition']  = FormatMoney($data['amount_of_condition']);
        $upData['release_quantity']     = $data['release_quantity'];
        $upData['create_time']          = time();
        $upData['store_shop_id']        = $this->memberData['store_sts_id'];
        $upData['receive_start_time']   = strtotime($data['receive_start_time']);
        $upData['receive_end_time']     = strtotime($data['receive_end_time']);
        $upData['use_start_time']       = strtotime($data['use_start_time']);
        $upData['use_end_time']         = strtotime($data['use_end_time']);
        $upData['store_category_idone'] = intval($data['oneCategory']);
        $upData['store_category_idtwo'] = intval($data['twoCategory']);
        $upData['store_shop_dishid']    = $data['store_shop_dishid_enter']!=''?json_encode($data['store_shop_dishid_enter']):'';

        $id =  mysqld_update('store_coupon', $upData , array('scid'=>$id));
        return $id;
    }
    
    //用户优惠券领取列表
    public function couponMemberList($scid,$pindex,$psize,$wheres='',$fieldstr='scmid,scid,receive_time,use_time,status,nickname,mobile,order_money,dish_id,dish_name,order_number,order_time',$order='ORDER BY use_time asc'){
        $result          = array();
        $store_where     = " where scid = {$scid} {$wheres} ";
        $sql = "SELECT {$fieldstr} FROM " . $this->table_cm  . $store_where;
        $limit  = ("LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

        $result['data']  = mysqld_selectall($sql . $order . ' ' . $limit);
        
        $sql_count = "SELECT count(0) FROM " . $this->table_cm  . $store_where;
        $result['total'] = mysqld_selectcolumn($sql_count);
        
        return $result;
    }
    
    
    //发放优惠券
    public function insertCouponMember($data){
        $insertData = array();
        
        $insertData['scid']               = $data['id'];
        $insertData['receive_time']       = time();
        $insertData['nickname']           = $data['nickname']!=''?$data['nickname']:'';
        $insertData['mobile']             = $data['mobile'];
        
        $insertStatus =  mysqld_insert('store_coupon_member', $insertData);
        $id =  mysqld_insertid();
        return $id;
    }
    
    //操作优惠券库存
    //增减优惠券的库存数量
    public function editCoupon($id,$nums){
        $where = '';
        $where .= " where scid = {$id}";
        $sql = "update" . $this->table_c ." set release_quantity = release_quantity - {$nums},inventory = inventory + {$nums}".$where;
        $queryStatus = mysqld_query($sql);
        return $queryStatus;
    }
    
    //更新领取数量
    //统计优惠券使用数量
    public function countUseCoupon($scid){
        $where = '';
        $sql = "select count(0) as total from {$this->table_c} where scid = {$scid} and status > 0";
        $rs  = mysqld_select($sql);
        return $rs['total'];
    }
    
}
?>