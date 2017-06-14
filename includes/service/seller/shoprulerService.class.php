<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/7
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\seller\goodsService();
if($a->todo()){
//操作成功 则继续业务
}else{
message($a->getError());
}
 */
namespace service\seller;

class shoprulerService extends \service\publicService
{

    /**
     * 获取卖家后台系统菜单的全部规则 没有层级结构
     * @return array|mixed
     */
    public function getAllRule($filed = '')
    {
        if(empty($filed)){
            $filed   = "rule_id,rule_id as id,modname,moddo,modop,url,rule_name,pid as parentid,act_type,sort";
        }
        if (extension_loaded('Memcached')) {
            $mcache      = new \Mcache();
            $menuAllRule = $mcache->get('getSeller_AllRule');
            if(empty($menuAllRule)){
                $menuAllRule  = mysqld_selectall('select '.$filed.' from '.table('seller_rule')." order by sort asc");
                $mcache->set('getSeller_AllRule',$menuAllRule,3600*24*3);
            }
        }else{
            $menuAllRule  = mysqld_selectall('select '.$filed.' from '.table('seller_rule')." order by sort asc");
        }
        return $menuAllRule;
    }
    /**
     * 获取卖家后台系统菜单的规则  带有层级结构
     * @return array
     */
    public function getSystemMenuRule()
    {
        if (extension_loaded('Memcached')) {
            $mcache   = new \Mcache();
            $menulist = $mcache->get('getSeller_SystemMenuRule');
            if(empty($menulist)){
                //获取顶级菜单
                $field        = "rule_id,rule_id as id,modname,moddo,modop,url,rule_name,pid as parentid,act_type,sort";
                $menudata     = mysqld_selectall('select '.$field.' from '.table('seller_rule')." order by sort asc");
                $menulist     = array();
                shopCategoryTree($menulist,$menudata,  0, 1);
                $mcache->set('getSeller_SystemMenuRule',$menulist,3600*24*3);
            }
        }else{
            //获取顶级菜单
            $field        = "rule_id,rule_id as id,modname,moddo,modop,url,rule_name,pid as parentid,act_type,sort";
            $menudata     = mysqld_selectall('select '.$field.' from '.table('seller_rule')." order by sort asc");
            $menulist     = array();
            shopCategoryTree($menulist,$menudata, 0, 1);
        }
        return $menulist;
    }

    /**
     * 卖家所被限制的规则 不同卖家权限不一样
     * @return array|bool
     */
    public function sellerHasRule()
    {
        $rule_ids   = array();
        $other_rule = array();
        $member = get_member_account();
        if($member['store_is_admin']){
            $mc_key = "getSeller_hasRule_0";
        }else{
            //不是最高管理员
            $rule_relation = mysqld_select("select group_id from ".table('seller_rule_relation')." where sts_id={$member['store_sts_id']} and openid='{$member['openid']}'");
            $mc_key = "getSeller_hasRule_".$rule_relation['group_id'];
        }

        if (extension_loaded('Memcached')) {
            $mcache    = new \Mcache();
            $rule_info = $mcache->get($mc_key);
            if(!empty($rule_info)){
                return $rule_info;
            }
        }
        if(empty($member['store_is_admin'])){
            //管理员有最高权限 非管理员 需要找出所属于组的权限
            $rule_relation = mysqld_select("select group_id from ".table('seller_rule_relation')." where sts_id={$member['store_sts_id']} and openid='{$member['openid']}'");
            if(empty($rule_relation)){
                $this->error = '你没有权限！';
                return false;
            }
            $has_rule = $this->getSellerGroup('*',$rule_relation['group_id']);
            //权限已经修改为 逆向思维 录进去的权限反而是需要限制操作的
            /*if(empty($has_rule['rule'])){
                $this->error = '你没有权限！';
                return false;
            }*/

            $rule_ids   = $has_rule['rule'];
            $other_rule = $has_rule['other_rule'];
        }else{
            //权限已经修改为 逆向思维 录进去的权限反而是需要限制操作的
            //id组成的数组
           /* $allMenuRule = $this->getAllRule('rule_id');
            foreach($allMenuRule as $one_rule){
                $rule_ids[] = $one_rule['rule_id'];
            }
            $other_rule  =  \MenuEnum::$sellerActRule;
            foreach($other_rule as &$val){
                $val = 1;
            }*/
            $rule_ids   = array();
            $other_rule = array();
        }
        $rule_info = array(
            'rule_ids'   => $rule_ids,
            'other_rule' => $other_rule,
        );

        if (extension_loaded('Memcached')) {
            $mcache    = new \Mcache();
            $rule_info = $mcache->set($mc_key,$rule_info,time()+3600*24*3);
            if(!empty($rule_info)){
                return $rule_info;
            }
        }
        return $rule_info;
    }

    /**
     * 通过用户请求的url 获取规则id  找不到则是可以访问，能找到就返回该规则id
     * @param $request
     * @return bool|int
     */
    public function findRuleByUrl($url)
    {
        $this_rule_id = 0;
        $find_rule = mysqld_selectall("select rule_id,act_type from ".table('seller_rule')." where url='{$url}'");
        if(empty($find_rule)){
           return $this_rule_id;
        }


        if(count($find_rule) > 1){
            //说明开发人员把  编辑和添加写在一个方法里
            if(empty($request['id'])){
                //添加的请求
                foreach($find_rule as $one){
                    if($one['act_type'] == 1){
                        $this_rule_id = $one['rule_id'];
                    }
                }
            }else{
                //编辑的请求
                foreach($find_rule as $one){
                    if($one['act_type'] == 2){
                        $this_rule_id = $one['rule_id'];
                    }
                }
            }
        } else {
            $this_rule_id = $find_rule[0]['rule_id'];
        }
        return $this_rule_id;
    }


    /**
     * 添加角色分组 或者修改角色分组
     * @param $_GP
     * @return bool
     */
    public function do_addgroup($_GP)
    {
        if (empty($_GP['group_name'])) {
            $this->error = LANG('COMMON_NAME_NOTNULL', '', '分组');
            return false;
        }
//        $member = get_member_account();
//        $sts_id = $member['store_sts_id'];
        $sts_id = 0;
        $rule   = implode(',', $_GP['rule_id']);
        $other_rule = empty($_GP['other_rule']) ? '' : implode(',',$_GP['other_rule']);
        $data = array(
            'group_name'  => $_GP['group_name'],
            'sts_id'      => $sts_id,
            'description' => $_GP['description'],
            'rule'        => $rule,
            'other_rule'  => $other_rule,
            'createtime'  => time(),
        );
        if(empty($_GP['group_id'])){
            mysqld_insert('seller_group', $data);
            $res = mysqld_insertid();
        }else{
            $res = mysqld_update('seller_group',$data,array('group_id'=>$_GP['group_id']));
        }
        if($res){
            return true;
        }else{
            $this->error = LANG('COMMON_OPERATION_FAIL');
            return false;
        }
    }

    /**
     * 添加管理用户
     * @param $_GP
     * @return bool
     */
    public function do_adduser($_GP)
    {
        if(strtolower($_SESSION["addUser"][$_GP['mobile']]) == strtolower($_GP['checkcode'])) {
            unset($_SESSION["addUser"]);
        }else{
            //验证码有误
            $this->error = LANG('COMMON_PHONECODE_ERROR');
            return false;
        }
        if(empty($_GP['group_id'])){
            $this->error = '请选择角色分组';
            return false;
        }
        $result = $this->mobile_isreget($_GP['mobile']);
        if(!$result){
            return false;
        }
        if($result['code'] == 1002){
            //该手机号未注册的  先注册该用户
            $loginService = new \service\shopwap\loginService();
            $memInfo    = $loginService->do_signin($_GP,0);
            if(!$memInfo){
                //注册失败
                return false;
            }

        }else if($result['code'] == 1004){
            //手机号已经存在
            $memInfo  = mysqld_select("select * from ".table('member')." where mobile={$_GP['mobile']}");
        }
        //往 member_store_relation插入一条记录 用户可以登录管理该店铺
        $insert_store = $this->inster_store_relation($memInfo);
        if(!$insert_store){
            return false;
        }
        //往 seller_rule_relation插入一条记录 用户属于某个角色 拥有的权限
        $usercache = get_member_account();
        $relation = array(
          'group_id'      => $_GP['group_id'],
          'sts_id'        => $usercache['store_sts_id'],
          'openid'        => $memInfo['openid'],
          'createtime'    => time(),
        );
        mysqld_insert('seller_rule_relation',$relation);
        if($memInfo['member_type'] != 2){
            //该手机号未注册的  此时已经是商家了  把类型标记为2
            mysqld_update('member',array('member_type'=>2),array('openid'=>$memInfo['openid']));
        }
        return true;
    }

    public function inster_store_relation($memInfo)
    {
        $member = get_member_account();
        //找到店铺法人
        $top_member = mysqld_select("select sts_openid from ".table('store_shop')." where sts_id={$member['store_sts_id']}");
        if(empty($top_member)) {
            $this->error = '信息有误,未找到店铺！';
            return false;
        }
        $is_default = 1;
        if($memInfo['member_type'] == 2){
            //该用户已经有可以管理的店铺了
            $is_default = 0;
        }
        $store_data = array(
            'sts_id'       => $member['store_sts_id'],
            'openid'       => $memInfo['openid'],
            'parent_openid'=> $top_member['sts_openid'],
            'createtime'   => time(),
            'is_default'   => $is_default,
        );
        mysqld_insert('member_store_relation',$store_data);
        if(mysqld_insertid()){
            return true;
        }else{
            $this->error = '用户从属店铺创建失败！';
            return false;
        }
    }

    /**
     * 发送短信验证码
     * @param $telephone
     * @return bool
     */
    public function send_mobile_code($telephone)
    {
        if (isset( $_SESSION['addUser']['sms_code_expired']) && time()< $_SESSION['addUser']['sms_code_expired'] ) {
            $this->error = LANG('COMMON_SMS_IS_ALREADY_SEND');
            return false;
        }

        if(strlen($telephone) != 11 || !is_numeric($telephone)){
            $this->error = LANG('COMMON_PHONE_ERROR');
            return false;
        }
        date_default_timezone_set('Asia/Shanghai');
        $code = set_sms_code($telephone);
        if($code){
            $_SESSION['addUser'] 		 = array();
            $_SESSION['addUser'][$telephone] 		 = $code;
            $_SESSION['addUser']['sms_code_expired'] = time()+120;		//短信的有效期,120s
            return true;
        }else{
            //发送失败
            $this->error = LANG('COMMON_SMS_SEND_FAIL');
            return true;
        }
    }

    /**
     * 验证要添加的子账户 是否注册过  或者是否已经是本店铺的子账户
     * 不能添加 返回 false  否则返回code 1002 手机号不存在 可以添加 但要设置一个密码  1004手机号已存在 但可以添加
     * @param $mobile
     * @return array|bool
     */
    public function mobile_isreget($mobile)
    {
        if(empty($mobile)){
            $this->error = LANG('COMMON_NAME_NOTNULL', '', '手机号');
            return false;
        }
        if(!is_numeric($mobile) || strlen($mobile) != 11){
            $this->error = LANG('COMMON_PHONE_ERROR');
            return false;
        }
        $member = get_member_account();
        if($member['mobile'] == $mobile){
            $this->error = '该手机号已经是管理员';
            return false;
        }
        $mem = mysqld_select("select openid,member_type from ".table('member')." where mobile={$mobile}");
        if(empty($mem)){
            //手机号不存在
            return array('code'=>1002,'message'=>LANG('COMMON_NAME_NOTEXIST','common','手机号'));
        }else{
            //手机号已经存在  是否已经是该店铺的子账户
            $store = mysqld_select("select id from ".table('member_store_relation')." where sts_id={$member['store_sts_id']} and openid='{$mem['openid']}'");
            if(empty($store)){
                //不是该店铺的子账户
                if($mem['member_type'] == 2){
                    //如果他是 一个卖家管理员 是否已经是绑定了其他 法人店铺
                    //找到店铺法人
                    $top_member = mysqld_select("select sts_openid from ".table('store_shop')." where sts_id={$member['store_sts_id']}");
                    //查找要添加的该手机号 法人openid
                    $compare_member = mysqld_select("select parent_openid from ".table('member_store_relation')." where openid='{$mem['openid']}'");
                    if($top_member['sts_openid'] != $compare_member['parent_openid']){
                        //该手机号  已经是其他法人店铺的管理员
                        $this->error = '该手机号已是其他法人店铺的管理员';
                        return false;
                    }
                }
                //可以添加为管理员
                return array('code'=>1004,'message'=>LANG('COMMON_PHONE_EXIST'));
            }else{
                $this->error = '该手机号已在管理列表中';
                return false;
            }
        }
    }

    /**
     * 获取店铺的权限组  或者某个组的信息
     * @param string $field
     * @param string $grop_id  有值则就是查询该组的信息  没值获取所有组
     * @return array|bool|mixed
     */
    public function getSellerGroup($field = "*",$group_id='')
    {
        $member      = get_member_account();
        if(empty($group_id)){
            $sellergroup = mysqld_selectall("select {$field} from ".table('seller_group'));
        }else{
            $sellergroup = mysqld_select("select {$field} from ".table('seller_group')." where  group_id={$group_id}");
            $sellergroup['rule']       = empty($sellergroup['rule']) ? array() : explode(',',$sellergroup['rule']);
            $sellergroup['other_rule'] = empty($sellergroup['other_rule']) ? array() : explode(',',$sellergroup['other_rule']);
        }
        return $sellergroup;
    }

    public function getUserlist()
    {
        $usercache = get_member_account();
        $userlist  = mysqld_selectall("select openid,is_admin,createtime from ".table('member_store_relation')." where sts_id={$usercache['store_sts_id']} order by id asc");
        foreach($userlist as &$one){
            $mem = member_get($one['openid'],'mobile,nickname');
            $one['mobile']   = $mem['mobile'];
            $one['nickname'] = $mem['nickname'];

            if($one['is_admin'] == 1){
                $one['group_name'] = '高级管理';
            }else{
                //获取属组
                $sql = "select g.group_name,r.id rid from ".table('seller_rule_relation')." as r left join ".table('seller_group')." as g ";
                $sql.= " on g.group_id=r.group_id where r.sts_id={$usercache['store_sts_id']} and r.openid='{$one['openid']}'";
                $group = mysqld_select($sql);
                $one['group_name'] = $group['group_name'];
                $one['rid']        = $group['rid'];
            }
        }
        return $userlist;
    }

    public function delSellerGroup($group_id)
    {
        if(empty($group_id)){
            $this->error = '参数有误！';
            return false;
        }
        //查找底下是否有 一些角色
        $find = mysqld_select("select id from ".table('seller_rule_relation')." where group_id={$group_id}");
        if($find){
            $this->error = '请先将改组下的成员移至其他分组';
            return false;
        }
        mysqld_delete('seller_group',array('group_id'=>$group_id));
        return true;
    }

    /**
     * 重店铺中移除用户
     * @param $data
     * @return bool
     */
    public function deluser($data)
    {
        $memInfo = get_member_account();
        $sql = "select * from ".table('seller_rule_relation')."  where id={$data['id']}";
        $the_user = mysqld_select($sql);
        if(empty($the_user) || $the_user['sts_id'] != $memInfo['store_sts_id']){
            $this->error = '该用户不存在!';
            return false;
        }
        if($the_user['openid'] == $memInfo['openid']){
            $this->error = '不能删除自己!';
            return false;
        }
        $res = mysqld_delete('seller_rule_relation',array('id'=>$data['id']));
        if(!$res){
            $this->error = '删除失败!';
            return false;
        }
        //删除成功对应的权限后  再删除跟店铺的关系
        mysqld_delete('member_store_relation',array('sts_id'=>$memInfo['store_sts_id'],'openid'=>$the_user['openid']));
        //查看是否还有店铺 ，如果没有店铺，该用户恢复为一般的用户身份
        $find = mysqld_selectall("select id,is_default from ".table('member_store_relation')." where openid={$the_user['openid']}");
        if(empty($find)){
            mysqld_update('member',array('member_type'=>1),array('openid'=>$the_user['openid']));
        }else{
            //有店铺那么 是否还有默认的 没有默认的要随机处理一个店铺为默认的
            $is_default = 0;
            foreach($find as $one){
                if($one['is_default'] == 1){
                    $is_default = 1;
                }
            }
            if($is_default == 0){
                mysqld_update('member_store_relation',array('is_default'=>1),array('id'=>$find[0]['id']));
            }
        }
        return true;
    }

}