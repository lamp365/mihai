<?php
/* 活动处理类 */
class Activity {
   public   $actId;
   private  $actData;
   private  $actListData;
   public   $page;
   public   $psize;
   public   $limie;
   public   $reason;
   public   $errno;
   public   $pagination;
   private  $model;
   public function __construct(){
       $this->psize  = 20;
	   $this->setPage();
	   $this->model = new ActModel($this);
   }
   public function getReason(){
       if ( !empty( $this->reason ) ){
            return $this->reason;
	   }else{
            return '未标记原因';
	   }
   }
   public function getActListData(){
       return $this->actListData;
   }
   public function getAuReason(){
       $reason = array(
            '商品信息资料不全', '商品图片信息不全', '商品价格不符合特卖要求', '商品标题信息有问题'
	   );
	   return $reason;
   }
   public function setPage($page = 1){
       $this->page = max(1, $page);
   }
   public function getAct($id = 0){
	   $this->actId = $id;
       return  $this->getActData();
   }
   public function findAcData(){
       return $this->actData;
   }
   public function getAuDish(){
       return $this->model->AuDish();
   }
   public function setAct($data = array()){
         if ( !empty($data) && is_array($data) ){
	        $this->actData = $data;
			return $this->setActData();
		 }else{
            return false;
		 }
   }
   public function findDiffArea(){
	   $area_set   = array(2,3,4,6);
       $area_find  = $this->findArea();
	   $diff = array_diff($area_set , $area_find);
       return $diff;
   }
   public function setDiffArea($area_id){
       if ( empty( $area_id ) ){
           return false;
	   }
	   $this->setArea($area_id);
	   return true;
   }
   private function setArea($area_id){
       $area = 24 / $area_id;
	   $secon = 3600;
	   $timezone = strtotime("00:00:00");
	   for ( $time = $area_id; $time <= 24 ; $time += $area_id ){
            $secons = $area_id * $secon -1 ;  
            $time_str  = $timezone;
			$time_end = $timezone + $secons;
            $timezone = $time_end + 1;
		    $area_data = array(
				 'ac_area_title'       => date('H:i:s', $time_str)."-".date('H:i:s', $time_end),
                 'ac_area_time_str'   => $time_str,
				 'ac_area_time_end'  => $time_end,
				 'ac_list_id'          =>  $area_id
			);
			mysqld_insert('activity_area', $area_data);
	   }
   }
   public function findArea($id = 0){
	   if ( empty($id) ){
		   $area_result = mysqld_selectall("SELECT ac_list_id from ".table('activity_area')." GROUP BY ac_list_id ");
		   $area_find   = array();
		   if ( is_array( $area_result ) ){
				 foreach ( $area_result as $area_result_value ){
					 $area_find[] = $area_result_value['ac_list_id'];
				 }
		   }
	   }else{
           $area_find = mysqld_select("SELECT * FROM ".table('activity_area')." limit 1 ");
	   }
	   return $area_find;
   }
   public function setAuDish($data = array()){
        if ( !empty($data) && is_array($data) ){
	        $this->actData = $data;
			return $this->model->setAuDish();
		}else{
            return false;
		}
   }
   public function findIndustry(){
        if ( !empty($this->actId) ) {
             $where = " and ac_action_id = ".$this->actId;
		}
		$industry = mysqld_selectall("SELECT a.ac_in_id,b.gc_name FROM ". table("activity_dish")." as a left join ".table("industry")." as b on a.ac_in_id = b.gc_id where a.ac_dish_status = 0 {$where} group by a.ac_in_id ");
		return $industry;
   }
   public function findCategory(){
         if ( !empty($this->actId) ) {
             $where = " and ac_action_id = ".$this->actId;
		}
		$category = mysqld_selectall("SELECT ac_p1_id, ac_p2_id FROM ".table("activity_dish")." where ac_dish_status = 0 {$where} group by ac_p2_id ");
   }
   private function getActData(){
	   if ( $this->actId == 0 ){
           $actData = mysqld_selectall("SELECT * FROM ".table("activity_list")." order by ac_time_end limit ".($this->page - 1) * $this->psize . ',' . $this->psize);
		   if ( $actData && is_array($actData) ){
                foreach ( $actData as &$actData_value ){
					$actData_value = $this->timeOfActData($actData_value);
				}
				unset($actData_value);
		   }
	   }else{
           $actData = mysqld_select("SELECT * FROM ".table("activity_list")." WHERE ac_id = ".$this->actId." limit 1 " );
		   if ( !$actData ){
			   $this->errno = '找不到这条数据';
               return false;
		   }else{
               $actData = $this->timeOfActData($actData);
		   }
	   }
	   return $actData;
   }
   // 校验时间，避免出现时间叠加
   private function CheckActData(){
	   $check = true;
       foreach( $this->actData as $key=>$actData_value ){
		   // 处理傻逼时间数据
		   if ( $this->actData['ac_time_str'] > $this->actData['ac_time_end'] ){
			    $this->actData['ac_time_str']=$this->actData['ac_time_str']^$this->actData['ac_time_end'];
                $this->actData['ac_time_end']=$this->actData['ac_time_end']^$this->actData['ac_time_str'];
                $this->actData['ac_time_str']=$this->actData['ac_time_str']^$this->actData['ac_time_end'];
		   }
           if ( empty( $actData_value ) && $actData_value != 0 ){
               $check =false;
		   }
	   }
	   if ( !empty( $this->actId ) ){
           $C = " and ac_id != $this->actId ";
	   }else{
           $C = "";
	   }
	   if ( $this->actData['ac_status'] == 1 ){
			   $result = mysqld_select(" SELECT * FROM ". table('activity_list'). " WHERE not(ac_time_str > '{$this->actData['ac_time_end']}' or ac_time_end < '{$this->actData['ac_time_str']}') and ac_status = 1 {$C}" );
			   if ( $result ){
					$this->errno = '该活动所选择的时间重叠';
					$check = false;
			   }
	   }
	   return $check;
   }
   private function timeOfActData($actData){
	   $this->actListData = $actData;
	   return $this->model->getModel();
   }
   private function setActData(){
       if ( is_array( $this->actData ) && !empty( $this->actData ) ){
		    if ( !($this->CheckActData()) ){
			   return false;
			}
            if ( !empty( $this->actId ) ){
               $result = mysqld_update("activity_list", $this->actData, array('ac_id'=> $this->actId));
			}else{
               $result = mysqld_insert("activity_list", $this->actData);
			}
			if ( ! $result ){
                $this->errno = '数据出错';
				return false;
			}
            return true;
	   }else{
		   $this->errno = '数据出错';
           return false;
	   }
   }
}