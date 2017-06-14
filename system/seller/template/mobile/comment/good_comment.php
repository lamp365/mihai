<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <?php include page('seller_header');?>
</head>
<style type="text/css">
body{
    padding: 30px;
    padding-top: 20px;
    box-sizing: border-box;
}
#main{
    width: 80%;
    height: 600px;
}
.star-area .red{
    color: red;
}
.star-area .gray{
    color: darkgrey;
}
.star-area{
    min-width: 170px;
}
.li-div{
    min-width: 300px;
}
.comment-li{
    background-color: #fbfbfb;
    padding: 10px;
    border-bottom: 1px dashed #e1e1e1;
    box-sizing: border-box;
}
.comment-list{
    padding: 10px;
    box-sizing: border-box;
}
.reply-div{
    padding:0 10px 10px 10px;
    box-sizing: border-box;
}
.comment-content{
    float: left;
    width: 75%;
    line-height: 30px;
}
.comment-list .reply{
    float: right;
}
.layui-btn-small.reply i{
    font-size:12px!important;
}
.comment-contents{
    color: #9f9f9f;
}
.li-left{
    float: left;
    width: 50%;
}
.li-right{
    float: left;
    width: 50%;
    position: relative;
}
.product-name{
    color: #000;
    font-size: 16px;
}
.comment-user{
    margin-top: 17px;
    font-size: 12px;
}
.lists{
    border: 1px solid #eaeaea;
    border-radius: 3px;
    margin-bottom: 15px;
}
</style>
<body >
    <div class="layui-tab layui-tab-card">
        <ul class="layui-tab-title">
        <!-- layui-this代表当前选中的tab项 -->
            <li><a href="<?php echo mobile_url('comment',array('name'=>'seller','op'=>'synthetic'));?>">综合评分</a></li>
            <li><a href="<?php echo mobile_url('comment',array('name'=>'seller','op'=>'index'));?>">差评</a></li>
            <li class="layui-this">好评</li>
        </ul>
        <div class="layui-tab-content">
                <ul>
                <?php if (!empty($good_comment['lists']) && is_array($good_comment['lists'])){foreach ($good_comment['lists'] as $val){?>
                    <li class="lists">
                        <div class="comment-li clearfix">
                            <div class="li-div li-left">
                                <div class="product-name"><?php echo $val['title']?></div>
                                <div class="comment-user">
                                                                    评价人：<?php echo $val['nickname'];?>
                                    [<?php echo $val['mobile'];?>]
                                    [<?php echo date("Y-m-d H:i:s",$val['createtime'])?>]
                                </div>
                            </div>
                            <div class="li-div star-area li-right">
                                <div>
                                <span>配送评分：</span>
                                    <!-- 一个i代表一个星 -->
                                    <?php for ($i=1;$i<=$val['wl_rate'];$i++){?>
                                        <i class="fa fa-star red" aria-hidden="true"></i>
                                    <?php }?>
                                    <?php for ($i=1;$i<=5-$val['wl_rate'];$i++){?>
                                        <i class="fa fa-star gray" aria-hidden="true"></i>
                                    <?php }?>
                                </div>
                                <div>
                                <span>质量评分：</span>
                                    <!-- 一个i代表一个星 -->
                                    <?php for ($i=1;$i<=$val['cp_rate'];$i++){?>
                                        <i class="fa fa-star red" aria-hidden="true"></i>
                                    <?php }?>
                                    <?php for ($i=1;$i<=5-$val['cp_rate'];$i++){?>
                                        <i class="fa fa-star gray" aria-hidden="true"></i>
                                    <?php }?>
                                </div>
                                <div>
                                <span>服务评分：</span>
                                    <!-- 一个i代表一个星 -->
                                    <?php for ($i=1;$i<=$val['fw_rate'];$i++){?>
                                        <i class="fa fa-star red" aria-hidden="true"></i>
                                    <?php }?>
                                    <?php for ($i=1;$i<=5-$val['fw_rate'];$i++){?>
                                        <i class="fa fa-star gray" aria-hidden="true"></i>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                        <div class="comment-list clearfix">
                            <span class="comment-content">买家的评价：<span class="comment-contents"><?php echo $val['comment'];?></span></span>
                            <?php if (empty($val['reply'])){?><div class="layui-btn layui-btn-normal layui-btn-small reply" commentid="<?php echo $val['id']?>"><i class="fa fa-reply" aria-hidden="true"></i>&nbsp;&nbsp;回复</div><?php }?>
                        </div>
                        <?php if (!empty($val['reply'])){?><div class='reply-div'>卖家的回复：<span class='comment-contents'><?php echo $val['reply'];?></span></div><?php }?>
                    </li>
                    <?php }
                    echo $good_comment['pager'];
                    }?>
                </ul>
        </div>
    </div>
    <!-- 回复的modal弹出框 -->
    <form action="<?php echo mobile_url('comment',array('name'=>'seller','op'=>'addReply')); ?>" method="post" id="add_reply_form" enctype="multipart/form-data" id="update_use_form" class="form-horizontal" >
    <div class="modal fade reply-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">回复</h4>
          </div>
          <div class="modal-body">
            <input type="hidden" name="commentid" value="" id="getcmid">
            <input class="form-control reply-content" name="reply" id="reply" placeholder="请输入回复内容">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <button type="button" id="tjreply" class="btn btn-primary saveReply">确定</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    </form>
</body>

<script>
    layui.use(['form','element'],function(){
        var form = layui.form();
        var element = layui.element();
    })
    //回复函数
    function reply(){
        var that = "";
        $(".reply").on("click",function(){
            that = $(this);
			var commentid = $(this).attr('commentid');
			if(commentid != ''){
				$("#getcmid").val(commentid);
				$(".reply-modal").modal();
			}
        });   
    }
    //回复调用
    reply();

    $("#tjreply").click(function(){
    	var reply = $("#reply").val();
    	if(reply.length == 0){
    		layer.open({
                title: '提示',
                content: '请填写评论'
            });
        }else{
        	$("#add_reply_form").submit();
        }
    });
</script>
</html>

