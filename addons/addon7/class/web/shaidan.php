<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/2/4
 * Time: 16:08
 */
$op = empty($_GP['op']) ? 'list':$_GP['op'];
if($op == 'list'){
    $psize =  20;
    $pindex = max(1, intval($_GP["page"]));
    $limit = ' limit '.($pindex-1)*$psize.','.$psize;
    $article_list = mysqld_selectall("SELECT * FROM " . table('share_active_shaidan')." order by is_top desc,id desc {$limit}" );
    $total = $pager = '';
    if(!empty($article_list)){
        $total        = mysqld_selectcolumn("SELECT count(id) FROM " . table('share_active_shaidan'));
        $pager        = pagination($total, $pindex, $psize);
    }
    include addons_page('shaidan_list');
}else if($op =='post'){
    if(checksubmit()){
        if(empty($_GP['award_id'])){
            message("对不起，没有选择中奖商品！",refresh(),'error');
        }
        $data = array(
            'title'     => $_GP['title'],
            'award_id'  => $_GP['award_id'],
            'content'    => htmlspecialchars_decode($_GP['content'],ENT_NOQUOTES),
            'openid'    => $_GP['openid'],
            'modifiedtime'=>time()
        );
        if(!empty($_GP['zan_num'])){
            $data['zan_num'] = intval($_GP['zan_num']);
        }
        if($_FILES['thumb']['error'] != 4){
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['thumb'] = $upload['path'];
        }
        if(empty($_GP['id'])){
            $data['createtime'] = time();
            mysqld_insert('share_active_shaidan',$data);
        }else{
           mysqld_update('share_active_shaidan',$data,array('id'=>$_GP['id']));
        }
        $url = web_url("shaidan");
        message("操作成功！",$url,'success');
    }

    //获取中奖商品并且已经兑换了
    $draw_goods = mysqld_selectall("select id,title,logo from ".table('addon7_award')." where state=4 order by id desc");
    if(empty($_GP['id'])){
        $article = array();
        include addons_page('shaidan_post');
    }else{
        $article = mysqld_select("select * from ".table('share_active_shaidan')." where id={$_GP['id']}");
        include addons_page('shaidan_post');
    }
}else if($op == 'get_zuozhe'){  //发布文章的时候，根据所选的中奖商品获取中奖作者
    $award_id = $_GP['award_id'];
    $draw_member = mysqld_selectall("select openid from ".table('addon7_request')." where award_id={$award_id} and status =1");
    if(!empty($draw_member)){
        //去除已经发布过的中奖用户
        $article = mysqld_selectall("select id,openid from ".table('share_active_shaidan')." where award_id={$award_id}");
        if(!empty($article_list)){
            foreach($draw_member as $key => $items){
                $nickname = true;
                foreach($article as $one){
                    if($items['openid'] == $one['openid']){
                        unset($draw_member[$key]);
                        $nickname = false;
                    }
                }
                //找出对应昵称
                if($nickname){
                    $member = member_get($items['openid']);
                    $draw_member[$key]['nickname'] = empty($member['realname']) ? $member['mobile'] : $member['realname'];
                }
            }
        }else{
            foreach($draw_member as $key => $items){
                $member = member_get($items['openid']);
                $draw_member[$key]['nickname'] = empty($member['realname']) ? $member['mobile'] : $member['realname'];

            }
        }
        die(showAjaxMess('200',$draw_member));
    }else{
        die(showAjaxMess('1002','无中奖者！'));
    }

}else if($op == 'settop'){  //给晒单的文章置顶
    $is_top   = $_GP['is_top'];
    $id       = $_GP['id'];
    if($is_top == 1){
        //需要取消置顶
        mysqld_update("share_active_shaidan",array('is_top'=>0),array('id'=>$id));
    }else{
        //设置为置顶
        mysqld_update("share_active_shaidan",array('is_top'=>1),array('id'=>$id));
    }
    die(showAjaxMess(200,'操作成功！'));
}