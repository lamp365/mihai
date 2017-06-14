<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/21
 * Time: 18:49
 */
namespace service\seller;

class goodstypeService extends \service\publicService
{
    private $memberData;
    private $goodstype;
    private $shopdish;
    
    function __construct() {
       parent::__construct();
       $this->memberData      = get_member_account();
       $this->shopdish        = new \service\seller\ShopDishService();   //宝贝操作对象
   }

    /**
     * 在创建模型前  先判断是否真的没有分组 没有则创建
     * 返回 group_id
     * @param $_GP
     * @return bool
     */
    public function checkGroupBeforeAddtype($_GP)
    {
        $member = get_member_account();
        
        if(empty($_GP['group_id'])){
            $defaultGroupId = mysqld_select("select group_id from ".table('goods_type_group')." where store_id = {$member['store_sts_id']} and is_default = 1 limit 1");
            if($defaultGroupId['group_id'])
            {
                return $defaultGroupId['group_id'];
            }
            
            //首次添加是没有组的 ，只有生成了 模型后，自动创建一个默认组
            $groups       = $this->getGtypeGroups();
            $selfgroup    = $groups['selfgroup'];
            
             $data   = array(
                'group_name'  => '默认分组',
                'store_id'    => $member['store_sts_id'],
                'createtime'  => time(),
                'modifytime'  => time(),
                'is_default'  => 1,
            );
            mysqld_insert('goods_type_group',$data);
            if($last_id = mysqld_insertid()){
                return $last_id;
            }else{
                //操作失败
                $this->error = LANG('COMMON_OPERATION_FAIL');
                return false;
            }
            
            //如果库里有，说明用户没有选择组
            /*
            if($selfgroup){
                $this->error = LANG('COMMON_NAME_NOTNULL','common','分组');
                return false;
            }else{
                //否则自动创建一个默认组
                $data   = array(
                    'group_name'  => '默认分组',
                    'store_id'    => $member['store_sts_id'],
                    'createtime'  => time(),
                    'modifytime'  => time(),
                    'is_default'  => 1,
                );
                mysqld_insert('goods_type_group',$data);
                if($last_id = mysqld_insertid()){
                    return $last_id;
                }else{
                    //操作失败
                    $this->error = LANG('COMMON_OPERATION_FAIL');
                    return false;
                }
            }
            */
        }
        return $_GP['group_id'];
    }
    
    //判断用户是否存在默认分组如果不存在则创建之存在则返回
    public function checkGroupDefault(){
        $member = get_member_account();
        
        $groupData = mysqld_select("select group_id from ".table('goods_type_group')." where store_id = {$member['store_sts_id']} and status = 1 and group_name = '默认分组'");
        if($groupData['group_id'] > 0){
            $groupId = $groupData['group_id'];
        }
        else{
            //否则自动创建一个默认组
            $member = get_member_account();
            $data   = array(
                'group_name'  => '默认分组',
                'store_id'    => $member['store_sts_id'],
                'createtime'  => time(),
                'modifytime'  => time(),
                'is_default'  => 1,
            );
            mysqld_insert('goods_type_group',$data);
            $groupId = mysqld_insertid();
        }
        
        return $groupId;
    }
    
    
    
    /**
     * 添加或者编辑 模型的时候 处理表单提交
     * @param $_GP
     * @param $group_id
     * @return bool
     */
    public function add_goodstype($_GP,$group_id)
    {
        if(empty($_GP['gtype_name'])){
            $this->error = LANG('COMMON_NAME_NOTNULL','common','模型名称');
            return false;
        }
        $data = array(
            'group_id'   => $group_id,
            'name'       => $_GP['gtype_name'],
        );
        if(empty($_GP['hide_id'])){
            //表示添加
            $member = get_member_account();
            $data['store_id']  = $member['store_sts_id'];
            mysqld_insert('goods_type',$data);
            $gtype_id = mysqld_insertid();
        }else{
            mysqld_update('goods_type',$data,array('id'=>$_GP['hide_id']));
            $gtype_id = $_GP['hide_id'];
        }
        return $gtype_id;
    }

    /**
     * 删除模型，显示为不可见
     * @param $_GP
     * @return bool
     */
    public function del_gtype($_GP)
    {
        //找出该规格
        $member = get_member_account();
        $gtype = mysqld_select("select id,store_id from ".table('goods_type')." where id={$_GP['id']} and store_id={$member['store_sts_id']}");
        if(empty($gtype)){
            //不存在
            $this->error = LANG('COMMON_NAME_NOTEXIST','common','该模型');
            return false;
        }
        mysqld_update("goods_type",array('deleted'=>1),array('id'=>$_GP['id']));
        return true;
    }
    
    //真实删除规格
    public function del_true_gtype($_GP){
        $sql   = "delete from ".table('goods_type')." where id = {$_GP['id']}";
        $redata = mysqld_query($sql);
        return $redata;
    }
    
    //彻底删除模型
    public function delete_completely($item_id){
        if($item_id <= 0){
            $this->error = LANG('COMMON_NAME_NOTNULL','common','必要配置ID不存在');
           return false;
        }
        
        $delStatus = mysqld_delete('goodstype_spec_item',array('id'=>$item_id));
        return $delStatus;
    }
    
    /**
     * 获取平台分组以及个人的模型分组
     * sel_type   1 查看系统以及 个人店铺的   2只查系统的  3只查店铺的
     * @return array
     */
    public function getGtypeGroups($sel_type = 1)
    {
        $member   = get_member_account();
        $store_id = $member['store_sts_id'];
        if($sel_type == 1){
            $where = "store_id=0 or store_id={$store_id}";
        }else if($sel_type == 2){
            $where = "store_id=0";
        }else if($sel_type == 3){
            $where = "store_id={$store_id}";
        }
        $the_groups = mysqld_selectall('select group_id,store_id,group_name,createtime from '.table('goods_type_group')." where {$where}");
        $selfGroup  = $pingtaiGroup     = array();
        foreach($the_groups as $group){
            if($group['store_id'] == 0){
                //平台的
                $pingtaiGroup[] = $group;
            }else{
                $selfGroup[]    = $group;
            }
        }

        return array(
            'selfgroup'     => $selfGroup,
            'pingtaigroup'  => $pingtaiGroup,
        );
    }

    /**
     * 根据所有的组来获取 对应所有的模型列表
     * @param $group
     * @return array
     */
    public function getGtypelists($group)
    {
        if(empty($group)){
            return array();
        }
        $gtype_list = array();
        foreach($group as $one_group){
            //根据分组找对应的模型
            if($one_group['store_id']){
                $filed = "id,name,p1,p2,group_id";
                $where = " group_id={$one_group['group_id']} and status=1";
            }else{
                //系统的组
                $filed = "id,name,p1,p2,system_group_id as group_id";
                $where = " system_group_id={$one_group['group_id']} and status=1";
            }

            $gtype = mysqld_selectall("select {$filed} from ".table('goods_type')." where {$where}");
            $gtype_list = array_merge($gtype_list,$gtype);
        }

        return $gtype_list;
    }

    /**
     * 根据单个组 获取对应的模型
     * @param $groupid
     * @return array
     */
    public function getGtypelistsByoneGroup($groupid)
    {
        //获取 该分组下的所有模型
        //先看该组 属于系统的还是 个人店铺的
        $group  = mysqld_select("select store_id from ".table('goods_type_group')." where group_id={$groupid}");
        if(empty($group['store_id'])){
            //系统的
            $filed = "id,name,p1,p2,system_group_id as group_id";
            $where = " system_group_id={$groupid} and status=1";
        }else{
            $filed = "id,name,p1,p2,group_id";
            $where = " group_id={$groupid} and status=1";
        }
        $filed = "id,name,p1,p2,group_id";
        $gtype_info = mysqld_selectall("select {$filed} from ".table('goods_type')." where {$where}");
        return $gtype_info;
    }

    /**
     * 获取规格以及规格项 通过模型id
     * @param $gtype_id
     * @param $dish_id   如果有值，会获取规格价格  之前有编辑过的 就会给一个状态
     * @return array
     */
    public function getSpecAndItemByGtypeid($gtype_id,$dish_id=0)
    {
        if(empty($gtype_id)){
            return array();
        }

        //获取规格对应价格表中 所选择的规格id
        $items_ids = array();
        if($dish_id){
            $items_id  = mysqld_select("select GROUP_CONCAT(`spec_key` SEPARATOR '_') AS items_id from ".table('dish_spec_price')." where dish_id={$dish_id}");
            $items_ids = explode('_', $items_id['items_id']);
        }

        $member    = get_member_account();
        $speclist  = mysqld_selectall("select * from ".table('goodstype_spec')." where gtype_id={$gtype_id} ");
        foreach($speclist as $key=>$one){
            $spec_item_arr = mysqld_selectall("select *,'0' as ischoose from ".table('goodstype_spec_item')." where spec_id={$one['spec_id']} order by status desc");
            if(!empty($items_ids)){
                foreach($spec_item_arr as &$spec_val){
                    if(in_array($spec_val['id'],$items_ids)){
                        $spec_val['ischoose'] = 1;
                    }
                }
            }
            $speclist[$key]['child_item'] = $spec_item_arr;
        }
        return $speclist;
    }
    
    
    public function getSpecAndItemByGtypeids($gtype_id,$dish_id=0)
    {
        //
        if(empty($gtype_id)){
            return array();
        }

        //获取规格对应价格表中 所选择的规格id
        $items_ids = array();
        if($dish_id){
            $items_id  = mysqld_select("select GROUP_CONCAT(`spec_key` SEPARATOR '_') AS items_id from ".table('dish_spec_price')." where dish_id={$dish_id}");
            $items_ids = explode('_', $items_id['items_id']);
        }

        $member    = get_member_account();
        $speclist  = mysqld_selectall("select * from ".table('goodstype_spec')." where gtype_id={$gtype_id} ");
        foreach($speclist as $key=>$one){
            $spec_item_arr = mysqld_selectall("select *,'0' as ischoose from ".table('goodstype_spec_item')." where spec_id={$one['spec_id']} order by id asc");

            if(!empty($items_ids)){
                foreach($spec_item_arr as &$spec_val){
                    if(in_array($spec_val['id'],$items_ids)){
                        $spec_val['ischoose'] = 1;
                    }
                }
            }
            $speclist[$key]['item'] = $spec_item_arr;
        }
        return $speclist;
    }
    
    /**
     * 添加规格
     * @param $_GP
     * @return bool
     */
    public function addspec($_GP)
    {
        if(empty($_GP['gtype_id']) || empty($_GP['spec_name'])){
            $this->error = LANG('COMMON_PARAME_ERR');
            return false;
        }
        $member    = get_member_account();
        $data = array(
            'gtype_id'   => $_GP['gtype_id'],
            'spec_name'  => $_GP['spec_name'],
            'store_id'   => $member['store_sts_id'],
        );
        mysqld_insert('goodstype_spec',$data);
        if($last_id = mysqld_insertid()){
            return $last_id;
        }else{
            $this->error = LANG('COMMON_OPERATION_FAIL');
            return false;
        }
    }

    /**
     * 添加规格具体的项
     * @param $_GP
     * @return bool
     */
    public function addspecitem($_GP)
    {
        if(empty($_GP['spec_id']) || empty($_GP['item_name'])){
            $this->error = LANG('COMMON_PARAME_ERR');
            return false;
        }
        $member    = get_member_account();
        $data = array(
            'spec_id'    => $_GP['spec_id'],
            'item_name'  => $_GP['item_name'],
            'store_id'   => $member['store_sts_id'],
        );
        mysqld_insert('goodstype_spec_item',$data);
        if($last_id = mysqld_insertid()){
            return $last_id;
        }else{
            $this->error = LANG('COMMON_OPERATION_FAIL');
            return false;
        }
    }

    
    /**
     * 添加 发布商品时  录入所新添加的 规格 和规格项
     * @param $_GP
     * @return array|bool
     */
    public function addspecAndItemOnAddGood($_GP)
    {
       /* $respecitemId = array(
            '1G' => 13,
            '2G' => 14,
            '3G' => 17,
            '4G' => 18,
        );
        $respecitem = array(
            'gtype_id'   => 2,
            'gtype_name'   => '内存加规格',
            'spec_info'  => $respecitemId,
        );
//        return $respecitem;*/
        //创建模型前，先确认是否真的没有分组  没有则默认创建一个分组
        $group_id = $this->checkGroupBeforeAddtype($_GP);
        if(!$group_id){
            return false;
        }
        if(empty($_GP['spec_and_item']) || empty($_GP['spec_name_arr'])){
            $this->error = LANG('COMMON_NAME_NOTNULL','common','规格名称');
            return false;
        }
        //添加模型 以两个 规格名字自动组合为 模型名
        if(empty($_GP['gtype_id'])){
            $spec_key_name = array_keys($_GP['spec_and_item']);
            $gtype_name['gtype_name']    = implode('',$spec_key_name);
            $gtype_id = $this->add_goodstype($gtype_name,$group_id);
            if(!$gtype_id)  return false;
        }else{
            $gtype_id   = $_GP['gtype_id'];
            $gtype_name = mysqld_select("select name as gtype_name from ".table('goods_type')." where id={$gtype_id}");

        }


        $respecitemId = array();
        //添加规格  以及 规格项
        foreach($_GP['spec_and_item'] as $spec_name => $spec_item_arr){
            $spec_id = $_GP['spec_name_arr'][$spec_name];
            $spec_inster_data = array(
                'gtype_id'   => $gtype_id,
                'spec_name'  => trim($spec_name),
            );
            if(empty($spec_id)){
                //如果是空的 说明是新添加   否则就是本来规格已经存在
                $spec_id = $this->addspec($spec_inster_data);
            }
            //添加规格项
            if(!empty($spec_item_arr)){
                foreach($spec_item_arr as $item => $item_id){
                    $item_inster_data = array(
                        'spec_id'    => $spec_id,
                        'item_name'  => trim($item),
                    );
                    if(empty($item_id)){
                        //如果是空的 说明是新添加   否则就是本来规格项已经存在
                        $item_id = $this->addspecitem($item_inster_data);
                    }
                    $respecitemId[trim($spec_name)][$item] = $item_id;
                }
            }
        }
  
        $respecitem = array(
            'gtype_id'   => $gtype_id,
            'gtype_name' => $gtype_name['gtype_name'],
            'spec_info'  => $respecitemId,
        );
        return $respecitem;

    }
    
    /***
     * 添加宝贝类型价格
     * 
     * 
     * 
     * ***/
    public function addDishItemPrice($_GP){
        if(empty($_GP['goods_id']) || empty($_GP['spec_key']) || empty($_GP['price']) || empty($_GP['sku']) || empty($_GP['promotion_price'])){
            $this->error = LANG('COMMON_PARAME_ERR');
            return false;
        }
    }
    
    //编辑规格
    public function editspec($data=array(),$id=0){
        
        $redata = array(
            'spec_name'  => $data['spec_name']
        );
        $upStatus = mysqld_update('goodstype_spec',$redata,array('spec_id'=>$id));
        return $upStatus;
    }
    
    //变更规格状态
    public function changespec($id=0){
        
        $redata = array(
            'status'  => 0
        );
        $upStatus = mysqld_update('goodstype_spec',$redata,array('spec_id'=>$id));
        return $upStatus;
    }
    
    //编辑规格项
    public function editSpecItem($data){
        $sub_data = array();
        $sub_data   = array(
            'spec_id'    => $data['spec_id'],
            'id'         => $data['id'],
            'item_name'  => $data['item_name']
        );
        $upStatus = mysqld_update('goodstype_spec_item',$data,array('id'=>$data['id']));

        return $upStatus;
    }
    
    //编辑分组
    public function changeSpecItem($data){
        $sub_data             = array();
        $sub_data['status']   = 0;
        $upStatus = mysqld_update('goodstype_spec_item',$sub_data,array('id'=>$data));

        return $upStatus;
    }
    
    //验证规格项
    public function checkSpec($status=2,$data=array()){
        
        //status 1添加验证 2 编辑验证
        if($status == 1)
        {
            $specData = mysqld_select("select spec_id from ".table('goodstype_spec')." where spec_name = '{$data['spec_name']}' and gtype_id = {$data['gtype_id']} and status = 1");      
            
        }
        elseif($status == 2){
            $specData = mysqld_select("select spec_id from ".table('goodstype_spec')." where spec_name = '{$data['spec_name']}' and gtype_id = {$data['gtype_id']} and spec_id != {$data['spec_id']} and status = 1");
        }
        
        if($specData['spec_id'] > 0)
        {
            return true;
        }
        
        return false;
    }
    
    //验证规格配置项
    public function checkSpecItem($status=2,$data=array()){
        //status 1添加验证 2 编辑验证
        if($status == 1)
        {
            $specItemData = mysqld_select("select id from ".table('goodstype_spec_item')." where item_name = '{$data['item_name']}' and spec_id = {$data['spec_id']}");      
        }
        elseif($status == 2){
            $specItemData = mysqld_select("select id from ".table('goodstype_spec_item')." where item_name = '{$data['item_name']}' and spec_id = {$data['spec_id']} and id != {$data['id']}");
        }
        
        if($specItemData['id'] > 0)
        {
            return true;
        }
        
        return false;
    }
    
    //根据模板ID获取模板和配置项信息
    public function getTemplateSpecItem($goodstypeid){
        $redata = array();
        
        $redata = mysqld_select("select group_id,name as gtype_name,id as gtype_id from ".table('goods_type')." where id = '{$goodstypeid}'");
        
        //获取配置项
        $redata['spec'] = mysqld_selectall("select spec_name,spec_id from ".table('goodstype_spec')." where gtype_id = '{$goodstypeid}' and status = 1 order by spec_id asc");
        foreach($redata['spec'] as $k=>$v){
            $redata['spec'][$k]['item'] = mysqld_selectall("select item_name,id from ".table('goodstype_spec_item')." where spec_id = '{$v['spec_id']}' and status = 1 order by id asc");
        }
        
        return $redata;
        
    }
    
    //根据店铺获取模板的规格
    public function getStoreTemplateSpecItem($_GP){
        $member = get_member_account();
        $redata = array();
        $wheres = '';
       
        $_GP['page'] = max(1, intval($_GP['page']));
        $_GP['limit'] = $_GP['limit']>0?$_GP['limit']:10; 
        $limit = " LIMIT " . ($_GP['page'] - 1) * $_GP['limit'] . ',' . $_GP['limit'];
        
        if($_GP['no_group_id'] > 0)
        {
            $wheres .= " and group_id != {$_GP['no_group_id']}";
            
            //获取店铺的默认分组
            $sql_default = "select group_id from ".table('goods_type_group')." where store_id = '{$member['store_sts_id']}' and is_default = 1";
            $rs_default  = mysqld_select($sql_default);
            if($rs_default['group_id'] > 0)
            {
                $wheres .= " and group_id != {$rs_default['group_id']}";
            }
        }
        else{
            if($_GP['group_id'] > 0 && $_GP['store_type'] != 1)
            {
                $wheres .= " and group_id = {$_GP['group_id']}";
            }
            elseif($_GP['group_id'] > 0){
                $wheres .= " and system_group_id = {$_GP['group_id']}";
            }
        }
        
        if($_GP['store_type'] == 1)
        {
            $_GP['store_id'] = 0;
        }
        else{
            $_GP['store_id'] = $member['store_sts_id'];
        }
        
        $redata['goods_type'] = mysqld_selectall("select id as gtype_id,group_id,name as gtype_name from ".table('goods_type')." where store_id = '{$_GP['store_id']}' and status = 1 {$wheres} {$limit}");
        
        //获取配置项
        foreach($redata['goods_type'] as $k=>$v){
            $redata['goods_type'][$k]['spec'] = mysqld_selectall("select spec_name,spec_id from ".table('goodstype_spec')." where gtype_id = '{$v['gtype_id']}' and status = 1");
         
            if(count($redata['goods_type'][$k]['spec']) > 0)
            {
                foreach($redata['goods_type'][$k]['spec'] as $kk=>$vv){
                    $redata['goods_type'][$k]['spec'][$kk]['item'] = mysqld_selectall("select item_name,id from ".table('goodstype_spec_item')." where spec_id = '{$vv['spec_id']}' and status = 1 order by id asc");
                }
            }
            else{
                $redata['goods_type'][$k]['spec'][$kk]['item'] = array();
            }
        }
        
        
        $redata['total'] = mysqld_select("select count(0) as total from ".table('goods_type')." where store_id = '{$_GP['store_id']}' and status = 1 {$wheres}");
        
        return $redata;
        
    }
    
    //
    public function changeStoreTemplateSpecItem($_GP){
         $data = array();
         $data['status'] = 0;
         $restatus = mysqld_update('goods_type',$data,array('id'=>$_GP['id']));
         return $restatus;
    }
    
    //
    public function changeStoreGroupTemplateSpecItem($_GP){
        $sql = "update ".table('goods_type')." set status = 0 where group_id = ({$_GP['group_id']})";
        $restatus = mysqld_query($sql);

         return $restatus;
         
    }
    
    //
    public function moveStoreTemplateSpecItem($_GP){
        $sql = "update ".table('goods_type')." set group_id = {$_GP['group_id']} where id in ({$_GP['goods_type_idstr']})";
        $restatus = mysqld_query($sql);

         return $restatus;
    }
    
    
    //根据
    public function getGoodInfo($data,$fields='*'){
        $redata = array();

        $sql  = "select {$fields} from ".table('goods_type')." where id = {$data['gtype_id']}";

        $redata = mysqld_select($sql);
        return $redata;
    }
    
    //通过dish获取配置的price选项
     public function getDishSpecPrice($data,$fields='*'){
        $redata = array();
        $sql  = "select {$fields} from ".table('dish_spec_price')." where dish_id = {$data['dish_id']}";
        $redata = mysqld_selectAll($sql);
        return $redata;
    }
    
    public function getSpecData($typeId,$fields='*'){
        $redata = array();
        $sql = "select {$fields} from ".table('goodstype_spec')." where gtype_id = '".$typeId."'";
        $redata = mysqld_selectAll($sql);
        return $redata;
    }
    
    public function getSpecItemDatas($specIds,$fields='*'){
        $redata = array();
        $sql = "select {$fields} from ".table('goodstype_spec_item')." where spec_id in ($specIds)";
        $redata = mysqld_selectAll($sql);
        return $redata;
    }
    
    //goodstype_spec
    public function delSpec($spec_ids){
        $delSql = "delete from ".table('goodstype_spec')." where spec_id in ($spec_ids)";
        $re_status = mysqld_query($delSql);
        return $re_status;
    }
    
    //goodstype_spec_item
    public function delSpecItem($spec_item_ids){
        $delSql = "delete from ".table('goodstype_spec_item')." where id in ($spec_item_ids)";
        $re_status = mysqld_query($delSql);
        return $re_status;
    }
    
    //dish_spec_price
    public function delSpecPrice($dishIds){
        $delPriceSql = "delete from ".table('dish_spec_price')." where dish_id in ($dishIds)";
        $re_status = mysqld_query($delPriceSql);
        return $re_status;
    }
    
    
    //dish_spec_price
    public function delSpecIdPrice($ids){
        $delPriceSql = "delete from ".table('dish_spec_price')." where id in ($ids)";
        $re_status = mysqld_query($delPriceSql);
        return $re_status;
    }
    
    //删除模板
    public function delSpecTemplate($_GP){
        $returnData = 1;
        $delNum = $this->del_true_gtype($_GP); 

        if($delNum > 0){
            //删除配置项 以 配置项对应的所有扩展项
            $specData = $this->getSpecData(intval($_GP['id']),'spec_id');
            $spec_ids = '';
            foreach($specData as $k=>$v)
            {
                $spec_ids .= $v['spec_id'].',';
            }
            $spec_ids = rtrim($spec_ids,',');
            

                
                if($spec_ids != '')
                {
                    $specItemData = $this->getSpecItemDatas($spec_ids,'id');
                    $spec_item_ids = '';
                    foreach($specItemData as $k=>$v)
                    {
                        $spec_item_ids .= $v['id'].',';
                    }
                    $spec_item_ids = rtrim($spec_item_ids,',');
                }

                //获取使用这个模型的宝贝
                $dishIds = '';
                $dishData = $this->shopdish->getGtypeCount($_GP);
                foreach($dishData as $k=>$v)
                {
                    $dishIds .= $v['dish_id'].',';
                }
                $dishIds = rtrim($dishIds,',');

                if($spec_ids != '')
                {
                    //删除配置扩展项 goodstype_spec_item
                    $delSpecStatus = $this->delSpec($spec_ids);

                    //delSpecItem
                    if($spec_item_ids != '')
                    {
                    //删除配置扩展项 goodstype_spec_item
                        $delSpecItemStatus = $this->delSpecItem($spec_item_ids);
                    }
                }

                //删除宝贝价格 dish_spec_price
                if($dishIds != '')
                {
                    //更新为零
                    $sql_goodstype = "update ".table('shop_dish')." set gtype_id = 0 where id in ({$dishIds})";
                    $restatus = mysqld_query($sql_goodstype);

                    $delDishSpecPriceStatus = $this->delSpecPrice($dishIds);
                }
            
                
                
        }
        else{
            $returnData = 0;
        }
        
        return $returnData;
    }
    
    
    //通过dish获取配置的price选项
     public function getDishsSpecPrice($dish_ids,$fields='*'){
        $redata = array();
        $sql  = "select {$fields} from ".table('dish_spec_price')." where dish_id in ({$dish_ids})";
        $redata = mysqld_selectAll($sql);
        return $redata;
    }
    
    //删除配置项商品价格
    public function delSpecnaPrice($specId,$gtypeId=0){
        $data = array();
        $data['gtype_id'] = $gtypeId;
        
        //删除配置项
        $delSpecStatus = $this->delSpec($specId);
        
        //获取配置的扩展项
        $specItemArr = $this->getSpecItemDatas($specId,'id');

        $specItemDataArr = array();
        foreach($specItemArr as $v){
            $specItemDataArr[] = $v['id'];
        }
        $specItemDataStr = implode(',', $specItemDataArr);
        
        //删除配置扩展项
        if($specItemDataStr != '')
        {
            $delSpecItemStatus = $this->delSpecItem($specItemDataStr);
        }
            
        //获取使用的价格
        $dishIds = '';
        $dishData = $this->shopdish->getGtypeCount($data);
        foreach($dishData as $k=>$v)
        {
            $dishIds .= $v['dish_id'].',';
        }
        $dishIds = rtrim($dishIds,',');
        if($dishIds != '')
        {
            $dishDelIds = '';
            $getDishSpecPriceData = $this->getDishsSpecPrice($dishIds,'spec_key,id');

            foreach($getDishSpecPriceData as $v){
                $spec_key_arr = array();
                $spec_key_arr = explode('_', $v['spec_key']);
                foreach($spec_key_arr as $vv)
                {
                    if(in_array($vv, $specItemDataArr)){
                        $dishDelIds .= $v['id'].',';
                    }
                }
            }
            $dishDelIds = rtrim($dishDelIds,',');
            if($dishDelIds != '')
            {
                $delstatus = $this->delSpecIdPrice($dishDelIds);
            }
        }
        return $delstatus;
    }
    
    //删除配置扩展项商品价格
    public function delSpecItemPrice($specItemId,$gtypeId=0){
        $data = array();
        $data['gtype_id'] = $gtypeId;
        
         //删除配置扩展项
        $delSpecItemStatus = $this->delSpecItem($specItemId);
        
        if($delSpecItemStatus > 0)
        {
            //查询
            $dishIds = '';
            $dishData = $this->shopdish->getGtypeCount($data);
            foreach($dishData as $k=>$v)
            {
                $dishIds .= $v['dish_id'].',';
            }
            $dishIds = rtrim($dishIds,',');
            
            //获取使用的价格
            $dishDelIds = '';
            $getDishSpecPriceData = $this->getDishsSpecPrice($dishIds,'spec_key,id');

            foreach($getDishSpecPriceData as $v){
                $spec_key_arr = array();
                $spec_key_arr = explode('_', $v['spec_key']);
                foreach($spec_key_arr as $vv)
                {
                    if($vv == $specItemId){
                        $dishDelIds .= $v['id'].',';
                    }
                }
            }
            $dishDelIds = rtrim($dishDelIds,',');
    
            if($dishDelIds != '')
            {
                $delstatus = $this->delSpecIdPrice($dishDelIds);
            }
        }
        
        return intval($delstatus);
    }
}