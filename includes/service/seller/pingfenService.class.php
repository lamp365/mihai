<?php
/**
评价service层
 */
namespace service\seller;
use service\publicService;
class pingfenService extends publicService
{
    public $sts_id;
    public function __construct(){
        parent::__construct();
        $member = get_member_account(1);
        $this->sts_id = $member['store_sts_id'];
    }
    
    /**
     * 返回CBD评分排名
     * */
    public function rankCBD( $region_code='' ){
        $member = get_member_account(1);
        $settings  = globaSetting();
        $region_code && $where = ' where sts_region =  '.$region_code ;
        $field ="sts_id,sts_region,sts_city,friend_count,order_money,product_count,recharge_money ,"
            . " friend_count*{$settings['enter_exchange']} as f_rate,"//直接输出单项*系数
            . " order_money*{$settings['order_num_exchange']} as o_rate,"//直接输出单项*系数
            ."  product_count*{$settings['comment_exchange']} as p_rate,"//直接输出单项*系数
            . " recharge_money*{$settings['bid_exchange']} as r_rate,"//直接输出单项*系数
            . "friend_count*{$settings['enter_exchange']}"
            . "+order_money*{$settings['order_num_exchange']}"
            . " +product_count*{$settings['comment_exchange']}"
            . "+recharge_money*{$settings['bid_exchange']}" 
            . " as cbd_score";//输出CBD综合分值
        //******************切区域查看排名↓*********************//    
        if( $member['sts_region']==$region_code ){//如果相等则查询结果包含了自己
        }else{//如果不相等则代表要查询出自己在不同区域的排名
            $addition_sql = "  UNION ALL	( SELECT $field FROM  ".table('store_shop')."  WHERE sts_id = ".$member['store_sts_id'] ." ) ";
        }
        //******************切区域查看排名↑*********************// 
        
        //******************排名*********************//rownum排名
        $order_sql = "SELECT * ,@rownum := @rownum + 1 AS rownum
                    FROM (
                        SELECT
                            ".$field."
                        FROM
                            ".table('store_shop').$where.$addition_sql . "
                        ORDER BY cbd_score DESC,friend_count DESC
                    ) AS obj ,(SELECT @rownum := 0) r" ;
//          ppd($order_sql );
        $tmp_result = mysqld_selectall($order_sql);
        if(!$tmp_result){return null;}
        foreach ($tmp_result as $value) {
            $result[$value['sts_id']] = $value;
        } 
//        ppd($result);
        return $result;
    }
    /**
     * 返回店铺所有商品的总评价
     * 
     * */
    public function getStoreAllGoodsGrade(){
        
        //获取店铺所有商品的所有评分
        $total = mysqld_select("SELECT sts_id,sum(wl_rate) as all_wl_rate,sum(fw_rate) as all_fw_rate,sum(cp_rate) as all_cp_rate from ".table('shop_goods_comment_total')." where sts_id=:sts_id",array('sts_id'=>$this->sts_id ));
        if (!empty($total)){
            $total['all_wl_rate'] = empty($total['all_wl_rate'])?0:$total['all_wl_rate'];
            $total['all_fw_rate'] = empty($total['all_fw_rate'])?0:$total['all_fw_rate'];
            $total['all_cp_rate'] = empty($total['all_cp_rate'])?0:$total['all_cp_rate'];
        }
        return $total;
    }
    
    /**
     * 获得单个商品的评分
     * @param int $dishid 商品id
     *   */
    public function getSingleGoodsGrade($dishid){
        if (empty($dishid)) return false;
        $total = mysqld_select("SELECT sts_id,wl_rate as all_wl_rate,fw_rate as all_fw_rate,cp_rate as all_cp_rate from ".table('shop_goods_comment_total')." where sts_id=:sts_id and dishid=:dishid",array('sts_id'=>$this->sts_id ,'dishid'=>$dishid));
        if (!empty($total)){
            $total['all_wl_rate'] = empty($total['all_wl_rate'])?0:$total['all_wl_rate'];
            $total['all_fw_rate'] = empty($total['all_fw_rate'])?0:$total['all_fw_rate'];
            $total['all_cp_rate'] = empty($total['all_cp_rate'])?0:$total['all_cp_rate'];
        }
        return $total;
    }
    /**
     * 获得多个商品的评分
     * @param array $dishid 商品id
     *   */
    public function getMoreGoodsGrade($dishid = array()){
        if (empty($dishid) || (!is_array($dishid))) return false;
        $total = mysqld_selectall("SELECT sts_id,dishid,wl_rate,fw_rate,cp_rate from ".table('shop_goods_comment_total')." where sts_id=:sts_id",array('sts_id'=>$this->sts_id ));
        if (!empty($total)){
            $return = array('all_wl_rate'=>0,'all_fw_rate'=>0,'all_cp_rate'=>0);
            foreach ($total as $v){
                if (in_array($v['dishid'], $dishid)){
                    $return['all_wl_rate'] += $v['wl_rate'];
                    $return['all_fw_rate'] += $v['fw_rate'];
                    $return['all_cp_rate'] += $v['cp_rate'];
                }
            }
        }
        return $return;
    }
    
}