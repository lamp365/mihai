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
    public function addCoupon($data,$id = ''){
        $insertData = array();
        if($data['usage_mode'] == 2){
            //按照分类
            if(empty($data['oneCategory'])){
                $this->error = '分类不用为空！';
                return false;
            }
        }else if($data['usage_mode'] == 3){
            //按照单品
            if(empty($data['store_shop_dishid'])){
                $this->error = '请选择对应商品！';
                return false;
            }
        }

        if(empty($data['coupon_name'])){
            $this->error = '优惠卷名称不能为空！';
            return false;
        }
	    $data['amount_of_condition'] = intval($data['amount_of_condition']);
	    $data['coupon_amount']       = intval($data['coupon_amount']);
        if(empty($data['amount_of_condition']) || empty($data['coupon_amount'])){
            $this->error = '请设置优惠卷金额！';
            return false;
        }
        if($data['amount_of_condition'] < $data['coupon_amount']){
            $this->error = '优惠卷金额不能大于满足的条件！';
            return false;
        }

        if(empty($data['receive_start_time']) || empty($data['use_start_time'])){
            $this->error = '时间不能为空！';
            return false;
        }
        $receive_start_time = strtotime($data['receive_start_time']);
        $receive_end_time   = strtotime($data['receive_end_time']);
        $use_start_time     = strtotime($data['use_start_time']);
        $use_end_time       = strtotime($data['use_end_time']);
        if($receive_start_time > $receive_end_time || $use_start_time>$use_end_time){
            $this->error = '开始时间不能大于结束时间！';
            return false;
        }
        if($receive_end_time > $use_end_time){
            $this->error = '领取结束时间不能大于使用结束时间！';
            return false;
        }
        $insertData['coupon_img']           = $data['coupon_img'];
        $insertData['coupon_name']          = $data['coupon_name'];
        $insertData['payment']              = $data['payment'];
        $insertData['usage_mode']           = $data['usage_mode'];
        $insertData['coupon_amount']        = FormatMoney($data['coupon_amount']);
        $insertData['amount_of_condition']  = FormatMoney($data['amount_of_condition']);
        $insertData['release_quantity']     = $data['release_quantity'];
        $insertData['create_time']          = time();
        $insertData['store_shop_id']        = $this->memberData['store_sts_id'];
        $insertData['receive_start_time']   = $receive_start_time;
        $insertData['receive_end_time']     = $receive_end_time;
        $insertData['use_start_time']       = $use_start_time;
        $insertData['use_end_time']         = $use_end_time;
        $insertData['store_category_idone']  = intval($data['oneCategory']);
        $insertData['store_category_idtwo']  = intval($data['twoCategory']);
        $insertData['store_shop_dishid']     = $data['store_shop_dishid'];   //多个用逗号拼接
        $insertData['inventory']     = 0;//被领取数量，开始时为0
        $insertData['get_limit']     = $data['get_limit'];

        if(empty($id)){
            mysqld_insert('store_coupon', $insertData);
            $res = mysqld_insertid();
        }else{
            $res = mysqld_update('store_coupon',$insertData,array('scid'=>$id));
        }

        if($res){
            return true;
        }else{
            $this->error = '操作失败请稍后再试！';
            return false;
        }

    }
    
    //获取店铺优惠券列表
    public function couponList($pindex,$psize,$wheres='',$fieldstr='',$order='ORDER BY use_end_time asc'){
        if(empty($fieldstr))
            $fieldstr    = 'scid,payment,coupon_amount,amount_of_condition,release_quantity,create_time,store_category_idone,store_category_idtwo,store_shop_id,usage_mode,receive_start_time,receive_end_time,use_start_time,use_end_time,coupon_name,inventory';
        $result          = array();
        $store_where     = " where store_shop_id = {$this->memberData['store_sts_id']} {$wheres} ";
        $sql = "SELECT {$fieldstr} FROM " . $this->table_c  . $store_where;
        $limit  = ("LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $list   = mysqld_selectall($sql . $order . ' ' . $limit);

        $payment_text    = array(1=>'用户',2=>'通用',3=>'活动');
        $usage_mode_text = array(1=>'全场',2=>'分类',3=>'单品');
        foreach($list as $k => $v){
            $payment_key     = $v['payment'];
            $usage_mode_key  = $v['usage_mode'];
            $list[$k]['payment_text']    = $payment_text[$payment_key];
            $list[$k]['usage_mode_text'] = $usage_mode_text[$usage_mode_key];

            $list[$k]['coupon_amount']          = FormatMoney($v['coupon_amount'],0);
            $list[$k]['amount_of_condition']    = FormatMoney($v['amount_of_condition'],0);
        }

        $sql_count = "SELECT count(scid) FROM " . $this->table_c  . $store_where;
        $total     = mysqld_selectcolumn($sql_count);

        $result['list']  = $list;
        $result['total'] = intval($total);
        return $result;
    }
    
    //获取单条店铺优惠券信息
    public function getOneCoupon($id=0,$fields='',$sts_id){
        $fields = $fields ?: '*';
        $data = array();
        $sql  = "select {$fields} from {$this->table_c} where scid = {$id} and store_shop_id={$sts_id}";
        $data = mysqld_select($sql);
        if(empty($data)){
            $this->error = '优惠卷不存在！';
            return false;
        }
        return $data;
    }

    //用户优惠券领取列表
    public function couponMemberList($scid,$pindex,$psize,$wheres='',$fieldstr='',$order='ORDER BY use_time asc'){
        if(empty($fieldstr)){
            $fieldstr = '*';
        }
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