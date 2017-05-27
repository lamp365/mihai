<?php
namespace shop\controller;

class dish extends \common\controller\basecontroller
{
    public function lists()
    {
        $_GP = $this->request;
        $all_category  = getCategoryAllparent();
        $first_son     = array();
        if(!empty($_GP['p1'])){
            $first_son   = getCategoryByParentid($_GP['p1']);
        }

        $pindex = max(1, intval($_GP['page']));
        $psize = 20;
        $condition = ' deleted=0 ';
        $sorturl = create_url('site', array('name' => 'shop','do' => 'dish','op'=>'lists'));
        if (!empty($_GP['keyword'])) {
            $key_type = $_GP['key_type'];
            $_GP['keyword'] = trim($_GP['keyword']);
            if ( $key_type == 'title' ){
                $condition .= " AND title LIKE '%{$_GP['keyword']}%'";
            }else{
                $condition .= " AND id = {$_GP['keyword']}";
            }
            $sorturl .= '&keyword='.$_GP['keyword'];
        }


        if ( isset($_GP['status'])) {
            $status = $_GP['status'];
            $condition .= " AND status = '{$status}'";
            $sorturl .= '&status='.$_GP['status'];
        }else{
            $_GP['status'] = 1;
        }
        if (!empty($_GP['p2'])) {
            $cid = intval($_GP['p2']);
            $condition .= " AND p2 = '{$cid}'";
            $sorturl .= '&p2='.$_GP['p2'];
        } elseif (!empty($_GP['p1'])) {
            $cid = intval($_GP['p1']);
            $condition .= " AND p1 = '{$cid}'";
            $sorturl .= '&p1='.$_GP['p1'];
        }
        if ( $_GP['type'] != -1 && isset($_GP['type']) ){
            $type = intval($_GP['type']);
            $condition .= " AND type = '{$type}'";
            $sorturl .= '&type='.$_GP['type'];
        }

        $orderby = '';
        $oprice = $otprice = $otot = 'asc';
        if ( isset($_GP['orderprice']) ){
            if ( $_GP['orderprice'] == 'asc' ){
                $oprice = 'desc';
            }else{
                $oprice = 'asc';
            }
            $orderby = "marketprice ".$_GP['orderprice'].' , ';
        }
        if ( isset($_GP['ordertprice']) ){
            if ( $_GP['ordertprice'] == 'asc' ){
                $otprice = 'desc';
            }else{
                $otprice = 'asc';
            }
            $orderby = "timeprice ".$_GP['ordertprice'].' , ';
        }
        if ( isset($_GP['ordertot']) ){
            if ( $_GP['ordertot'] == 'asc' ){
                $otot = 'desc';
            }else{
                $otot = 'asc';
            }
            $orderby = "total ".$_GP['ordertot'].' , ';
        }

        $sql = "select * from ".table('shop_dish')." where {$condition} order by {$orderby} id DESC ";
        //导出 操作
        if (!empty($_GP['report'])) {
            $list   = mysqld_selectall($sql);
            $report ='dishreport';
            require_once 'report.php';
            die();
        }

        $sql .= " limit ".($pindex - 1) * $psize . ',' . $psize;
        //查询
        $list   = mysqld_selectall($sql);

       foreach($list as $key=>$val){
            switch ( $val['type'] ){
                case 1:
                    $list[$key]['typename'] = '团购商品';
                    break;
                case 2:
                    $list[$key]['typename'] = '秒杀商品';
                    break;
                case 3:
                    $list[$key]['typename'] = '今日特价商品';
                    break;
                case 4:
                    $list[$key]['typename'] = '限时促销';
                    break;
                default:
                    $list[$key]['typename'] = '一般商品';
                    break;
            }
        }
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_dish') . " as a WHERE {$condition}");
        $pager = pagination($total, $pindex, $psize);
        include page('dish/lists');
    }

    public function ajax_title()
    {
        $_GP = $this->request;
        if ( !empty($_GP['ajax_id']) ){
            $data = array(
                'title'=>$_GP['ajax_title']
            );
            mysqld_update('shop_dish',$data,array('id'=>$_GP['ajax_id']));
            die(showAjaxMess('200',$_GP['ajax_title']));
        }else{
            die(showAjaxMess('1002','修改失败'));
        }
    }

    public function ajax_total()
    {
        $_GP = $this->request;
        if ( !empty($_GP['ajax_id']) ){
            $data = array(
                'total'=>$_GP['ajax_stock']
            );
            mysqld_update('shop_dish',$data,array('id'=>$_GP['ajax_id']));
            die(showAjaxMess('200',$_GP['ajax_stock']));
        }else{
            die(showAjaxMess('1002','修改失败'));
        }
    }

    public function post()
    {
        $_GP = $this->request;
        $taxlist = mysqld_selectall("SELECT * FROM ".table('shop_tax'));
        $id = intval($_GP['id']);
        if (!empty($id)) {
            $item = mysqld_select("SELECT a.*,b.title as gname,b.id as gid,b.thumb as gthumb FROM " . table('shop_dish') . " AS a LEFT JOIN " . table('shop_goods') . " as b on a.gid = b.id WHERE a.id = :id", array(':id' => $id));
            if (empty($item)) {
                message('抱歉，商品不存在或是已经删除！', '', 'error');
            }


            $piclist = mysqld_selectall("SELECT * FROM " . table('shop_dish_piclist') . " where goodid={$id} ORDER BY id ASC");

        }

        include page('dish/dish_add');
    }

    public function do_post()
    {
        $_GP = $this->request;

        if (empty($_GP['pcate'])) {
            message('请选择商品分类！');
        }
        //非一般商品时   以下isset要有，对于权限操作后不可以见，所加的。
        elseif(isset($_GP['type']) && intval($_GP['type'])!=0 && (empty($_GP['timestart']) || empty($_GP['timeend'])))
        {
            message('请设置促销时间！');
        }
        //团购商品时
        elseif(isset($_GP['type']) && intval($_GP['type'])==1 && isset($_GP['team_buy_count']) && empty($_GP['team_buy_count']))
        {
            message('请设置成团人数！');
        }
        elseif (isset($_GP['type']) && intval($_GP['type'])==1 && isset($_GP['draw']) && $_GP['draw'] == 1 && isset($_GP['team_draw_num']) && empty($_GP['team_draw_num'])) {
            message('请设置抽奖人数！');
        }

        // 获取模板产品库的数据
        $shop_goods = mysqld_select("SELECT * FROM ". table('shop_goods') . " WHERE id = ".intval($_GP['c_goods'])." limit 1 ");
        //不要加类型转换，PHP本就是弱类型不用做类型转换，加了类型转化，会破坏原始数据格式,有些时候影响业务
        $data = array();
        if(empty($id)){    //这一步是为兼顾，权限，因为有些管理员不能修改价钱
            //新添加数据
            $timeprice    = !empty($_GP['timeprice']) && ($_GP['timeprice'] > 0) ?$_GP['timeprice']:$shop_goods['marketprice'];
            $marketprice  = !empty($_GP['marketprice']) && ($_GP['marketprice'] > 0)?$_GP['marketprice']:$shop_goods['marketprice'];
            $productprice = !empty($_GP['productprice']) && ($_GP['productprice'] > 0)?$_GP['productprice']:$shop_goods['productprice'];
            $app_marketprice = !empty($_GP['app_marketprice']) && ($_GP['app_marketprice'] > 0) ? $_GP['app_marketprice'] : $marketprice;
        }else{
            $timeprice    = $_GP['timeprice'];
            $marketprice  = $_GP['marketprice'] == 0 ? $shop_goods['marketprice'] : $_GP['marketprice'];
            $productprice = $_GP['productprice'];
            $app_marketprice = !empty($_GP['app_marketprice']) && ($_GP['app_marketprice'] > 0) ? $_GP['app_marketprice'] : $marketprice;
        }
        $data = array(
            'pcate' => intval($_GP['pcate']),
            'ccate' => intval($_GP['ccate']),
//					'taxid' => intval($_GP['taxid']),
            'timeprice'=> $timeprice,
            'gid'  => intval($_GP['c_goods']),
//                    'status' => $_GP['status'],
            'displayorder' => intval($_GP['displayorder']),
            'title' =>  !empty($_GP['dishname'])?$_GP['dishname']:$shop_goods['title'],
            'description' => $_GP['description'],
            'content' => htmlspecialchars_decode($_GP['content']),
            'dishsn' => $_GP['dishsn'],
            'explain' => $_GP['explain'],
            'advertising'=>$_GP['advertising'],
            'headline'=>$_GP['headline'],
            'productsn' => $_GP['productsn'],
//                    'marketprice' => $marketprice,
            'weight' => $_GP['weight'],
//                    'productprice' => $productprice,
//                    'commision' => $_GP['commision']/100,
            'total' => intval($_GP['total']),
            'totalcnf' => intval($_GP['totalcnf']),
            'credit' => intval($_GP['credit']),
            'createtime' => TIMESTAMP,
            'isnew' => intval($_GP['isnew']),
            'isfirst' => intval($_GP['isfirst']),
            'ishot' => intval($_GP['ishot']),
            'isjingping' => intval($_GP['isjingping']),
            'issendfree' => intval($_GP['issendfree']),
            'ispurchase'=>intval($_GP['ispurchase']),
//                    'type' => $_GP['type'],								//促销类型
            'ishot' => intval($_GP['ishot']),
            'isdiscount' => intval($_GP['isdiscount']),
            'isrecommand' => intval($_GP['isrecommand']),
//                    'istime' => $_GP['istime'],
//                    'timestart' => strtotime($_GP['timestart']),
            'hasoption' => intval($_GP['hasoption']),
//                    'timeend' => strtotime($_GP['timeend']),
            'max_buy_quantity' => (int)$_GP['max_buy_quantity']			//单笔最大购买数量
        );

        //保税设置
        $data = getDataIsNotNull($data,'taxid',$_GP['taxid']);
        //是否销售
        $data = getDataIsNotNull($data,'status',$_GP['status']);
        $data = getDataIsNotNull($data,'marketprice',$marketprice);
        $data = getDataIsNotNull($data,'app_marketprice',$app_marketprice);
        $data = getDataIsNotNull($data,'productprice',$productprice);
        $data = getDataIsNotNull($data,'timeprice',$timeprice);
        //促销类型
        $data = getDataIsNotNull($data,'type',$_GP['type']);
        //促销时间
        $data = getDataIsNotNull($data,'istime',$_GP['istime']);
        //商品佣金比例
        $data = getDataIsNotNull($data,'commision',$_GP['commision']);
        $data = getDataIsNotNull($data,'timestart',$_GP['timestart']);
        $data = getDataIsNotNull($data,'timeend',$_GP['timeend']);


        //团购商品时
        if(intval($_GP['type'])==1)
        {
            $data = getDataIsNotNull($data,'team_buy_count',$_GP['team_buy_count']);
            $data = getDataIsNotNull($data,'draw',$_GP['draw']);
            if ($_GP['draw'] !== NULL) {
                //因为加入权限后，部分字段不可以见，再修改的时候，提交得不到数据，会被修改为0。故加这个判断
                if($_GP['draw'] == 1)
                    $data['draw_num'] = (int)$_GP['team_draw_num'];
                else
                    $data['draw_num'] = 0;
            }
        }
        $c_p = mysqld_select("SELECT * FROM ".table("shop_goods")." WHERE id = ".$_GP['c_goods']);
        $data['p1'] = $c_p['pcate'];
        $data['p2'] = $c_p['ccate'];
        $data['p3'] = $c_p['ccate2'];
        if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['thumb'] = $upload['path'];
        }
        if (empty($id)) {
            $data['sales']=0;
            mysqld_insert('shop_dish', $data);
            $id = mysqld_insertid();
            if ( empty($id) ){
                message('宝贝已存在，请勿重复添加');
            }
        } else {
            unset($data['createtime']);
            unset($data['sales']);
            mysqld_update('shop_dish', $data, array('id' => $id));
        }


        $hsdata=array();
        if (!empty($_GP['attachment-new'])) {
            foreach ($_GP['attachment-new'] as $index => $row) {
                if (empty($row)) {
                    continue;
                }
                $hsdata[$index] = array(
                    'attachment' => $_GP['attachment-new'][$index],
                );
            }
            $cur_index = $index + 1;
        }
        if (!empty($_GP['attachment'])) {
            foreach ($_GP['attachment'] as $index => $row) {
                if (empty($row)) {
                    continue;
                }
                $hsdata[$cur_index + $index] = array(
                    'attachment' => $_GP['attachment'][$index]
                );
            }
        }
        mysqld_delete('shop_dish_piclist', array('goodid' => $id));
        foreach ($hsdata as $row) {
            $data = array(
                'goodid' => $id,
                'picurl' =>$row['attachment']
            );
            mysqld_insert('shop_dish_piclist', $data);
        }

        message('商品操作成功！',  'refresh', 'success');

    }

    public function comment()
    {
        $_GP = $this->request;
        //订单评论时用的是goods表中的id   详情页面的$_gp['id']是dish表中的id
        $pindex = max(1, intval($_GP['page']));
        $psize = 20;
        $total = 0;
        $where = '';
        if(!empty($_GP['system'])){
            $where  = ' where system='.$_GP['system'];
        }

        if(!empty($_GP['timestart']) && !empty($_GP['timeend'])){
            $timestart = strtotime($_GP['timestart']);
            $timeend   = strtotime($_GP['timeend']);
            $where = "where comment.createtime >= {$timestart} and comment.createtime <= {$timeend}";
        }

        if(!empty($_GP['keyword'])){
            if(!empty($where)){
                $where .= " and";
            }else{
                $where = " where";
            }
            if(is_numeric($_GP['keyword'])){
                //说明要查找产品id
                $where .= " shop_dish.id={$_GP['keyword']}";
            }else{
                //说明模糊查询标题
                $where .= " shop_dish.title like '%{$_GP['keyword']}%'";
            }
        }

        $list = mysqld_selectall("SELECT comment.*,shop_dish.title,shop_dish.id as did FROM " . table('shop_goods_comment') . "   comment  left join " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid {$where} ORDER BY comment.istop desc,comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
//            ppd("SELECT comment.*,shop_dish.title,shop_dish.id as did FROM " . table('shop_goods_comment') . "   comment  left join " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid {$where} ORDER BY comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $pager = '';
        if(!empty($list)){
            //获取评论对应的图片
            foreach($list as $key=> $row){
                $list[$key]['piclist'] = mysqld_selectall("select img from ". table('shop_comment_img') ." where comment_id={$row['id']}");
            }
            // 获取评论数量
            $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_goods_comment')." as comment left join  " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid {$where}" );
            $pager = pagination($total, $pindex, $psize);
        }
        include page('dish_comment');
    }

    public function delete()
    {
        $_GP = $this->request;
        $id = intval($_GP['id']);
        $row = mysqld_select("SELECT id, thumb FROM " . table('shop_dish') . " WHERE id = :id", array(':id' => $id));
        if (empty($row)) {
            message('抱歉，商品不存在或是已经被删除！');
        }
        mysqld_delete("shop_dish", array('id' => $id));

        message('删除成功！', 'refresh', 'success');
    }

    public function delcomment()
    {
        $_GP = $this->request;
        $id = intval($_GP['id']);
        mysqld_delete("shop_goods_comment", array('id' => $id));
        mysqld_delete('shop_comment_piclist',array('comment_id'=>$id));
        message('删除成功！', 'refresh', 'success');
    }

    public function addcomment()
    {
        $_GP = $this->request;
        if(!empty($_GP['type']) && $_GP['type'] == 'new'){
            $dishid = $dish = $pager = $List = '';
            include page('dish_addcomment');

        }else{
            $pindex = max(1, intval($_GP['page']));
            $psize  = 20;
            $total  = 0;

            $dishid = $_GP['dishid'];
            $dish = mysqld_select("select * from ". table('shop_dish'). " where id={$dishid}");
            if(empty($dish)){
                message('查无此宝贝商品',refresh(),'error');
            }

            //提交的表单
            if(!empty($_GP['add_sub']) && $_GP['add_sub'] == 'sub'){
                if(empty($_GP['username']))
                    message('用户名不能为空！',refresh(),'error');
                if(empty($_GP['comment']))
                    message('评论不能为空！',refresh(),'error');

                $face  = '';
                $ispic = 0;
                if($_FILES['face']['error'] != 4){   //等于4没有内容
                    $upload = file_upload($_FILES['face']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $face  = $upload['path'];
                    $ispic = 1;
                }


                $data = array(
                    'createtime' => time(),
                    'username'   => $_GP['username'],
                    'comment'    => $_GP['comment'],
                    'rate'       => $_GP['rate'],
                    'goodsid'    => $dish['gid'],
                    'face'       => $face,
                    'ispic'      => $ispic
                );
                if($_GP['system'] == 0){
                    $rand = mt_rand(1,1000);   //随机取得系统设备3是ios 2安卓 1pc
                    $num = $rand%4;
                    if($num == 0)
                        $num = 1;
                }else{
                    $num = $_GP['system'];
                }
                $data['system'] = $num;
                mysqld_insert('shop_goods_comment',$data);
                $lastid = mysqld_insertid();
                $url    = web_url('dish',array('op'=>'addcomment','dishid'=>$dishid));
                if($lastid){
                    if(!empty($_GP['picurl'])){
                        foreach($_GP['picurl'] as $picurl){
                            mysqld_insert('shop_comment_img',array('img'=>$picurl,'comment_id'=>$lastid));
                        }
                    }
                    message('操作成功！',$url,'success');
                }else{
                    message('操作失败！',$url,'error');
                }
            }

            $total = 0;
            $pager = '';
            $list  = mysqld_selectall("SELECT comment.*,shop_dish.title,shop_dish.id as did FROM " . table('shop_goods_comment') . "   comment  left join " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid where shop_dish.id={$dishid} ORDER BY comment.istop desc, comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
//                pp("SELECT comment.*,shop_dish.title,shop_dish.id as did FROM " . table('shop_goods_comment') . "   comment  left join " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid where shop_dish.id={$dishid} ORDER BY comment.istop desc, comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
//                ppd($list);
            if(!empty($list)){
                //获取评论对应的图片
                foreach($list as $key=> $row){
                    $list[$key]['piclist'] = mysqld_selectall("select img from ". table('shop_comment_img') ." where comment_id={$row['id']}");
                }
                // 获取评论数量
                $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_goods_comment')." where goodsid={$list[0]['goodsid']}");
                $pager = pagination($total, $pindex, $psize);
            }
            include page('dish_addcomment');
        }
    }


    public function topcomment()
    {
        $_GP = $this->request;
        if($_GP['istop'] == 1)
            $istop = 0;    //取消置顶
        else
            $istop = 1;    //置顶评论
        mysqld_update('shop_goods_comment',array('istop'=>$istop),array('id'=>$_GP['id']));
        message('操作成功！',refresh(),'success');
    }

    public function downcomment()
    {
        $_GP = $this->request;
        //下沉沉到中下位置如第三页或者第四页，而不是沉到底，排在最后一页  一页算15个
        $id  = $_GP['id'];
        $gid = $_GP['gid'];
        $data = mysqld_selectall("select id,createtime from ".table('shop_goods_comment')." where goodsid={$gid} order by id desc");

        $num  = count($data)-1;
        $j = 0;
        foreach($data as $row){
            $j++;
            if($row['id'] == $id){
                break;
            }
        }
        $zhong = floor($num / 2);
        $xia   = floor($zhong / 2);
        $key   = $zhong + $xia;
        $time  = $data[$key]['createtime'];
        $res   = mysqld_update("shop_goods_comment",array('createtime'=>$time),array('id'=>$id));
        if($res){
            message("操作成功！",refresh(),'success');
        }else{
            message("操作失败！",refresh(),'error');
        }
    }

    public function open_groupbuy()
    {
        $_GP = $this->request;
        //凑单开关 关闭或者开启
        //先判断是否有虚拟用户
        $member = mysqld_select("select openid from ".table('member')." where dummy=1");
        if(empty($member))
            message("对不起，请到会员管理注册批量的虚拟用户",refresh(),'error');

        if($_GP['act'] == 'open'){
            mysqld_update('shop_dish',array('open_groupbuy'=>1),array('id'=>$_GP['id']));
        }else if($_GP['act'] == 'close'){
            mysqld_update('shop_dish',array('open_groupbuy'=>0),array('id'=>$_GP['id']));
        }
        message('操作成功',refresh(),'success');

    }

    public function replycomment()
    {
        $_GP = $this->request;
        // 评论回复
        $id  = $_GP['id'];
        $reply = $_GP['reply'];

        if (empty($reply)) {
            $reply = NULL;
        }
        if (!empty($id)) {
            $re = mysqld_update("shop_goods_comment",array('reply'=>$reply),array('id'=>$id));
        }
        if ($re) {
            message("回复成功！",refresh(),'success');
        }else{
            message("回复失败，不能回复重复的内容！",refresh(),'error');
        }

    }
}


