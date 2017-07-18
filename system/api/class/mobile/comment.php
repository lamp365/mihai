<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/4/19
 * Time: 14:35
 */
namespace api\controller;
use service\seller\commentService;
class comment extends base
{
    public function __construct(){
        parent::__construct();
        $this->commentService = new commentService();
    }
    //评论列表
    public function commentList(){
        $_GP = $this->request;
        $commentList = $this->commentService->getStoreCommentListAPP($_GP);
        if ($commentList['lists']){
            $returnData = $data = array();
            foreach ($commentList['lists'] as $v){
                $temp['id'] = $v['id'];
                $temp['cp_rate'] = $v['cp_rate'];
                $temp['comment'] = $v['comment'];
                $temp['createtime'] = $v['createtime'];
                $temp['nickname'] = $v['nickname'];
                $temp['status'] = $v['status'];
                $temp['thumb'] = $v['thumb'];
                $temp['reply'] = !empty($v['reply']) ? $v['reply'] : '';
                $info = $this->commentService->getCommentNewSign($v['id']);
                $temp['remark'] = !empty($info) ? $info : '';
                $img = $this->commentService->getCommentImg($v['id']);
                $temp['img'] = $img;
                $returnData[] = $temp;
            }
            $data = array('total'=>$commentList['total'],'lists'=>$returnData);
            ajaxReturnData(1,'',$data);
        }else{
            ajaxReturnData(1,'暂无评价','');
        }
    }
    //评论接入、状态
    public function editStatus(){
        $member = get_member_account(1);
        $sts_id = intval($member['store_sts_id']);
        $_GP = $this->request;
        $status = intval($_GP['status']);
        $commentid = intval($_GP['commentid']);
        if (!in_array($status, array(1,2,3,4)) || (!$commentid)) ajaxReturnData(0,'参数错误');
        $flag = mysqld_update('shop_goods_comment',array('status'=>$status),array('id'=>$commentid,'sts_id'=>$sts_id));
        if ($flag) ajaxReturnData(1,'修改成功');
        else ajaxReturnData(0,'修改失败');
    }
    //评论备注
    public function editRemark(){
        $openid = checkIsLogin();
        $_GP = $this->request;
        $status = intval($_GP['status']);
        $commentid = intval($_GP['commentid']);
        $remark = $_GP['remark'];
        if (!in_array($status, array(1,2,3,4)) || (!$commentid) || (!$remark)) ajaxReturnData(0,'参数错误');
        $insertData = array(
            'commentid'     =>  $commentid,
            'openid'        =>  $openid,
            'content'       =>  $remark,
            'createtime'    =>  time(),
            'status'        =>  $status,
        );
        mysqld_insert('shop_goods_comment_sign',$insertData);
        if (mysqld_insertid()){
            ajaxReturnData(1,'备注成功');
        }else{
            ajaxReturnData(0,'备注失败');
        }
    }
    //评论回复
    public function addReply(){
        $_GP = $this->request;
        $reply = $_GP['reply'];
        $commentid = intval($_GP['commentid']);
        if ((!$reply) || (!$commentid)) ajaxReturnData(0,'参数错误');
        $sql = "select * from ".table('shop_goods_comment')." where id=:id";
        $info = mysqld_select($sql,array('id'=>$commentid));
        if ($info && $info['reply']){
            ajaxReturnData(0,'回复失败,已经回复过');
        }
        $system = get_mobile_type(1);
        $data = array(
            'reply'=>$reply,
            'replytime'=>time(),
            'system'=>$system,
        );
        $flag = $this->commentService->reply($data,$commentid);
        if ($flag) ajaxReturnData(1,'回复成功');
        else ajaxReturnData(0,'回复失败');
    }
    //评论详情
    public function commentDetail(){
        $_GP = $this->request;
        $commentid = intval($_GP['commentid']);
        if ((!$commentid)) ajaxReturnData(0,'参数错误');
        $commentInfo = $this->commentService->getCommentDetail($commentid);
        if (empty($commentInfo)) ajaxReturnData(0,'出错');
        $img = $this->commentService->getCommentImg($commentid);
        $remark = $this->commentService->getCommentSign($commentid);
        $remarkall = array();
        if ($remark){
            foreach ($remark as $v){
                $temp['content'] = $v['content'];
                $temp['nickname'] = $v['nickname'];
                $temp['status'] = $v['status'];
                $temp['createtime'] = $v['createtime'];
                $remarkall[] = $temp;
            }
        }
        $data = array(
            'title'     =>  $commentInfo['title'],
            'cp_rate'   =>  $commentInfo['cp_rate'],
            'thumb'     =>  $commentInfo['thumb'],
            'orderid'   =>  $commentInfo['orderid'],
            'comment'   =>  $commentInfo['comment'],
            'id'        =>  $commentInfo['id'],
            'status'    =>  $commentInfo['status'],
            'nickname'  =>  $commentInfo['nickname'],
            'avatar'    =>  $commentInfo['avatar'],
            'reply'     =>  $commentInfo['reply'],
            'createtime'=>  $commentInfo['createtime'],
            'replytime' =>  $commentInfo['replytime'],
            'img'       =>  $img,
            'remarkall'    =>  $remarkall,
        );
        ajaxReturnData(1,'',$data);
    }
    
}