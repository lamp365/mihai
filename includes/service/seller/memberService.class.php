<?php
namespace service\seller;

class memberService extends \service\publicService {
    private $memberData;
    
    function __construct() {
       parent::__construct();
       $this->memberData    = get_member_account();
       $this->member        = table('member');
       $this->memberBlongRelation  = table('member_blong_relation');
       $this->shopOrder         = table('shop_order');
       $this->storeShopLevel         = table('store_shop_level');
       
       //
   }
   
   public function getMemberLists($data){
       $redata = array();
       //
       $openidArr = mysqld_selectAll("select m_openid as openid from ".$this->memberBlongRelation." where p_sid = {$this->memberData['store_sts_id']}");
       
       if(is_array($openidArr))
       {
           $openidStr = '';
           foreach($openidArr as $v){
               $openidStr .= $v['openid'].',';
           }
           $openidStr = rtrim($openidStr,',');
       }
       
       if($openidStr != '')
       {
            $condition = " and openid in ({$openidStr})";
            if($data['mobile'] != '')
            {
                $condition .= " and mobile = {$data['mobile']}";
            }
            if($data['weixin'] != '')
            {
                $condition .= " and weixin = {$data['weixin']}";
            }

            $pindex = max(1, intval($data['page']));
            $psize = 10;
            $limit = " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            $fields = '*';
            $sql = "SELECT {$fields} FROM " . $this->member . " WHERE 1 {$condition} ".$limit;
            $list  = mysqld_selectall($sql);
            
            $total = mysqld_selectcolumn('SELECT COUNT(openid) as total FROM ' . $this->member . " WHERE 1 {$condition}");
            $pager = pagination($total, $pindex, $psize);
       }
       else{
           $list = array();
           $total = 0;
           $pager = '';
       }
       
        $redata['data'] = $list;
        $redata['total'] = $total;
        $redata['pager'] = $pager;
       
        return $redata;
   }
   
   public function getMemberList($data){
       $redata = array();
       //
       $openidArr = mysqld_selectAll("select openid from ".$this->shopOrder." where sts_id = {$this->memberData['store_sts_id']}");
       if(is_array($openidArr))
       {
           $openidStr = '';
           foreach($openidArr as $v){
               $openidStr .= $v['openid'].',';
           }
           $openidStr = rtrim($openidStr,',');
       }
       
       //统计
       $totalOrderSql = "SELECT count(0) as ordernums,sum(price) as totalprice,openid FROM `squdian_shop_order` where sts_id = {$this->memberData['store_sts_id']} GROUP BY openid;";
       $totalOrderData = mysqld_selectAll($totalOrderSql);
       $totalArr = array();
       foreach($totalOrderData as $v){
           $totalArr[$v['openid']] = $v;
       }
       
       if($openidStr != '')
       {
            $condition = " and openid in ({$openidStr})";
            if($data['mobile'] != '')
            {
                $condition .= " and mobile = {$data['mobile']}";
            }
            if($data['weixin'] != '')
            {
                $condition .= " and weixin = {$data['weixin']}";
            }

            $pindex = max(1, intval($data['page']));
            $psize = 10;
            $limit = " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            $fields = '*';
            $sql = "SELECT {$fields} FROM " . $this->member . " WHERE 1 {$condition} ".$limit;
            $list  = mysqld_selectall($sql);
            
            foreach($list as $k=>$v){
                $list[$k]['ordernums']   = $totalArr[$v['openid']]['ordernums'];
                $list[$k]['totalprice']  = $totalArr[$v['openid']]['totalprice'];
            }
            
            $total = mysqld_selectcolumn('SELECT COUNT(openid) as total FROM ' . $this->member . " WHERE 1 {$condition}");
            $pager = pagination($total, $pindex, $psize);
       }
       else{
           $list = array();
           $total = 0;
           $pager = '';
       }
       
        $redata['data'] = $list;
        $redata['total'] = $total;
        $redata['pager'] = $pager;
       
        return $redata;
   }
   
   public function getLevelShopInfo($rank_level,$fields='*'){
       $sql = "select {$fields} from {$this->storeShopLevel} where rank_level = {$rank_level} limit 1";
       $rs  = mysqld_select($sql);
       return $rs;
   }
   
}