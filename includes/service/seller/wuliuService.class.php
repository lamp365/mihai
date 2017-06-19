<?php
/**
物流的service层
 */
namespace service\seller;
use service\publicService;
class wuliuService extends publicService
{
    public $sts_id;
    public function __construct(){
        parent::__construct();
        $member = get_member_account();
        $this->sts_id = $member['store_sts_id'];
    }

    /**
     * 根据店铺id获取物流列表
     * @param $type return type 返回类型，1表示返回店铺物流id，2表示返回店铺物流id和名称
     * **/
    public function getStoreDispatchList($type="1"){
        $myDispatch = mysqld_select("SELECT dispatch_id from " .table('store_extend_info'). " where store_id =:sts_id" ,array('sts_id'=>$this->sts_id));
        $dispatchId = array();
        if (!empty($myDispatch['dispatch_id'])){
            $dispatchId = explode(",", $myDispatch['dispatch_id']);
        }
        if ($type == 2){
            $dispatchList = mysqld_selectall("SELECT id,code,name,enabled from " .table('dispatch'). " where enabled = 1 and id IN(".$myDispatch['dispatch_id'].")");
            return $dispatchList;
        }elseif ($type == 1){
            return $dispatchId;
        }
    }
    /**
     * 获取所有物流列表
     * ***/
    public function getAllDispatchList(){
        $result = mysqld_selectall("SELECT id,code,name,enabled from " .table('dispatch'). " where enabled = 1");
        return $result;
    }
    /**
     * 新增店铺物流
     * @param string $dispatchId 物流id ，逗号连接的字符串
     * **/
    public function addStoreDispatch($dispatchId =''){
        $return = mysqld_update('store_extend_info',array('dispatch_id'=>$dispatchId),array('store_id'=>$this->sts_id));
        return true;
    }
    /**更新 store_shop表
     * $condition  array 条件
     * $data array 更改的数据
     *   */
    public function updateStoreShop($data=array(),$condition=array()){
        if (!empty($data)){
            $condition['store_id']=$this->sts_id;
            $res = mysqld_update('store_extend_info',$data,$condition);
            return $res;
        }
    }
    /**
     * 通过物流code获取物流信息
     * 
     * @param $com 物流code */
    public function getExpressNameByCode($com){
        if ($com){
            $info = mysqld_select("SELECT id,code,name FROM ".table('dispatch')." where code=:code",array('code'=>$com));
            return $info;
        }
    }
    
}