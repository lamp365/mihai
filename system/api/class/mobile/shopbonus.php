<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/7/5
 * Time: 14:05
 */
namespace api\controller;

class shopbonus extends \api\controller\base
{

    //优惠券列表
    public function index()
    {
        $_GP = $this->request;
        $pindex = max(1, intval($_GP['page']));
        $psize  = $_GP['limit'] ?: 15;

        $condition = '';
        $shopBonusService  = new \service\seller\ShopBonusService();
        $bonuslist         = $shopBonusService->couponList($pindex,$psize,$condition);
        ajaxReturnData(1,'请求成功',$bonuslist);
    }

    //优惠券表单添加以及编辑
    public function addcoupon()
    {
        $_GP = $this->request;

        if (!empty($_FILES['coupon_img']['tmp_name'])) {
            $upload = file_upload($_FILES['coupon_img']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $_GP['coupon_img'] = $upload['path'];
        }
        $shopBonusService  = new \service\seller\ShopBonusService();
        $res = $shopBonusService->addCoupon($_GP,$_GP['id']);
        if($res) {
            ajaxReturnData(1,'优惠券添加成功');
        }else{
            ajaxReturnData(0,$shopBonusService->getError());
        }
    }

    //获取要编辑的优惠券
    public function getcoupon()
    {
        $_GP            = $this->request;
        if(empty($_GP['id'])){
            ajaxReturnData(0,'优惠卷id不能为空!');
        }
        $member = get_member_account();
        $shopBonusService  = new \service\seller\ShopBonusService();
        $coupon = $shopBonusService->getOneCoupon($_GP['id'],'',$member['store_sts_id']);
        if(!$coupon){
            ajaxReturnData(0,$shopBonusService->getError());
        }
        $coupon['coupon_amount']        = FormatMoney($coupon['coupon_amount'],1);
        $coupon['amount_of_condition']  = FormatMoney($coupon['amount_of_condition'],1);
        $coupon['store_category_idone_text']  = '';
        $coupon['store_category_idtwo_text']  = '';

        $dish_info = array();
        if($coupon['usage_mode']==3) {
            //找出单品  目前 store_shop_dishid 是一个逗号分隔的
            $dishidArr = explode(',',$coupon['store_shop_dishid']);
            $dishid    = $dishidArr[0];  //app目前只能 单品对应一个
            $coupon['store_shop_dishid'] = $dishid;
            $dish_info = mysqld_select("select id,title,thumb from ".table('shop_dish')." where id={$dishid}");
        } else if($coupon['usage_mode']==2){
            //找出分类
            if(!empty($coupon['store_category_idone'])){
                $cate_info = mysqld_select("select name from ".table('shop_category')." where id={$coupon['store_category_idone']}");
                $coupon['store_category_idone_text']  = $cate_info['name'] ?: '';
            }
            if(!empty($coupon['store_category_idtwo'])){
                $cate_info = mysqld_select("select name from ".table('shop_category')." where id={$coupon['store_category_idtwo']}");
                $coupon['store_category_idtwo_text']  = $cate_info['name'] ?: '';
            }
        }
        empty($dish_info) && $dish_info = new \stdClass();
        $data = array('coupon'=>$coupon,'dish_info'=>$dish_info);
        ajaxReturnData(1,'操作成功！',$data);
    }

    /**
     * 删除优惠卷
     */
    public function delcoupon()
    {
        $_GP            = $this->request;
        if(empty($_GP['id'])){
            ajaxReturnData(0,'优惠卷id不能为空!');
        }
        $member = get_member_account();
        $res = mysqld_delete('store_coupon',array('scid'=>$_GP['id'],'store_shop_id'=>$member['store_sts_id']));
        if($res){
            ajaxReturnData(1,'删除成功！');
        }else{
            ajaxReturnData(0,'删除失败！');
        }
    }
}