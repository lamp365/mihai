<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\shopwap\loginService();
if($a->todo()){
    //操作成功 则继续业务
}else{
    message($a->getError());
}
 */
namespace service\shopwap;

class accountService extends \service\publicService
{
   public function addBank($data)
   {
       $member = get_member_account();
       if(empty($data['bank_number'])){
           $this->error = '账户号码不能为空！';
           return false;
       }

       if(!in_array($data['type'],array(1,2))){
           $this->error = '类型参数有误！';
           return false;
       }
       if($data['type'] == 1){
           if(!checkBankIsRight($data['bank_number'])){
               $this->error = '银行卡不是合法的！';
               return false;
           }
       }

       if(empty($data['card_own'])){
           $this->error = '持卡人信息未填写！';
           return false;
       }

       $card_info = array();

       if($data['type'] == 1){
           //银行卡
           $card_info = bankInfo($data['bank_number']);
           $card_info = explode('-',$card_info);
           $bank_name = '';
       }else{
           $bank_name = '支付宝';
       }

       $bank_name = $bank_name ?: $card_info[0];  //建设
       $card_type = $card_info[1];  //龙卡通
       $card_kind = $card_info[2];  //储蓄卡  借记卡

       $action_data  = array(
           'bank_name'   => $bank_name,
           'openid'      => $member['openid'],
           'bank_number' => $data['bank_number'],
           'card_type'   => $card_type,
           'card_kind'   => $card_kind,
           'card_own'    => $data['card_own'],
           'type'        => intval($data['type']),
       );

       if(empty($data['id'])){
           mysqld_insert('member_bank',$action_data);
           $res = mysqld_insertid();
           if($res){
               //把当前的卡设置为 默认
               set_bank_default($member['openid'],$res);
           }
           $action_data['id'] = $res;
       }else{
           //编辑
           $find = mysqld_select("select * from ".table('member_bank')." where id={$data['id']} and openid='{$member['openid']}'");
           if(empty($find)){
               $this->error = '账户不存在！';
               return false;
           }
           $res = mysqld_update('member_bank',$action_data,array('id'=>$data['id']));
           $action_data['id'] = $data['id'];
       }
       if($res){
           return $action_data;
       }else{
           $this->error = '操作失败！';
           return false;
       }
   }

    public function get_bank_list()
    {
        $memInfo = get_member_account();
        $bank_array = array('all'=>array(),'bank'=>array(),'ali'=>array());
        $bank_list  = mysqld_selectall("select * from ".table('member_bank')." where openid='{$memInfo['openid']}'");
        //在获取卡的 对应背景图
        foreach($bank_list as $item){

            //获取银行卡图片
            $bank_bg = mysqld_select("select * from ".table('bank_img')." where bank='{$item['bank_name']}'");
            $item['card_icon'] = $bank_bg['card_icon'];
            $item['card_bg']   = $bank_bg['card_bg'];
            $item['bg_color']  = $bank_bg['bg_color'];

            //  ************************2355
            $weihao   = mb_substr($item['bank_number'], -4, 4, 'utf-8');
            $xing     = str_repeat("*",strlen($item['bank_number'])-4);
            $item['bank_bumber_star'] = $xing.$weihao;
            //  尾号8661
            $item['bank_bumber_wei']  =  $weihao;

            $bank_array['all'][] = $item;
            if($item['type'] == 1){
                $bank_array['bank'][] = $item;
            }else if($item['type'] == 2){
                $bank_array['ali'][] = $item;
            }
        }

        return $bank_array;
    }

    /**
     * 提交提款的表单提交操作
     * @param $data
     * @return bool
     */
    public function do_outgold($data)
    {
        $member     = get_member_account();
        if(empty($data['bank_id'])){
            $this->error = '请选择提款账户';
            return false;
        }
        if(empty($data['money']) || !is_numeric($data['money'])){
            $this->error = '金额不能为空并且必须是数字！';
            return false;
        }

        $gold   = $member['gold'];
        $config = globaSetting();
        $money  = $data['money'];
        $compare_money = $config['teller_limit'];
        if($gold <= $compare_money){
            $this->error = "您的余额小于最低提款金额";
            return false;
        }

        if($gold <= $money){
            $this->error = "您的提款大于您的余额";
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
            'openid'   =>$member['openid'],
            'fee'      =>$money,
            'status'   =>0,
            'ordersn'    =>$ordersn,
            'createtime' =>time()
        ));
        if($cash_id = mysqld_insertid()){
            if($bank_info['type'] == 2){
                $replace = $bank_info['bank_number'];
                $remark  = \PayLogEnum::getLogTip('LOG_OUTMONEY_ALI_TIP',$replace);
            }else{
                $weihao  = mb_substr($bank_info['bank_number'], -4, 4, 'utf-8');
                $replace = $bank_info['bank_name']."({$weihao})";
                $remark  = \PayLogEnum::getLogTip('LOG_OUTMONEY_BANK_TIP',$replace);
            }
            //记录paylog
            $pid   = member_gold($member['openid'],$money,'usegold',$remark);

            //本次 账单记录为 提现，还需要记录 账单的 提现id
            mysqld_update('member_paylog',array('cash_id'=>$cash_id,'check_step'=>1),array('pid'=>$pid));

            //把本次的银行卡设置为默认
            set_bank_default($member['openid'],$data['bank_id']);
            return true;
        }else{
            $this->error = '余额提现申请失败';
            return false;
        }
    }
}