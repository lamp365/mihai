<?php
defined('SYSTEM_IN') or exit('Access Denied');
class shopwapAddons extends BjSystemModule
{
	public function do_control($name=''){
        if ( !empty($name) ){
            $this->__mobile($name);
		}else{
            exit('控制器不存在');
		}
	}
    public function getCartTotal($goodid='')
    {
		if (!empty($goodid) ){
            $where = ' goodsid = '.$goodid.' and ';
		}else{
            $where = '';
		}
        $member = get_member_account(false);
        $openid = $member['openid'];
        $cartotal = mysqld_selectcolumn("select sum(total) from " . table('shop_cart') . " where $where session_id='" . $openid . "'");
        return empty($cartotal) ? 0 : $cartotal;
    }

    public function setOrderCredit($openid, $id, $minus = true, $remark = '')
    {
        $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id='{$id}'");
        if (! empty($order['credit'])) {
            if ($minus) {
                member_credit($openid, $order['credit'], 'addcredit', $remark);
            } else {
                member_credit($openid, $order['credit'], 'usecredit', $remark);
            }
        }
    }

    public function getPaytypebycode($code)
    {
        $paytype = 2;
        if ($code == 'delivery') {
            $paytype = 3;
        }
        if ($code == 'gold') {
            $paytype = 1;
        }
        return $paytype;
    }
    public function time_tran($the_time, $type = 0)
    { 
		if ($type == 0){
            $timediff = $the_time - time();
		}else{
            $timediff = time() - $the_time;
		}
        $days = intval($timediff / 86400);
        if (strlen($days) <= 1) {
            $days = "0" . $days;
        }
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        ;
        if (strlen($hours) <= 1) {
            $hours = "0" . $hours;
        }
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        if (strlen($mins) <= 1) {
            $mins = "0" . $mins;
        }
        $secs = $remain % 60;
        if (strlen($secs) <= 1) {
            $secs = "0" . $secs;
        }
        $ret = "";
        if ($days > 0) {
            $ret .= $days . " 天 ";
        }
        if ($hours > 0) {
            $ret .= $hours . " 时 ";
        }
        if ($mins > 0) {
            $ret .= $mins . " 分 ";
        }
        
        $ret .= $secs . " 秒 ";
        
        return array(
            $ret,
            $timediff
        );
    }
}


