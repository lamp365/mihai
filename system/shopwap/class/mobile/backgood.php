<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/10/27
 * Time: 13:23
 */
$member = get_member_account(true,true);
$openid = $member['openid'] ;

$op            = $_GP['op'];
$order_id      = $_GP['orderid'];
$order_good_id = $_GP['order_good_id'];
if(empty($op) || empty($order_id) || empty($order_good_id)){
    message('对不起，非法访问!',refresh(),'error');
}

$orderInfo      = mysqld_select("select * from ". table('shop_order') ." where id={$order_id} and openid='{$openid}'");
$orderGoodInfo  = mysqld_select("select * from ". table('shop_order_goods') ." where id={$order_good_id} and orderid={$order_id}");

if(empty($orderInfo) || empty($orderGoodInfo)){
    message('对不起，订单不存在!',refresh(),'error');
}

switch($op){
    case 'canclemoney':  //取消退款申请
        if($orderGoodInfo['shop_type'] == 1)
            message('对不起，团购商品不允许退款！',refresh(),'error');

        if($orderGoodInfo['status'] == 1 && $orderGoodInfo['type'] == 3){
            $res = mysqld_update('shop_order_goods',array('status'=>'-2'),array('id'=>$order_good_id));
            if($res){
                //添加售后记录
                $aftersales = mysqld_select ( "SELECT aftersales_id FROM " . table ( 'aftersales' )." WHERE order_goods_id = {$order_good_id} ");

                //退款日志数组
                $arrLog = array(
                    'aftersales_id' => $aftersales['aftersales_id'],
                    'order_goods_id'=> $order_good_id,
                    'status'        => '-2',
                    'title'         => '买家已经取消退款申请',
                    'createtime'    => date ( 'Y-m-d H:i:s' )
                );

                //新增退款日志记录
                mysqld_insert ( 'aftersales_log', $arrLog);

                //给APP卖家推送消息
                if(!empty($orderGoodInfo['seller_openid']) && !empty($orderGoodInfo['commision'])){
                    $time     = date("Y-m-d H:i:s",$orderInfo['createtime']);
                    $dishInfo = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$orderGoodInfo['goodsid']}");
                    $msg  = "老板，您好！{$arrLog['title']}
订单编号：{$orderInfo['ordersn']}
退款商品：{$dishInfo['title']}
下单时间：{$time}";
                    pushOrderImMessage(IM_ORDER_FROM_USER,$orderdata['seller_openid'],$msg);
                }

                message('已取消退款申请',refresh(),'success');
            }else{
                message('对不起，操作有误',refresh(),'error');
            }
        }else{
            message('对不起，非法访问!',refresh(),'error');
        }
        break;

    case 'shouhuo' :  //可能是退款  也可能是退货
        if($orderGoodInfo['shop_type'] == 1)
            message('对不起，团购商品不允许退货退款！',refresh(),'error');

        if(empty($_GP['reason'])){
            message("对不起，请输入原因!",refresh(),'error');
        }

        if($orderGoodInfo['status'] == 0 && $orderGoodInfo['type'] == 0){  //防止非法改参数手动输入地址 进行访问
            $res = mysqld_update('shop_order_goods',array('status'=>'1','type'=>$_GP['type']),array('id'=>$order_good_id));
            if($res){
                //插入售后信息
                $data1 = array(
                    'order_goods_id' => $order_good_id,
                    'reason'         => $_GP['reason'],
                    'description'    => $_GP['description'],
                    'createtime'     => date ( 'Y-m-d H:i:s' ),
                    'modifiedtime'   => date ( 'Y-m-d H:i:s' ),
                );

                $arrLogContent['evidence_pic'] = '';
                if(!empty($_GP['picurl']) && $_GP['type'] == 1){
                    $data1['evidence_pic']         = implode ( ";", $_GP['picurl'] );
                    $arrLogContent['evidence_pic'] = implode ( ";", $_GP['picurl'] );
                }
                if($_GP['type'] == 1)
                    $zi = "退款退货";
                else
                    $zi = "仅退款";
                $arrLogContent['type']          = $zi;
                $arrLogContent['reason']        = $_GP['reason'];
                $arrLogContent['description']   = $_GP['description'];

                $data2 = array(
                    'order_goods_id'  => $order_good_id,
                    'status'          => 1,
                    'title'           => "买家发起了{$zi}申请",
                    'content'         => serialize($arrLogContent),
                    'createtime'      => date ( 'Y-m-d H:i:s' )
                );
                mysqld_insert('aftersales',$data1);
                $aftersales_id = mysqld_insertid();
                if($aftersales_id){
                    $data2['aftersales_id'] = $aftersales_id;
                    mysqld_insert('aftersales_log',$data2);


                    //给APP卖家推送消息
                    if(!empty($orderGoodInfo['seller_openid']) && !empty($orderGoodInfo['commision'])){
                        $time     = date("Y-m-d H:i:s",$orderInfo['createtime']);
                        $dishInfo = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$orderGoodInfo['goodsid']}");
                        $msg  = "老板，您好！{$data2['title']}
订单编号：{$orderInfo['ordersn']}
退款商品：{$dishInfo['title']}
下单时间：{$time}
";
                        pushOrderImMessage(IM_ORDER_FROM_USER,$orderdata['seller_openid'],$msg);
                    }

                    message("{$zi}申请已提交",refresh(),'success');
                }else{
                    //插入失败则撤掉操作
                    mysqld_update('shop_order_goods',array('status'=>'0','type'=>0),array('id'=>$order_good_id));
                    message("{$zi}退货申请操作败!",refresh(),'error');
                }

            }else{
                message('对不起，操作有误',refresh(),'error');
            }

        }else{
            message('对不起，非法访问!',refresh(),'error');
        }
        break;

    case 'canclegood':  //取消退款退货申请
        if($orderGoodInfo['status'] == 1 && $orderGoodInfo['type'] == 1){
            $res = mysqld_update('shop_order_goods',array('status'=>'-2'),array('id'=>$order_good_id));
            if($res){
                //添加售后记录
                $aftersales = mysqld_select ( "SELECT aftersales_id FROM " . table ( 'aftersales' )." WHERE order_goods_id = {$order_good_id} ");

                //退款日志数组
                $arrLog = array(
                    'aftersales_id' => $aftersales['aftersales_id'],
                    'order_goods_id'=> $order_good_id,
                    'status'        => '-2',
                    'title'         => '买家已经取消退款退货申请',
                    'createtime'    => date ( 'Y-m-d H:i:s' )
                );

                //新增退款日志记录
                mysqld_insert ( 'aftersales_log', $arrLog);

                //给APP卖家推送消息
                if(!empty($orderGoodInfo['seller_openid']) && !empty($orderGoodInfo['commision'])){
                    $time     = date("Y-m-d H:i:s",$orderInfo['createtime']);
                    $dishInfo = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$orderGoodInfo['goodsid']}");
                    $msg  = "老板，您好！{$arrLog['title']}
订单编号：{$orderInfo['ordersn']}
退款商品：{$dishInfo['title']}
下单时间：{$time}
";
                    pushOrderImMessage(IM_ORDER_FROM_USER,$orderdata['seller_openid'],$msg);
                }

                message('已取消退款退货申请',refresh(),'success');
            }else{
                message('对不起，操作有误',refresh(),'error');
            }
        }else{
            message('对不起，非法访问!',refresh(),'error');
        }
        break;

    case 'aftersale_detail':  //查看售后详情
        $afterSale    = mysqld_select("select * from ". table('aftersales') ." where order_goods_id={$_GP['order_good_id']}");
        if(empty($afterSale)){
            message('对不起，记录不存在!',refresh(),'error');
        }
        $afterSaleLog    = mysqld_selectall("select * from ". table('aftersales_log') ." where aftersales_id={$afterSale['aftersales_id']} order by log_id asc");
        $afterSaleDialog = mysqld_selectall("select * from ".table('aftersales_dialog')." where aftersales_id={$afterSale['aftersales_id']} order by id asc");

        if($orderInfo['openid'] != $openid){
            message('对不起，记录不存在!',refresh(),'error');
        }

        //物流货运
        $dispatchlist = mysqld_selectall("SELECT code,name FROM " . table('dispatch')." where sendtype=0" );

        $delivery_corp = $delivery_no = '';   //快递公司和单号
        if(!empty($afterSale['sendback_data'])){
            $sendback_data = unserialize($afterSale['sendback_data']);
            $delivery_name = $delivery_corp = $sendback_data['delivery_corp'] ;
            $delivery_no   = $sendback_data['delivery_no'];
            foreach($dispatchlist as $val){
                if($val['code'] == $delivery_corp){
                    $delivery_name = $val['name'];
                }
            }
        }

        $picArr = '';
        if(!empty($afterSale['evidence_pic'])){
            $picArr = explode(";",$afterSale['evidence_pic']);
        }
        // `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '申请状态，-2为撤销申请，-1为审核驳回，0为未申请，1为正在申请，2为审核通过，3为退款成功',
        if($_GP['type'] == 'money'){
            $title = '退款';
            $statusArr = array('-2'=>'撤销申请','-1'=>'审核驳回','1'=>'申请退款','2'=>'审核通过','4'=>'退款成功');
        }else{
            $title = '退货';
            $statusArr = array('-2'=>'撤销申请','-1'=>'审核驳回','1'=>'申请退货','2'=>'审核通过','3'=>'买家退货','4'=>'退货成功');
        }

        include themePage('aftersale_detail');
        break;


    case 'aftersale_dialog':   //协商对话
        if(empty($_GP['aftersales_id'])){
            message('参数有误!',refresh(),'error');
        }
        if(empty($_GP['content'])){
            message('协商内容不能为空!',refresh(),'error');
        }
        if(!setAfterSaleDialogNum())
            message('对不起，提交记录太频繁了！',refresh(),'error');

        //验证是否是本人的记录 还是非法改参数访问的
         if($orderGoodInfo['status'] != 1)
            message("该订单已经协商通过",refresh(),'error');

        if($orderInfo['openid'] != $openid)
            message("对不起，非法访问",refresh(),'error');

        $data = array(
            'aftersales_id' => $_GP['aftersales_id'],
            'role'		    => 2,
            'content'	    => $_GP['content'],
            'createtime'	=> date('Y-m-d H:i:s')
        );
        mysqld_insert('aftersales_dialog',$data);
        if(mysqld_insertid()){
            message('操作成功！',refresh(),'success');
        }else{
            message('操作失败!',refresh(),'error');
        }
        break;

    case 'sendback' : //寄回物品 快递单号
        if(empty($_GP['aftersales_id'])){
            message('参数有误!',refresh(),'error');
        }

        if(empty($_GP['delivery_corp']) || empty($_GP['delivery_no'])){
            message('物流单号信息不能为空!',refresh(),'error');
        }
        //验证是否是本人的记录 还是非法改参数访问的
        if($orderGoodInfo['type']!=1 && $orderGoodInfo['status']!=2)
            message("该订单已经提交过物流信息",refresh(),'error');

        if($orderInfo['openid'] != $openid)
            message("对不起，非法访问",refresh(),'error');

        $sendback_data['delivery_corp'] = $_GP['delivery_corp'];
        $sendback_data['delivery_no'] 	= $_GP['delivery_no'];

        $data = array ('sendback_data'	=> serialize($sendback_data),
                        'modifiedtime' 	=> date ( 'Y-m-d H:i:s' ));


        $res = mysqld_update ( 'aftersales', $data ,array('aftersales_id' =>$_GP['aftersales_id']));

        if(checksubmit('wuliu_sub')){
            message('修改成功',refresh(),'success');
        }else{
            if($res){
                //退款日志数组
                $arrLog = array('aftersales_id'	=> $_GP['aftersales_id'],
                    'order_goods_id'=> $order_good_id,
                    'status'		=> 3,
                    'title'			=> '买家已经退货',
                    'content'		=> serialize($sendback_data),
                    'createtime'	=> date ( 'Y-m-d H:i:s' )
                );

                //新增退款日志记录
                mysqld_insert ( 'aftersales_log', $arrLog);

                //修改订单 order_good状态
                mysqld_update('shop_order_goods',array('status'=>3),array('id'=>$order_good_id));
                message('操作成功！',refresh(),'success');
            }else{
                message('操作失败！',refresh(),'error');
            }
        }


        break;
}