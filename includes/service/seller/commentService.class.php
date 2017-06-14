<?php
/**
评价service层
 */
namespace service\seller;
use service\publicService;
class commentService extends publicService
{
    const SUCCESS = 1;//成功
    const FAIL = 2;//失败
    const PROCESSING = 3;//进行中
    const UNTREATED = 4;//未处理
    //订单表的状态名称
    public static $status_name_sign = array(
    
        self::SUCCESS => '成功',
        self::FAIL => '失败',
        self::PROCESSING => '进行中',
        self::UNTREATED => '未处理',
    );
    public $sts_id;
    public function __construct(){
        parent::__construct();
        $this->member = get_member_account(1);
        $this->sts_id = intval($this->member['store_sts_id']);
    }
    /**
     * 返回店铺评价列表
     * @param $type类型 1表示好评，2表示差评
     * */
    public function getStoreCommentList($_GP,$type=2){
        
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['psize'])?$_GP['psize']:10;//默认每页10条数据
        $limit= ($pindex-1)*$psize;
        $sql = "SELECT id,sts_id,openid,orderid,ordersn,dishid,wl_rate,fw_rate,cp_rate,comment,reply,createtime,replytime,type from " .table('shop_goods_comment'). " where sts_id=:sts_id and type={$type}";
        $listNum = mysqld_selectall($sql,array('sts_id'=>$this->sts_id));
        $total = count($listNum);
        $sql .= " ORDER BY createtime DESC LIMIT ".$limit.",".$psize;
        $lists = mysqld_selectall($sql,array('sts_id'=>$this->sts_id));
        if (!empty($lists)) {
            foreach ($lists as $key=>$val){
                $shopInfo = mysqld_select("SELECT id,title from ".table('shop_dish')." where id=:dishid",array('dishid'=>$val['dishid']));
                $lists[$key]['title'] = $shopInfo['title'];
                $commentName = mysqld_select("SELECT nickname,mobile from " .table('member'). "where openid=:openid",array('openid'=>$val['openid']));
                $lists[$key]['nickname'] = $commentName['nickname'];
                $lists[$key]['mobile'] = $commentName['mobile'];
            }
            $pager = pagination($total, $pindex, $psize);
            $result = array(
                'lists' => $lists,
                'total' => $total,
                'pager' => $pager,
            );
            return $result;
        }
    }
    /**根据评论id获取评论标记列表
     * @param $commentid 评论id
     *   */
    public function getCommentSign($commentid){
        if ($commentid){
            $list = mysqld_selectall("SELECT a.*,b.nickname FROM ".table('shop_goods_comment_sign')." as a LEFT JOIN ".table('member')." as b on a.openid=b.openid WHERE a.commentid=:commentid order by createtime desc",array('commentid'=>$commentid));
            foreach ($list as $key=>$v){
                $list[$key]['status_name'] = commentService::$status_name_sign[$v['status']];
            }
            return $list;
        }
    }
    /**
     * 增加差评处理标记
     * @return boolean  */
    public function addSign($_GP){
        if (!empty($_GP) && is_array($_GP)){
            $data = array(
                'commentid' => $_GP['commentid'],
                'openid' => $this->member['openid'],
                'content' => $_GP['sign_recorde'],
                'status' => $_GP['sign_radio'],
                'createtime' => time(),
            );
            mysqld_insert('shop_goods_comment_sign',$data);
            if (mysqld_insertid()){
                return true;
            }
        }
    }
    /**
     * 统计好评和差评的数量
     *   */
    public function statistics(){
        $good_num = mysqld_select("SELECT count(*) as num from ".table('shop_goods_comment')." where sts_id=:sts_id and type=1",array('sts_id'=>$this->sts_id));
        $bad_num = mysqld_select("SELECT count(*) as num from ".table('shop_goods_comment')." where sts_id=:sts_id and type=2",array('sts_id'=>$this->sts_id));
        if (empty($good_num['num'])) $good_num['num']=0;
        if (empty($bad_num['num'])) $bad_num['num']=0;
        return $return=array('good_num'=>$good_num['num'],'bad_num'=>$bad_num['num']);
    }
    /**
     *商家回复评论 
     * */
    public function reply($data){
        $return = mysqld_update('shop_goods_comment',array('reply'=>$data['reply'],'replytime'=>time()),array('id'=>$data['commentid'],'sts_id'=>$this->sts_id));
        return $return;
    }
}