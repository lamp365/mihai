<?php
/**
评论service
 */
namespace service\shopwap;
use service\publicService;
use model\shop_goods_comment_model;
use model\shop_goods_comment_total_model;
class commentService extends publicService
{
    
    /**
     * 根据店铺id获得该店铺的综合得分
     *   */
    public function getstoreAvePingfen($storeid){
        if(empty($storeid)) return ;
        $commentTotal = new shop_goods_comment_total_model();
        $sql = "SELECT sts_id,sum(wl_rate) as all_wl_rate,sum(fw_rate) as all_fw_rate,sum(cp_rate) as all_cp_rate,sum(comment_num) as num from ".table($commentTotal->table_name)." where sts_id=:sts_id";
        $total = $commentTotal->fetch($sql,array('sts_id'=>$storeid));
        if (!empty($total)){
            $total['all_wl_rate'] = empty($total['all_wl_rate'])?0:$total['all_wl_rate'];
            $total['all_fw_rate'] = empty($total['all_fw_rate'])?0:$total['all_fw_rate'];
            $total['all_cp_rate'] = empty($total['all_cp_rate'])?0:$total['all_cp_rate'];
        }
        if (!empty($total['num'])){
            $ave = ($total['all_wl_rate']+$total['all_fw_rate']+$total['all_cp_rate'])/(3*$total['num']);
            $ave = sprintf("%.2f", $ave);
        }
        return $ave;
    }
    /**
     * 取得商品的评论列表
     * 
     *  */
    public function  getGoodsComment($data) {
        $storeid = $data['storeid'];
        $dishid = $data['dishid'];
        if (empty($storeid) || empty($dishid)) return '';
        $pindex = max(1, intval($data['page']));
        $psize = isset($data['limit']) ? $data['limit'] : 4;//默认每页4条数据
        $limit= ($pindex-1)*$psize;
        $shopGoodsComment = new shop_goods_comment_model();
        $where = array('sts_id'=>$storeid,'dishid'=>$dishid);
        $all = $shopGoodsComment->getAll($where,'id');
        $num = count($all);
        $list = $shopGoodsComment->getAll($where,'*',"createtime DESC LIMIT {$limit} , {$psize}");
        if ($list) {
            foreach ($list as $key=>$v){
                $commentInfo = member_get($v['openid'],'nickname,avatar');
                $list[$key]['nickname'] = $commentInfo['nickname'];
                $list[$key]['avatar'] = $commentInfo['avatar'];
            }
        }
        $return = array('num'=>$num,'list'=>$list);
        return $return;
    }
}