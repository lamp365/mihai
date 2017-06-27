<?php
/* 活动相关模型和产品处理类 */
class ActModel{
	 private $act;
	 private $page;
	 private $auId;
     public function __construct(Activity $act){
         if ( $act instanceof Activity ){
             $this->act = $act; 
		 }else{
             return false;
		 }
	 }
	 public function getModel(){
         return $this->setModel();
	 }
	 public function AuDish(){
         return $this->getAuDish();
	 }
	 // 获取未审核商品
	 private function getAuDish(){
         $actId = $this->act->actId;
		 $psize = $this->act->psize;
		 $page = $this->act->page;
		 if ( !empty( $actId ) ){
              $where = " and a.ac_action_id = ".$actId." ";
		 }    
		 $result = mysqld_selectall("SELECT a.*,b.* FROM ".table('activity_dish')." as a left join ".table('shop_dish')." as b on a.ac_shop_dish = b.id WHERE a.ac_dish_status = 0 {$where} limit ".($page - 1) * $psize . ',' . $psize);
		 $total  = mysqld_selectcolumn("SELECT count(*) FROM ".table('activity_dish')." as a left join ".table('shop_dish')." as b on a.ac_shop_dish = b.id WHERE a.ac_dish_status = 0 {$where}");
		 $this->act->pagination = pagination($total, $page, $psize);
		 return $result;
	 }
	 public function setAuDish(){
         $data = $this->act->findAcData();
		 if ( isset( $data['id'] ) ){
             $id = $data['id'];
			 unset( $data['id'] );
			 mysqld_update('activity_dish', $data, array('ac_dish_id'=>$id) );
		 }
		 return true;
	 }
	 private function setModel(){
		 $actData = $this->act->getActListData();
		 $ac_shop_num = mysqld_selectall("SELECT ac_shop FROM ".table('activity_dish')." WHERE ac_action_id = {$actData['ac_id']} GROUP BY ac_shop ");
		 $actData['ac_shop_num'] = count($ac_shop_num);
		 $actData['ac_dish_num'] = mysqld_selectcolumn("SELECT count(*) FROM ".table('activity_dish')." WHERE ac_action_id = {$actData['ac_id']} and ac_dish_status = 1 ");
		 $actData['status'] = $actData['ac_status'];
		 if ( $actData['ac_status'] != 1 ){
			 $actData['ac_time_info'] = '<span class="label label-default">已关闭</span>';
		 }else{
			 if ( time() >= $actData['ac_time_str'] && time() < $actData['ac_time_end'] ){
				   $actData['status'] = 2;
			       $actData['ac_time_info'] = '<span class="label label-success">正在进行中</span>';
			 }
			 if ( time() < $actData['ac_time_str'] ){
				   $actData['ac_time_info'] = '<span class="label label-warning">还未开始</span>';
			 }
			 if ( time() > $actData['ac_time_end'] ){
				   $actData['ac_time_info'] = '<span class="label label-default">已经结束</span>';
			 }
		 }
		 $actData['ac_time_str']  = date("Y-m-d H:i:s",  $actData['ac_time_str']);
		 $actData['ac_time_end'] = date("Y-m-d H:i:s",  $actData['ac_time_end']);
		 return $actData;
	 }
}