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
     * 获得单条shop_goods_comment_total表信息
     *   */
    public function getOneCommentTotal($where = array(),$param="*"){
        if (empty($where)) return false;
        $commentTotal = new shop_goods_comment_total_model();
        return $commentTotal->getOne($where,$param);
    }
    /**
     * 获得多条shop_goods_comment表信息
     *   */
    public function getAllComment($where = array(),$param="*" ,$orderby = false){
        if (empty($where)) return false;
        $comment = new shop_goods_comment_model();
        return $comment->getAll($where,$param,$orderby);
    }
    /**
     * 根据店铺id获得该店铺的综合得分
     *   */
    public function getstoreAvePingfen($storeid){
        if(empty($storeid)) return ;
        $commentTotal = new shop_goods_comment_total_model();
        $total = $this->getOneCommentTotal(array('sts_id'=>$storeid),"sts_id,sum(wl_rate) as all_wl_rate,sum(fw_rate) as all_fw_rate,sum(cp_rate) as all_cp_rate,sum(comment_num) as num");
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
        $where = array('sts_id'=>$storeid,'dishid'=>$dishid);
        $all = $this->getAllComment($where,'id');
        $num = count($all);
        $list = $this->getAllComment($where,'*',"createtime DESC LIMIT {$limit} , {$psize}");
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