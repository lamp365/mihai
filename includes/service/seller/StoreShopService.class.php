<?php

/**
 * Author: 王敬
 */

namespace service\seller;

class StoreShopService extends \service\publicService {
    const UPDATE_SHOP_LEVEL_REMARK = '购买店铺等级';//后台付款审核
    /**
     *店铺注册信息第一步
     * $param array('')
     */
    public function step1($data) {
        if($data['id']){
            //如果 id存在，说明是 编辑操作，判断传过来的id是否是自己的店铺
            $res   =  $this->checkIsMyApplyShop($data['id']);
            if(!$res){
                ajaxReturnData(0,$this->getError());
            }
        }

        $userInfo=  get_member_account();
        $data['sts_openid']   = $userInfo['openid'];
        $data['sts_info_status']   = 1;
        if(empty($data['id'])){
            $data['sts_creatime']   = time();
            unset($data['id']);
            mysqld_insert('store_shop_apply', $data);
            $id =  mysqld_insertid();
        }else{
            $id = $data['id'];
            unset($data['id']);
            mysqld_update('store_shop_apply',$data,array('sts_id'=>$id));
        }
        return $id;
    }
     /**
     *店铺注册信息第2步
     * $param array('')
     */
    public function step2($data,$id) {
        $userInfo=  get_member_account();
        //店铺类型1 表示 交收店铺  需要上传资质信息 标记status 12 也就是有第三步
        //其他的类型直接 提交 进入审核 status 2
        $data['sts_info_status']   =  $data['sts_shop_type']==1?12:2;//12代表交收店铺特殊状态
        if( $id ){
            //app  第一步 与第二步 是拆解的
            $effect= mysqld_update('store_shop_apply', $data, array('sts_id' => $id));
            $sts_id = $id;
        }else{
            //pc 第一步与第二步放在一起操作
            $data['sts_openid'] = $userInfo['openid'];
            mysqld_insert('store_shop_apply', $data);
            $sts_id = mysqld_insertid();
        }
        return $sts_id;
    }

    /**
     *店铺注册信息第3步
     * $param array('')
     */
    public function step3($data) {
        $find = mysqld_select('SELECT * FROM ' . table('store_shop_identity_apply') . " WHERE  ssi_id = :ssi_id ", array(':ssi_id'=>$data['ssi_id']));
        if($find){
            $id = $data['ssi_id'];
             mysqld_update('store_shop_identity_apply', $data, array('ssi_id' => $id));
        }else{
            mysqld_insert('store_shop_identity_apply', $data);
            $id = $data['ssi_id'];
        }
        $edata['sts_info_status']   = 2;//步骤三统一2，代表审核中
        mysqld_update('store_shop_apply', $edata, array('sts_id' => $id));
        return $id;
    }
    
    //校验加盟限制，验证是否允许加盟某行业
    public function validateShopNum($reion_code,$cat_p2_id) {
        $Service = new \service\shop\IndustryService();
        $data = $Service->getStoreNumByIndustryAndCode($reion_code,$cat_p2_id);
        if(empty($data['remain'])){
            return false;
        }else{
            return true;
        }
    }
    
    public function checkIsMyApplyShop($sts_id) {
        $userInfo=  get_member_account();
        //查找该商铺id 是否是自己的
        $find = mysqld_select("select sts_id from ".table('store_shop_apply')." where sts_id={$sts_id} and sts_openid='{$userInfo['openid']}'");
        if(empty($find)){
           $this->error = '该id参数有误！';
            return false;
        }
        return true;
    }
    
    public function processInvitationCode($code) {
        //邀请码 就是业务员的手机号码
        if(empty($code)){
            return '';
        }
        $find = mysqld_select("select id from ".table('user')." where mobile='{$code}'");
        if(empty($find)){
            //说明是用户随便输入的
            return '';
        }
        return $code;
    }
    
    public function getMydefaultShop($sts_id,$param="*") {
        if(!$sts_id){return null;}//已注册会员，但是未注册商铺
        $info = mysqld_select("SELECT {$param} FROM " . table('store_shop') . " WHERE  sts_id = :ssi_id ", array(':ssi_id'=>$sts_id));
        if($info){
            return $info;
        }else{
            return null;
        }
    }
    
    

    /**
     * 短息验证码
     * action   bank 则是操做绑定银行卡时
     * action   ali 则是操做绑定支付宝时
     * action   cash 提现操作时
     * @param $telephone
     * @return bool
     */
    public function send_mobile_code($data)
    {
        //判断是是否是管理员操作
        $memInfo = get_member_account();
        if(empty($memInfo['store_is_admin'])){
            $this->error = '只允许最高管理员才可操作！';
            return false;
        }
        $telephone = $memInfo['mobile'];
        if (isset( $_SESSION['mobilecode']['sms_code_expired']) && time()< $_SESSION['mobilecode']['sms_code_expired'] ) {
            $this->error = LANG('COMMON_SMS_IS_ALREADY_SEND');
            return false;
        }

        if(strlen($telephone) != 11 || !is_numeric($telephone)){
            $this->error = LANG('COMMON_PHONE_ERROR');
            return false;
        }
        if(empty($data['action'])){
            $this->error = 'action参数有误！';
            return false;
        }

        //根据不同的类型 发送不同的短信
        date_default_timezone_set('Asia/Shanghai');
        $code = '';
        if($data['action'] == 'bank'){
            //银行卡账户操作的时候
            $code = set_sms_code($telephone);
        }else if($data['action'] == 'ali'){
            //支付宝账户操作的时候
            $code = set_sms_code($telephone);
        }else if($data['action'] == 'cash'){
            //提现 验证码
            $code = set_sms_code($telephone);
        }

        if($code){
            $_SESSION['mobilecode'] 		 = array();
            $_SESSION['mobilecode'][$telephone] 		 = $code;
            $_SESSION['mobilecode']['sms_code_expired']  = time()+120;		//短信的有效期,120s
            return $telephone;
        }else{
            //发送失败
            $this->error = LANG('COMMON_SMS_SEND_FAIL');
            return false;
        }
    }

    /**
     * 获取银行卡列表
     * @return array|bool
     */
    public function get_bank_list()
    {
        $memInfo = get_member_account();
        if(empty($memInfo['store_is_admin'])){
            $this->error = '对不起，你不是高级管理员';
            return false;
        }
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
     * 设置店铺的支付密码
     * @param $data
     * @return bool
     */
    public function set_store_pwd($data){
        $member = get_member_account();
        if(empty($member['store_is_admin'])){
            $this->error = '对不起，你不是最高管理员！';
            return false;
        }
        if($data['pwd'] != $data['repwd']){
            $this->error = '对不起，两次密码不一致！';
            return false;
        }
        if(strlen($data['pwd']) < 6){
            $this->error = '密码长度最少六位';
            return false;
        }
        if(empty($data['mobilecode'])){
            $this->error = '验证码不能为空！';
            return false;
        }
        //获取法人的电话
        $mobile = $member['mobile'];
        //验证code
        if($_SESSION['mobilecode'][$mobile] != $data['mobilecode']){
            $this->error = '验证码有误！';
            return false;
        }
        //获取店铺原来的密码
        $store = member_store_getById($member['store_sts_id'],'sts_tran_passwd');
        if(!empty($store['sts_tran_passwd'])){
            //验证当前输入的 旧密码与之前的密码是否一样
            if($store['sts_tran_passwd'] != encryptPassword($data['old_pwd'])){
                $this->error = '原始密码输入有误！';
                return false;
            }
        }
        mysqld_update('store_shop',array('sts_tran_passwd'=>encryptPassword($data['pwd'])),array('sts_id'=>$member['store_sts_id']));
        $_SESSION['mobilecode'] = array();
        return true;
    }

    /**
     * 添加或者编辑银行卡账户
     * @step  用于app操作的时候 分解动作 1和2  而且是银行卡的时候才分解  支付宝不分解
     * @param $data
     * @return bool
     */
    public function add_zhanghu($data,$step = 0)
    {
        $member = get_member_account();
        if(empty($member['store_is_admin'])){
            $this->error = '对不起，你不是最高管理员！';
            return false;
        }
        if(empty($data['bank_number'])){
            $this->error = '账户号码不能为空！';
            return false;
        }
        //app的第二步揍 不用验证
        if($step != 2){
            if(empty($data['mobilecode'])){
                $this->error = '验证码不能为空！';
                return false;
            }
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

        //获取法人的电话
        $mobile = $member['mobile'];
        /**************app是分解动作的第一次已经验证过了 第二次 不用再验证 *************/
        if($step != 2){
            //验证code
            if($_SESSION['mobilecode'][$mobile] != $data['mobilecode']){
                $this->error = '验证码有误！';
                return false;
            }
        }else{
            if(empty($data['card_own'])){
                $this->error = '持卡人信息未填写！';
                return false;
            }
        }
        /**************app是分解动作的第一次已经验证过了 第二次 不用再验证 *************/


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
        /**************app是分解动作的  第一次 银行卡不入库 只是验证 并返回卡的信息*************/
        if($step == 1 && $data['type'] == 1){
            return $action_data;
        }
        /**************app是分解动作的  第一次 银行卡不入库不入库 只是验证 并返回卡的信息*************/
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
            $res = mysqld_update('member_bank',$action_data,array('id'=>$data['id']));
            $action_data['id'] = $data['id'];
        }
        if($res){
            $_SESSION['mobilecode'] = array();
            return $action_data;
        }else{
            $this->error = '操作失败！';
            return false;
        }
    }

    /**
     * 设置店铺信息
     */
    public function setshop($data)
    {
        $memInfo = get_member_account();
        if(empty($memInfo['store_is_admin'])){
            $this->error = '对不起您不是最高管理员！';
            return false;
        }
        $sts_avatar = '';
        if(!empty($_FILES['sts_avatar'])){
            $upload = file_upload($_FILES['sts_avatar']);
            if (is_error($upload)) {
                $this->error = $upload['message'];
                return false;
            }
            $sts_avatar = $upload['path'];
        }

        /**通过第三级找到城市与省份的code**/
        $regionService = new \service\seller\regionService();
        $cityprovince  = $regionService->getParentsByRegionCode($data['sts_locate_add_3']);

        $update = array();
        $sts_avatar && $update['sts_avatar'] = $sts_avatar;
        $update['sts_physical_shop_name'] = $data['sts_physical_shop_name'];
        $update['sts_contact_name']       = $data['sts_contact_name'];
        $update['sts_mobile']             = $data['sts_mobile'];
        $update['sts_weixin']             = $data['sts_weixin'];
        $update['sts_qq']                 = $data['sts_qq'];
        $update['sts_address']            = $data['sts_address'];
        $update['sts_summary']            = $data['sts_summary'];
        $update['sts_locate_add_1']       = $cityprovince['province'];
        $update['sts_locate_add_2']       = $cityprovince['city'];
        $update['sts_locate_add_3']       = $data['sts_locate_add_3'];
        $update['sts_lat']                = $data['sts_lat'];
        $update['sts_lng']                = $data['sts_lng'];
        $update['commision']                = $data['commision'];

        mysqld_update('store_shop',$update,array('sts_id'=>$memInfo['store_sts_id']));
        return true;
    }
    
    /**
     * 切换店铺
     * @param $data
     * @return bool
     */
    public function changeshop($sts_id)
    {
        $member = get_member_account();
        if(empty($sts_id)){
            $this->error = '对不起，参数有误！';
            return false;
        }
        if($sts_id != $member['store_sts_id']){
            //查看该店铺是否 属于自己管理的
            $store = mysqld_select("select id from ".table('member_store_relation')." where sts_id={$sts_id} and openid='{$member['openid']}'");
            if(empty($store)){
                $this->error = '对不起，该店铺不存在！';
                return false;
            }
            //先去除默认 把当前的设为默认
            mysqld_update("member_store_relation",array('is_default'=>0),array('openid'=>$member['openid']));
            mysqld_update("member_store_relation",array('is_default'=>1),array('sts_id'=>$sts_id,'openid'=>$member['openid']));
            save_member_login('',$member['openid']);
        }
       return true;
    }

    /**
     * 切换店铺的时候 需要展示店铺的简单 订单信息
     * @param $store_info
     * @return mixed
     */
    public function getEachStoreSaleOrder($store_info)
    {
        foreach($store_info as &$one){
            $zero_time = strtotime(date("Y-m-d"));  //凌晨时间戳
            $curt_day  = date('d')-1; //当前天数
            $month_time   = $zero_time - 3600*24*$curt_day;  //当月1号时间
            //今天的销量  和 收益
            $today_order = mysqld_select("SELECT count(id) as order_num, sum(price) as price FROM ".table('shop_order')." WHERE sts_id={$one['sts_id']} and status >=1 and  paytime>={$zero_time}");
            //本月的销量  和 收益
            $month_order = mysqld_select("SELECT count(id) as order_num, sum(price) as price FROM ".table('shop_order')." WHERE sts_id={$one['sts_id']} and status >=1 and  paytime>={$month_time}");

            $one['today_order_num']   = intval($today_order['order_num']);
            $one['today_order_price'] = FormatMoney($today_order['price'],0);
            $one['month_order_num']   = intval($month_order['order_num']);
            $one['month_order_price'] = FormatMoney($month_order['price'],0);
        }
        return $store_info;
    }

    /**
     * 设置通用详情为默认
     * @param $data
     * @return bool
     */
    public function commondetail_default($id,$position)
    {
        $member = get_member_account();
        if(empty($id)){
            if(empty($position)){
                $this->error = 'position和ID参数不能同时为空！';
                return false;
            }else{
                //全部去除该 position下的默认
                mysqld_update("shop_dish_commontop",array('is_default'=>0),array(
                    'sts_id'   => $member['store_sts_id'],
                    'position' => $position,
                ));
                return true;
            }
        }

        //查找是否是自己的
        $find   = mysqld_select("select id,position from ".table('shop_dish_commontop')." where id={$id} and sts_id={$member['store_sts_id']}");
        if(empty($find)){
            $this->error = '该模板不存在';
            return false;
        }
        //把其他的全部设置为 非默认，当前的设置为默认
        mysqld_update("shop_dish_commontop",array('is_default'=>0),array('sts_id'=>$member['store_sts_id'],'position'=>$find['position']));
        mysqld_update("shop_dish_commontop",array('is_default'=>1,'modifiedtime'=>time()),array('id'=>$id));
        return true;
    }

    /**
     * 删除通用详情
     * @param $data
     * @return bool
     */
    public function commondetail_del($id)
    {
        if(empty($id)){
            $this->error = 'id参数不能为空！';
            return false;
        }
        $member = get_member_account();
        //查找是否是自己的
        $find   = mysqld_select("select id,position from ".table('shop_dish_commontop')." where id={$id} and sts_id={$member['store_sts_id']}");
        if(empty($find)){
            $this->error = '该模板不存在';
            return false;
        }
        mysqld_delete("shop_dish_commontop",array('id'=>$id));
        return true;
    }
    /**
     * 添加通用详情
     * @param $data
     * @return bool
     */
    public function commondetail_add($data)
    {
        if(empty($data['temp_name'])){
            $this->error = '模板名称不能为空！';
            return false;
        }
        if(!in_array($data['position'],array(1,2))){
            $this->error = 'position参数有误！';
            return false;
        }
        $member = get_member_account();
        $act_data = array(
            'position'   => $data['position'],
            'temp_name'  => $data['temp_name'],
            'sts_id'     => $member['store_sts_id'],
            'createtime'   => time(),
            'modifiedtime' => time()
        );


        if(empty($data['id'])){
            //添加的  图片必须给
            $upload = file_upload($_FILES['picurl']);
            if (is_error($upload)) {
                $this->error = $upload['message'];
                return false;
            }
            $act_data['picurl'] = $upload['path'];
        }else{
            //修改的 只要图片存在上传 就操作
            if (!empty($_FILES['picurl']['tmp_name'])) {
                $upload = file_upload($_FILES['picurl']);
                if (is_error($upload)) {
                    $this->error = $upload['message'];
                    return false;
                }
                $act_data['picurl'] = $upload['path'];
            }
        }


        if(empty($data['id'])){
            mysqld_insert('shop_dish_commontop',$act_data);
            $id = mysqld_insertid();
        }else{
            $id = $data['id'];
            mysqld_update('shop_dish_commontop',$act_data,array('id'=>$id));
        }

        if($id){
            return true;
        }else{
            $this->error = '操作有误！';
            return false;
        }

    }
    /**
     * 增加店铺退货地址
     * $type 1增加 2修改
     * $data 插入的数据 array
     * $condition 条件
     *   */
    public function addreturnAddress($data=array()){
        if (!empty($data)){
            $info = $this->viewreturnAddress();
            if (empty($info)){
                $member=get_member_account();
                $data['store_id'] = $member['store_sts_id'];
                mysqld_insert('store_return_address',$data);
                return true;
            }else {
                mysqld_update('store_return_address',$data,array('store_id'=>$info['store_id']));
                return true;
            }
        }
    }
    /**
     * 查询店铺退货表 返回一条数据
     * $condition 条件 
     *   */
    public function viewreturnAddress($condition=array(),$param="*",$front="AND"){
        $member=get_member_account();
        $condition['store_id'] = $member['store_sts_id'];
        $condition = to_sqls($condition,$front);
        $sql = "SELECT {$param} FROM ".table('store_return_address')." WHERE {$condition}";
        $info = mysqld_select($sql);
        if ($info) {
            return $info;
        }
    }
}
