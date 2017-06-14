<?php
/**
* 银行卡相关操作
* @author WZW
*/



// 获取卡类型
function bankInfo($card) {
 $bankList =   require_once WEB_ROOT . '/includes/bankList.php';
  $card_8 = substr($card, 0, 8); 
  if (isset($bankList[$card_8])) { 
    return $bankList[$card_8]; 
  } 
  $card_6 = substr($card, 0, 6); 
  if (isset($bankList[$card_6])) { 
    return $bankList[$card_6]; 
  } 
  $card_5 = substr($card, 0, 5); 
  if (isset($bankList[$card_5])) { 
    return $bankList[$card_5]; 
  } 
  $card_4 = substr($card, 0, 4); 
  if (isset($bankList[$card_4])) { 
    return $bankList[$card_4]; 
  } 
  return false; 
} 

// 保存卡信息
function insert_bank($card_id, $name, $openid) {
    $card_type = bankInfo($card_id);
    if (!$card_type) {
      return '银行卡识别失败!';
    }
    $card_t_ary = explode('-', $card_type);
    // if ($card_type[0] == '工商银行') {
    //     $card_type['card_img1'] = '';
    //     $card_type['card_img2'] = '';
    //     $card_type['bg_color'] = '';
    // }

    if (mysqld_select("SELECT card_id FROM ".table('bank')." WHERE card_id='".$card_id."'")) {
        return '该卡号已绑定!';
    }
    $data = array('openid' => $openid, 'card_id' => $card_id, 'bank' => $card_t_ary[0], 'card_type' => $card_t_ary[2], 'card_kind' => $card_t_ary[1], 'name' => $name);
    $i_b = mysqld_insert('bank', $data);

    if ($i_b) {
      return 1;
    }else{
      return '绑定失败!';
    }
}


// luhn算法
function luhn($num) {
    if (!is_numeric($num)) {
        return false;
    }
    $arr_no = str_split($num);
    $last_n = $arr_no[count($arr_no)-1];
    krsort($arr_no);
    $i = 1;
    $total = 0;
    foreach ($arr_no as $n){
        if($i%2==0){
            $ix = $n*2;
            if($ix>=10){
                $nx = 1 + ($ix % 10);
                $total += $nx;
            }else{
                $total += $ix;
            }
        }else{
            $total += $n;
        }
        $i++;
    }
    $total -= $last_n;
    $total *= 9;
    if($last_n == ($total%10)){
        return true;
    }else{
        return false;
    }
}

// 获取银行列表
function get_all_bank() {
  $bank_ary = array(
    '建设银行',
    '工商银行',
    '农业银行',
    '中国银行',
    '农村信用合作社',
    '邮政储蓄银行',
    '交通银行',
    '中信银行',
    '光大银行',
    '华夏银行',    
    '民生银行',    
    '广发银行',    
    '平安银行',    
    '招商银行',    
    '兴业银行',    
    '浦发银行',    
    '恒丰银行',    
    '浙商银行',    
    '上海银行',    
    '福建海峡银行',
    '吉林银行',    
    '汉口银行',          
    '徽商银行',    
    '晋商银行',         
    '江苏银行',    
    '北京银行',    
    '渤海银行'    
    );

    return $bank_ary;
}

/**
 * 检验银卡 是否是合法的
 * @param $num
 * @return bool
 */
function checkBankIsRight($num){
    $url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo={$num}&cardBinCheck=true";
    $res = http_get($url);
    $res = json_decode($res,'true');
    if($res['validated']){
        return $res['bank'];  //返回银卡的标识 用于可能有后续作用
    }else{
        return false;
    }
}

/**
 * 把该用户下的所有银行卡去除默认，当前操作的银行卡进行设置为默认
 * @param $openid
 * @param $bank_id
 */
function set_bank_default($openid,$bank_id){
    mysqld_update('member_bank',array('is_default'=>0),array('openid'=>$openid));
    mysqld_update('member_bank',array('is_default'=>1),array('id'=>$bank_id,'openid'=>$openid));
}