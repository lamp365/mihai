<?php
    namespace service\seller;

    class goodsTypeGroupService extends \service\publicService
    {
        private $memberData;
        private $table;

        function __construct() {
           parent::__construct();
           $this->memberData = get_member_account();
           $this->table      = 'goods_type_group';
       }
        
        //添加分组
        public function addGoodsTypeGroup($data){
            $sub_data = array();
            $sub_data   = array(
                'store_id'    => $this->memberData['store_sts_id'],
                'group_name'  => $data,
                'createtime'  => time(),
                'modifytime'  => time()
            );
            $insertStatus = mysqld_insert($this->table,$sub_data);
            return mysqld_insertid();
        }
        
        //编辑分组
        public function editGoodsTypeGroup($data){
            $sub_data = array();
            $sub_data   = array(
                'group_name'  => $data['group_name'],
                'modifytime'  => time()
            );
            $upStatus = mysqld_update($this->table,$data,array('group_id'=>$data['group_id']));
            
            return $upStatus;
        }
        
        //编辑分组
        public function changeGoodsTypeGroup($data){
            $sub_data             = array();
            $sub_data['status']   = 0;
            $upStatus = mysqld_update($this->table,$sub_data,array('group_id'=>$data['group_id']));
            
            return $upStatus;
        }
        
        //获取分组列表
        public function getGoodsTypeList($_GP,$fields='*',$type=1){
            $where = ' and status = 1';

            $store_id = $_GP['store_type'] > 0?0:$this->memberData['store_sts_id']; 
            
            //$sql = "select {$fields} from ".table($this->table)." where store_id = {$store_id} and group_name != '默认分组' {$where} limit {$_GP['page']},{$_GP['limit']}";
            $sql = "select {$fields} from ".table($this->table)." where store_id = {$store_id} and group_name != '默认分组' {$where}";

            $listData['grouplist'] = mysqld_selectall($sql);
            
            //$GoodsTypeListData = mysqld_select("select count(0) as total from ".table($this->table)." where store_id = {$this->memberData['store_sts_id']} {$where}");
             
            //$listData['total'] = $GoodsTypeListData['total'];
            
            return $listData;
        }
        
    }
?>