<?php
namespace seller\controller;
use service\seller\commentService;
class comment extends base
{
    public function __construct(){
        parent::__construct();
        $this->commentService = new commentService();
    }
    //差评评论列表
	public function index()
	{
	    $_GP = $this->request;
	    //获取差评列表
	    $bad_comment = $this->commentService->getStoreCommentList($_GP,2);
	    if ($bad_comment){
	        foreach ($bad_comment['lists'] as $key=>$v){
	            //获取差评的标记
	            $list = $this->commentService->getCommentSign($v['id']);
	            if (!empty($list)){
	                $bad_comment['lists'][$key]['data'] = $list;
	            }
	        }
	    }
        include page('comment/bad_comment');
	}
	//好评列表
	public function goodComment()
	{
	    $_GP = $this->request;
	    //获取好评列表
	    $good_comment = $this->commentService->getStoreCommentList($_GP,1);
	    include page('comment/good_comment');
	}
    //增加标记
	public function addSign(){
	    $_GP = $this->request;
	    if(empty($_GP['commentid']) || empty($_GP['sign_radio']) || empty($_GP['sign_recorde'])) ajaxReturnData('1','出错，请重新增加标记');
	    $flag = $this->commentService->addSign($_GP);
	    if ($flag) {
	        ajaxReturnData('2','标记成功');
	    }else {
	        ajaxReturnData('1','出错，请重新增加标记');
	    }
	}
	//综合评分
	public function synthetic(){
	    //统计好评和差评
	    $num = $this->commentService->statistics();
	    include page('comment/synthetic');
	}
    //商家回复评论
    public function addReply(){
        $_GP = $this->request;
        $commentid = intval($_GP['commentid']);
        if (empty($commentid) || empty($_GP['reply'])) message('参数有误！',refresh(),'error');
        $system = get_mobile_type();
        $data = array(
            'reply'=>$_GP['reply'],
            'replytime'=>time(),
            'system'=>$system,
        );
        $return = $this->commentService->reply($data,$commentid);
        if ($return) message('回复成功！',refresh(),'success');
    }
   
    
}