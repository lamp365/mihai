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
   
   
}