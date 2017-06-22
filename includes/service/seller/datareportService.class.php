<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/5/16
 * Time: 18:13
 */
namespace service\seller;

class datareportService extends \service\publicService
{
    private $storeShopVisted;
     function __construct() {
         parent::__construct();
        $this->storeShopVisted = new storeShopVistedService();
        
        
    }


    /**
     * 工作台 获取待收货的金额
     * @param $sts_id
     * @return array
     */
    public function wait_income($sts_id)
    {
        //店铺余额
        $store_info  = member_store_getById($sts_id,'recharge_money');
        $store_money = FormatMoney($store_info['recharge_money'],0);
  
        //等待收货(已经支付 或者 已经发货的 )  的金额 与单数
        $wait_order = mysqld_select("SELECT count(id) as order_num, sum(price) as price FROM ".table('shop_order')." WHERE sts_id={$sts_id} and (status = 1 or status =2)");
        $wait_price = FormatMoney($wait_order['price'],0);
        return array(
            'store_money'  => $store_money,
            'wait_price'   => $wait_price,
            'wait_order_num'=> empty($wait_order['order_num']) ? 0 : $wait_order['order_num'],
        );
    }

    /**
     * 访问量占比
     * @param $sts_id
     * @return array
     */
    public function visted_rate($sts_id)
    {
        // title: ['本店用户','他店用户','其他用户']
        /**rate:[
                {value:100, name:'本店用户'},
                {value:80, name:'他店用户'},
                {value:523, name:'其他用户'}
            ]
       **/
        $title = array('本店用户','他店用户','其他用户');
        $rate  = array(
            array('value'=>180,'name'=>'本店用户'),
            array('value'=>98,'name'=>'他店用户'),
            array('value'=>65,'name'=>'其他用户'),
        );
        $data = array(
            'title'  => $title,
            'rate'  => $rate,
        );

        $app_rate = array(
            'self_store'   => 180,
            'tadian_store' => 98,
            'other_store'  => 98,
            'total_visted' => 46,
        );
        $data['app_rate'] = $app_rate;
        return $data;
    }

    /**
     * 根据时间获取 销售的数据情况   工作台的
     * @param $sts_id
     * @param $type
     * @return array|bool
     */
    public function sales_data_info($sts_id,$type,$data)
    {
        $today_zero  = strtotime(date("Y-m-d"));   //今天凌晨时间点
        switch($type){
            case 'today':
                $s_time = strtotime(date("Y-m-d"));  //今天凌晨时间点
                $e_time = time();
                break;
            case 'yestoday':
                $e_time = $today_zero;               //今天凌晨时间点
                $s_time = $e_time - 3600*24;         //昨天的凌晨
                break;
            case 'week':  //本周  周一到现在
                $curt_week = date('w',time());   //当前所在的week 处在第几天  第七天为0
                if($curt_week == 0){
                    $day = 7;
                }else{
                    $day = $curt_week;
                }
                $e_time = time();
                $s_time = $today_zero - 3600*24*$day;   //推到周一的时间戳
                break;
            case 'month':   //本月
                $curt_day = date('d')-1; //当前天数
                $e_time   = time();
                $s_time   = $today_zero - 3600*24*$curt_day;  //当月1号时间
                break;
            case 'lastmonth':
                $curt_month = date('m')-1;
                $curt_year  = date('Y');
                $s_time   = strtotime($curt_year.'-'.$curt_month.'-1');  //上个月一号
                $endDay   = $curt_year . '-' . $curt_month . '-' . date('t', $s_time);
                $e_time   = strtotime($endDay);    //上个月月末
                break;
            case 'time' :
                if(empty($data['start_time']) || empty($data['end_time'])){
                    $this->error = '时间不能为空！';
                    return false;
                }
                $s_time = strtotime($data['start_time']);
                $e_time = strtotime($data['end_time']);
                if(empty($s_time) || empty($e_time)){
                    $this->error = '时间格式有误！';
                    return false;
                }
                break;
            default :
                $this->error = '类型参数有误！';
                return false;
                break;
        }
        //获取收益 与已经支付订单量
        $pay_info = mysqld_select("SELECT count(id) as order_num, sum(price) as price FROM ".table('shop_order')." WHERE sts_id={$sts_id} and status >= 1 and paytime >=".$s_time." and paytime<=".$e_time);
        $money    = FormatMoney($pay_info['price'],0);
        $pay_num  = $pay_info['order_num'];

        //获取收益 与等待支付订单量
        $wait_info  = mysqld_select("SELECT count(id) as order_num, sum(price) as price FROM ".table('shop_order')." WHERE sts_id={$sts_id} and status = 0 and paytime >=".$s_time." and paytime<=".$e_time);
        $wait_money = FormatMoney($wait_info['price'],0);
        $wait_num   = $wait_info['order_num'];
        
        //转化率  成交订单数量 比去  访问量
        $vistedData = $this->storeShopVisted->bettenTime($s_time, $e_time);
        if(empty($vistedData['uv_sum']))
            $conversion = '0%';
        else
            $conversion = sprintf("%.2f",$pay_num/$vistedData['uv_sum']).'%';

        
        //$conversion = '4.5%';  // $pay_num/(某个时间段（beteen $s_time and $e_time）的UV)
        
        return array(
            'money'       => $money,
            'pay_num'     => $pay_num,
            'wait_money'  => $wait_money,
            'wait_num'    => $wait_num,
            'conversion'  => $conversion,
        );
    }
}