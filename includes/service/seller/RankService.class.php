<?php

/**
 * Author: 王敬
 */

namespace service\seller;

class RankService extends \service\publicService {

    
    public function getFieldByRankLevel($rank_level,$field= 'rank_name') {
//        if (extension_loaded('Memcache')) {//缓存设置
//             $mcache = new Mcache();
//             $arr = $mcache->get('cache_func_recursiveRegionAssort');
//             if(!$arr){
//                 $arr = mysqld_selectall("SELECT * FROM " . table('store_shop_level'),array(),'rank_level');
//                 $mcache->set('func_getFieldByRankLevel', $arr);
//             }
//             return $arr[$rank_level][$field];
//         }else{
             $arr = mysqld_select("SELECT {$field} FROM " . table('store_shop_level')." where rank_level= ".$rank_level );
//             ppd($arr);
             return $arr[$field];
//         }
    }
    
    public function getInfoByRankLevel($rank_level) {
//        if (extension_loaded('Memcache')) {//缓存设置
//             $mcache = new Mcache();
//             $arr = $mcache->get('cache_func_recursiveRegionAssort');
//             if(!$arr){
//                 $arr = mysqld_selectall("SELECT * FROM " . table('store_shop_level'),array(),'rank_level');
//                 $mcache->set('func_getFieldByRankLevel', $arr);
//             }
//             return $arr[$rank_level][$field];
//         }else{
             $arr = mysqld_select("SELECT * FROM " . table('store_shop_level')." where rank_level= ".$rank_level );
//             ppd($arr);
             return $arr;
//         }
    }
    
    //收费等级到期后，降级逻辑       
    public function downToFreeLevel($sts_id,$rank_level) {
        $free_info = mysqld_select("SELECT * FROM " . table('store_shop_level')." where rank_level < ".$rank_level." and is_free = 1 order by rank_level desc " );
//        ppd($free_info);
        if($free_info){
            //1.针对店铺
            $data['sts_shop_level'] = $free_info['rank_level'];
            mysqld_update("store_shop",$data,array('sts_id'=>$sts_id ));
    
            //2.针对商品，进行下架操作
            $total = mysqld_selectcolumn("select count(*) from ". table('shop_dish')."where deleted = 0 AND sts_id ={$sts_id} ");            
            $effect_limit = $total>$free_info['dish_num']? $total-$free_info['dish_num'] :$total;//250-100取150 50-100取50
        
            $effect_num= mysqld_query( "UPDATE ".  table('shop_dish')." SET status = 0  "
                . "WHERE deleted = 0 AND status=1 AND sts_id = {$sts_id}
                ORDER BY id ASC LIMIT {$effect_limit} ");
        }else{
            
        }
        return true;
    }
    
     /**
     * 购买店铺等级
     * @param $sts_id,$level
     * @return bool
     */
    public function updateLevel($sts_id,$level) {
        #1数据或权限校验
        $sql = "select * from". table('store_shop') ."where sts_id = {$sts_id} ";
        $shop_info  = mysqld_select($sql);
        
        $sql = "select * from". table('store_shop_level') ."where rank_level = {$level} ";
        $level_info  = mysqld_select($sql);
        if($shop_info['recharge_money']<$level_info['money']){
            $this->error = '可用余额不足';
            return false;
        }

        $userInfo=  get_member_account();
        if($sts_id != $userInfo['store_sts_id']){
            $this->error = '参数id非法！';
             return false;
        }
        if(empty($userInfo['store_is_admin'])){
            $this->error = '对不起您不是最高管理员！';
            return false;
        }

        //扣钱 打下 paylog
        store_gold($sts_id,$level_info['money'],-1,LANG('LOG_BUY_SHOP_LEVEL_REMARK','paylog'));

        $data =array(
            'sts_shop_level'       => $level,
            'sts_level_valid_time' => strtotime("+".$level_info['time_range']." year"),
         );
        $where  = array('sts_id' => $sts_id );
        $effect = mysqld_update('store_shop', $data, $where);
        return $effect;
        
    }
}
