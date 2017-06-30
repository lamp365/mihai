<?php
/**
微信限时购活动
 */
namespace service\wapi;

class activityService extends \service\publicService
{
    //热搜词获取
    public function gethot($catid){
        $where = '';
        if ($catid > 0) {
            $where['classify_id'] = $catid;
        }
        $HotModel = new \model\shop_hottopic_model();
        $info = $HotModel->getAllShopHot($where,'*',"rand()");
        if($info){
            $hottopic = '';
            foreach ($info as $v){
                $hottopic .= $v['hottopic'].";";
            }
            if ($hottopic){
                $hottopic = rtrim($hottopic,";");
                $hottopic = explode(';', $hottopic);
                if (count($hottopic) > 16){
                    $num = 20;
                }else {
                    $num = count($hottopic);
                }
                for ($i = 0; $i < $num; $i++){
                    $data[$i] = $hottopic[$i];
                }
            }
        }
        
        return $data;
    }
    /**
     * 取一个活动未过期的时间段
     * @param $ac_list_id 区域码
     * @param $flag 为空则取当天的未过期，不为空则取第二天的
     */
    public function getActArea($ac_list_id,$flag=''){
        $actAreaModel = new \model\activity_area_model();
        //取时间段
        $activty_area = $actAreaModel->getAllActArea(array('ac_list_id'=>$ac_list_id));
        if (empty($activty_area)) return '';
        if (empty($flag)){
            $mydate = date("Y:m:d");
        }else {
            $mydate = date("Y-m-d",strtotime("+1 day"));
        }
        foreach ($activty_area as $key=>$val){
            $startDate = $mydate." ".date('H:i:s',$val['ac_area_time_str']);
            $endDate = $mydate." ".date('H:i:s',$val['ac_area_time_end']);
            $starttime = strtotime($startDate);
            $endtime = strtotime($endDate);
            if ($endtime <= time()) continue;//过期的筛选出
            $temp['ac_area_id'] = $val['ac_area_id'];
            $temp['ac_area_time_str'] = date("H:i",$starttime);
            $temp['ac_area_time_end'] = date("H:i",$endtime);
            $temp['status'] = 0;
            if (time() >= $starttime && time() <= $endtime){
                $temp['status'] = 1;
                $temp['section'] = $endtime-time();
            }else{
                $temp['section'] = $starttime-time();
            }
            $data[] = $temp;
        }
        return $data;
    }
    /**
     * 取当前活动的当前时间的区域
     *   */
    public function getCurrentArea(){
        $list = getCurrentAct();
        if (!empty($list)) $ac_area = $list['ac_area'];
        if (empty($ac_area)) return '';
        $actAreaModel = new \model\activity_area_model();
        //取时间段
        $activty_area = $actAreaModel->getAllActArea(array('ac_list_id'=>$ac_area));
        if (empty($activty_area)) return '';
        if (empty($flag)){
            $mydate = date("Y:m:d");
        }else {
            $mydate = date("Y-m-d",strtotime("+1 day"));
        }
        foreach ($activty_area as $v){
            $startDate = $mydate." ".date('H:i:s',$v['ac_area_time_str']);
            $endDate = $mydate." ".date('H:i:s',$v['ac_area_time_end']);
            $starttime = strtotime($startDate);
            $endtime = strtotime($endDate);
            if (time() >= $starttime && time() <= $endtime){
                return $v['ac_area_id'];
            }
        }
    }
   /**
    * 根据时间区域id，判断该时间区域是否有商品
    * @param $ac_id 活动id
    * @param $areaid 区域id
    * @param jd 经度
    * @param wd 纬度 
    *   */
    public function checkIsGoods($ac_id,$areaid,$jd='',$wd=''){
        if (empty($ac_id) || empty($areaid)) return false;
        //获得当前时间的区域id
        $currentId = $this->getCurrentArea();
        if (!$currentId) return false;
        $sql = "SELECT count(*) as num from ".table('activity_dish')." AS a LEFT JOIN ".table('shop_dish')." AS b on a.ac_shop_dish=b.id where ";
        $sql .= " a.ac_action_id='$ac_id' and a.ac_dish_status=1 and b.status=1 ";
        if (empty($jd) || empty($wd)){
            $cityCode = getCityidByIp();
            $sql .=" and (a.ac_city='$cityCode' or a.ac_city=0)";
        }else{
            $jdwd = getAreaid($jd,$wd);
            if (empty($jdwd)) return false;
            if ($jdwd['status'] == 0) {
                $cityCode = $jdwd['ac_city'];
                $sql .=" and (a.ac_city='$cityCode' or a.ac_city=0)";
            }else{
                $ac_city = $jdwd['ac_city'];
                $ac_city_area = $jdwd['ac_city_area'];
                //$sql .= " and IF(a.ac_city='$ac_city',a.ac_city_area='$ac_city_area' or a.ac_city_area=0,IF(a.ac_city_area=0,a.ac_city=0,a.ac_city_area='$ac_city_area'))";
                $sql .= " and IF(a.ac_city='$ac_city',a.ac_city_area='$ac_city_area' OR a.ac_city_area=0,a.ac_city=0)";
            }
        }
        $sql .= " and (a.ac_area_id = '$areaid' or a.ac_area_id=0) ";
        if ($currentId != $areaid){
            $sql .= " and a.ac_dish_total > 0 ";
        }
        $info = mysqld_select($sql);
        //没有商品
        if ($info && $info['num'] > 0 ) {
            return true;
        }
    }
}