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
     * 取一个活动当天的所有时间段
     * @param $ac_list_id 区域码
     */
    public function getActArea($ac_list_id){
        $actAreaModel = new \model\activity_area_model();
        //取时间段
        $activty_area = $actAreaModel->getAllActArea(array('ac_list_id'=>$ac_list_id));
        if (empty($activty_area)) return '';
        foreach ($activty_area as $key=>$val){
            $startDate = date("Y:m:d")." ".date('H:i:s',$val['ac_area_time_str']);
            $endDate = date("Y:m:d")." ".date('H:i:s',$val['ac_area_time_end']);
            $temp['starttime'] = strtotime($startDate);
            $temp['endtime'] = strtotime($endDate);
            $temp['ac_area_id'] = $val['ac_area_id'];
            $data[] = $temp;
        }
        return $data;
    }
    /**
     * 取当前活动的当天的未过期的时间段
     * @param $ac_list_id 区域码
     * @num 取的个数
     *   */
    public function getActAreaNoExp($ac_list_id,$num = 0){
        if (empty($ac_list_id)) return '';
        $list = $this->getActArea($ac_list_id);
        if($list){
            foreach ($list as $v){
                if ($v['endtime'] <= time()) continue;//过期的筛选出
                $temp['ac_area_id'] = $v['ac_area_id'];
                $temp['ac_area_time_str'] = date("H:i",$v['starttime']);
                $temp['ac_area_time_end'] = date("H:i",$v['endtime']);
                $temp['status'] = 0;
                if (time() >= $v['starttime'] && time() <= $v['endtime']){
                    $temp['status'] = 1;
                    $temp['section'] = $v['endtime']-time();
                }else{
                    $temp['section'] = $v['starttime']-time();
                }
                $data[] = $temp;
            }
            if ($num == 0) return $data;
            
            if (count($data) > $num){
                for ($i=0;$i<$num;$i++){
                    $return[$i] = $data[$i];
                }
            }else {
                $return = $data;
            }
            return $return;
        }
    }
}