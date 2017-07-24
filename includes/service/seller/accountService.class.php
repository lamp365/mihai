<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/5/16
 * Time: 18:13
 */
namespace service\seller;

class accountService extends \service\publicService
{
    function __construct() {
       parent::__construct();
       $this->store_shop_visted  = table('store_shop_visted');
       
   }
    
    /**
     * 获取提现页面所需要的数据  金额还有上次默认提款账户
     */
    public function outgold()
    {
        $member = get_member_account();
        //获取店铺信息
        $fields    = 'recharge_money';
        $storeInfo = member_store_getById($member['store_sts_id'],$fields);
        //获取法人的 账户信息 上一次最后提款的账户
        if(empty($member['store_is_admin'])){
            $bank_info = array();
        }else{
            $bank_info = mysqld_select("select * from ".table('member_bank')." where openid='{$member['openid']}' and is_default=1");
            //  ************************2355
            if(!empty($bank_info)){
                //获取银行卡的图片
                $bank_bg = mysqld_select("select * from ".table('bank_img')." where bank='{$bank_info['bank_name']}'");
                $bank_info['card_icon'] = $bank_bg['card_icon'];
                $bank_info['card_bg']   = $bank_bg['card_bg'];
                $bank_info['bg_color']  = $bank_bg['bg_color'];

                $weihao   = mb_substr($bank_info['bank_number'], -4, 4, 'utf-8');
                $xing     = str_repeat("*",strlen($bank_info['bank_number'])-4);
                $bank_info['bank_bumber_star'] = $xing.$weihao;
                //  尾号8661
                $bank_info['bank_bumber_wei']  =  $weihao;
            }
        }
        $config = globaSetting();
        return array(
            'recharge_money'  => FormatMoney($storeInfo['recharge_money'],0),
            'draw_money'      => FormatMoney($config['draw_money'],0),      //提款手续费
            'default_bank'    => $bank_info,
        );
    }

    /**
     * 提交提款的表单提交操作
     * @param $data
     * @return bool
     */
    public function do_outgold($data)
    {
        $member     = get_member_account();
        if(empty($member['store_sts_id'])){
            $this->error = '您不是最高管理员！';
            return false;
        }
        if(empty($data['bank_id'])){
            $this->error = '请选择提款账户';
            return false;
        }
        if(empty($data['money']) || !is_numeric($data['money'])){
            $this->error = '金额不能为空并且必须是数字！';
            return false;
        }

        $store_info     = member_store_getById($member['store_sts_id']);
        $recharge_money = $store_info['recharge_money'];
        $config = globaSetting();
        $money  = FormatMoney($data['money']);  //单位是分
        $compare_money = $money + $config['draw_money'];
        if($recharge_money <= $compare_money){
            //提款加上手续费 大于余额
            $limit_mon   = FormatMoney($config['draw_money']);
            $this->error = "您的余额不足以抵扣手续费";
            return false;
        }

        //验证code
        $mobile = $member['mobile'];
        if($_SESSION['mobilecode'][$mobile] != $data['mobilecode']){
            $this->error = '验证码有误！';
            return false;
        }

        //获取提款账户
        $bank_info = mysqld_select("select * from ".table('member_bank')." where id={$data['bank_id']} and openid='{$member['openid']}'");
        if(empty($bank_info)){
            $this->error = '提款账户不存在';
            return false;
        }

        $ordersn    = 'rg'.date('YmdHis') .uniqid();
        mysqld_insert('gold_teller',array(
            'bank_name'=>$bank_info['bank_name'],
            'bank_id'  =>$bank_info['bank_number'],
            'sts_id'   =>$member['store_sts_id'],
            'openid'   =>$member['openid'],
            'fee'      =>$money,
            'status'   =>0,
            'ordersn'    =>$ordersn,
            'draw_money' =>$config['draw_money'],
            'createtime' =>time()
        ));
        if($cash_id = mysqld_insertid()){
            if($bank_info['type'] == 2){
                $remark = LANG('LOG_OUTMONEY_ALI_TIP','paylog');
            }else{
                $weihao  = mb_substr($bank_info['bank_number'], -4, 4, 'utf-8');
                $replace = $bank_info['bank_name']."({$weihao})";
                $remark  = LANG('LOG_OUTMONEY_BANK_TIP','paylog',$replace);
            }
            //记录paylog
            //加上手续费
            $money = $money +  $config['draw_money'];
            $pid   = store_gold($member['store_sts_id'],$money,-1,$remark);

            //本次 账单记录为 提现，还需要记录 账单的 提现id
            mysqld_update('member_paylog',array('cash_id'=>$cash_id,'check_step'=>1),array('pid'=>$pid));

            //把本次的银行卡设置为默认
            set_bank_default($member['openid'],$data['bank_id']);
            $_SESSION['mobilecode'] = array();
            return $mobile;
        }else{
            $this->error = '余额提现申请失败';
            return false;
        }
    }

    /**
     * 工作台根据商铺id 统计对应的商品评价分
     * @param $sts_id
     * @return array
     */
    public function databord_pinfen_rate($sts_id)
    {
        //根据商铺id 统计对应的商品评价分
        $pinfen_rate = array('wl_rate'=>0,'fw_rate'=>0,'cp_rate'=>0,'avg_rate'=>0);
        if(empty($sts_id))  return $pinfen_rate;

        $sql = "select sum(wl_rate) wl_rate,sum(fw_rate) fw_rate,sum(cp_rate) cp_rate,sum(comment_num) comment_num ";
        $sql.= " from ".table('shop_goods_comment_total')." where sts_id={$sts_id}";
        $total_rate = mysqld_select($sql);

        if(!empty($total_rate)){
            $pinfen_rate['wl_rate']  = empty($total_rate['wl_rate'])? 0.00 : round($total_rate['wl_rate']/$total_rate['comment_num'],1);
            $pinfen_rate['fw_rate']  = empty($total_rate['fw_rate'])? 0.00 : round($total_rate['fw_rate']/$total_rate['comment_num'],1);
            $pinfen_rate['cp_rate']  = empty($total_rate['cp_rate'])? 0.00 : round($total_rate['cp_rate']/$total_rate['comment_num'],1);
            $avg_total = $total_rate['wl_rate'] + $total_rate['fw_rate'] + $total_rate['cp_rate'];
            $pinfen_rate['avg_rate'] = empty($avg_total)? 0.00 : round($avg_total/(3*$total_rate['comment_num']),1);
        }
        return $pinfen_rate;
    }

    /**
     * 工作台获取 商铺的或者中的 订单今天的金额数量 昨天的金额以及数量  还有待发货 待支付 退换货数量
     * @param $sts_id
     */
    public function dashbord_data_info($sts_id)
    {
        $where = "1=1";
        if(!empty($sts_id)){
            $where .= " and sts_id={$sts_id}";
        }
        //统计待支付 待发货 退换货申请数量
        $pay_num    = mysqld_selectcolumn("select count(id) from ".table('shop_order')." where {$where} and status=0");
        $send_num   = mysqld_selectcolumn("select count(id) from ".table('shop_order')." where {$where} and status=1");
        $return_num = mysqld_selectcolumn("select count(id) from ".table('shop_order_goods')." where {$where} and status != 0");

        //今天的订单金额  订单量  已经下单的
        $today_zero    = strtotime(date("Y-m-d"));   //今天凌晨时间点
        $today_order   = mysqld_select("SELECT count(id) as order_num, sum(price-store_earn_price-plate_money) as price FROM ".table('shop_order')." WHERE {$where} and status >= 1 and paytime >=".$today_zero);
        $today_price   = FormatMoney($today_order['price'],0);   //钱转为元 页面显示一定要用元

        //昨天的订单金额 订单量  已经下单的
        $yestoday_zero  = strtotime(date("Y-m-d",strtotime("-1 day")));   //昨天的凌晨时间点
        $yestoday_order = mysqld_select("SELECT count(id) as order_num, sum(price-store_earn_price-plate_money) as price FROM ".table('shop_order')." WHERE {$where} and status >= 1 and paytime >=".$yestoday_zero." and paytime<=".$today_zero);
        $yestoday_price   = FormatMoney($yestoday_order['price'],0);    //钱转为元 页面显示一定要用元

        //今天昨天 访问量
        $visted_info    = $this->dashborder_visted_count($sts_id);

        $result_data = array(
            'order_data'    => array('pay_num'=>$pay_num,'send_num'=>$send_num,'return_num'=>$return_num),
            'today_data'    => array('order_num'=>$today_order['order_num'],'price'=>$today_price,'visted'=>$visted_info['today_uv']),
            'yestoday_data' => array('order_num'=>$yestoday_order['order_num'],'price'=>$yestoday_price,'visted'=>$visted_info['yestoday_uv']),
        );
        return $result_data;
    }

    /**
     * 工作台 商品访问量
     * PV（page view）即页面浏览量或点击量
     * UV（unique visitor）即独立访客数
     * 具体是 pv 还是 uv 再定
     * @param $sts_id
     * @return array
     */
    public function dashborder_visted_count($sts_id){
        //今天和昨天的时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        
        //获取昨天和今天
        $sql_today = "select pv_count,uv_count from squdian_store_shop_visted where zero_time = '{$beginToday}' and sts_id = {$sts_id}";
        $rs_today = mysqld_select($sql_today);
        
        $sql_yesterday = "select pv_count,uv_count from squdian_store_shop_visted where zero_time = '{$beginYesterday}' and sts_id = {$sts_id}";
        $rs_yesterday = mysqld_select($sql_yesterday);
        
        if(empty($sts_id)){
            return array('today_uv'=>0,'yestoday_uv'=>0);
        }
        return array('today_uv'=>intval($rs_today['uv_count']),'yestoday_uv'=>intval($rs_yesterday['uv_count']));
    }

    /**
     * 工作台 店铺或者总得 金额折线图 按照7天或者 30天
     * @param $sts_id
     */
    public function dashborder_money_line($sts_id=0,$time='week'){
        //  title:['销售额','联盟广告','视频广告']
        //  x_data:['周一','周二','周三','周四','周五','周六','周日']
        //  y_data:[销售额 => 120, 132, 101, 134, 90, 230, 210]
        $title      = array('销售额');
        $end_time   = strtotime(date("Y-m-d"));   //今夜凌晨时间
        if($time == 'week'){
            $start_time = $end_time - 24*3600*7;
        }else{
            $start_time = $end_time - 24*3600*30;
        }

        $x_data   =  $y_data = $time_arr = array();
        for($i=$start_time;$i<=$end_time;$i = $i+24*3600){
            $x_data[] = date("m月d",$i);
            $time_arr[] = $i;
        }

        $price_data = array();
        $where = "1=1";
        if(!empty($sts_id)){
            $where .= " and sts_id={$sts_id}";
        }
        foreach($time_arr as $time){
            $s_time = $time;
            $e_time = $time+3600*24;
            $price_total = mysqld_select("SELECT sum(price-store_earn_price-plate_money) as price FROM ".table('shop_order')." WHERE {$where} and status >= 1 and paytime >=".$s_time." and paytime<=".$e_time);
            $price_data[] = FormatMoney($price_total['price'],0);
        }
        $y_data['销售额'] = $price_data;

        return array(
            'title'  => $title,
            'x_data' => $x_data,
            'y_data' => $y_data,
        );
    }

    /**
     * 获取账单记录
     */
    public function get_goldrecord($data)
    {
        $memInfo = get_member_account();
        //
        $pindex = max(1, intval($data['page']));
        $psize  = isset($data['limit'])?$data['limit']:20;//默认每页10条数据
        $limit  = ' limit '.($pindex-1)*$psize.','.$psize;
        $where  = "sts_id={$memInfo['store_sts_id']} ";
        $field  = "createtime,fee,openid,orderid,pid,remark,sts_id,type,cash_id,check_step";
        $paylog = mysqld_selectall("select {$field} from ".table('member_paylog')." where {$where} order by pid desc {$limit}");
        if(empty($paylog)){
            $total  = 0;
        }else{
            $total  = mysqld_selectcolumn("select count('pid') from ".table('member_paylog')." where {$where}");
        }

        foreach($paylog as $key => &$one){
            if($one['type'] == 2 || $one['type'] == -2){
                //积分的 去除掉
                unset($paylog[$key]);
            }else{
                $one['fee']         = FormatMoney( $one['fee'],0);
                $one['account_fee'] = FormatMoney( $one['account_fee'],0);
                $one['icon']        = get_paylog_icon($one);
            }
        }
        return array('paylog'=>$paylog,'total'=>$total);
    }

    /**
     * PC获取当前月  的总支出 总收入
     * @param $sts_id
     * @return array
     */
    public function month_paylog_total($sts_id)
    {
        //本月一号时间
        $today_zero = strtotime(date("Y-m-d"));
        $curt_day = date('d')-1; //当前天数
        $s_time   = $today_zero - 3600*24*$curt_day;  //当月1号时间
        $paylog   = mysqld_selectall("select fee,type from ".table('member_paylog')." where sts_id={$sts_id} and createtime>{$s_time}");
        //统计支出的  和收入的
        $data   = array('outmoney' => 0,'inmoney'=> 0);
        $in_arr  = array(1,3);   //收入的金额
        $out_arr = array(-1);    //支出的金额
        foreach($paylog as $item){
            if(in_array($item['type'],$in_arr)){
                $data['inmoney'] = $data['inmoney'] + $item['fee'];
            }
            if(in_array($item['type'],$out_arr)){
                $data['outmoney'] = $data['outmoney'] + $item['fee'];
            }
        }
        //元转为分
        $data['inmoney']  = FormatMoney($data['inmoney'],0);
        $data['outmoney'] = FormatMoney($data['outmoney'],0);
        return $data;
    }

    /**
     * 获取提款记录
     * @param $sts_id
     */
    public function get_cash_record($data)
    {
        $memInfo = get_member_account();
        $pindex  = max(1, intval($data['page']));
        $psize   = isset($data['limit'])?$data['limit']:20;//默认每页10条数据
        $limit   = ' limit '.($pindex-1)*$psize.','.$psize;
        $where   = "sts_id={$memInfo['store_sts_id']} ";
        $data_info = mysqld_selectall("select * from ".table('gold_teller')." where {$where} {$limit}");
        foreach($data_info as &$item){
            $item['fee']        = FormatMoney($item['fee'],0);
            $item['draw_money'] = FormatMoney($item['draw_money'],0);
        }
        $total   = 0;
        if(!empty($data_info)){
            $total = mysqld_selectcolumn("select count(id) from ".table('gold_teller')." where {$where}");
        }
        $pagehtml = pagination($total,$pindex,$psize);
        return array(
            'cash_info' => $data_info,
            'pagehtml'  => $pagehtml,
            'total'     => $total,
        );
    }
}