<?php
namespace shop\controller;

class address extends \common\controller\basecontroller
{
    public function index()
    {
        $_GP     = $this->request;
        if(checksubmit('sure_add')){
            $data['username'] = $_GP['username'];
            $data['mobile']   = $_GP['mobile'];
            $data['address']  = $_GP['address'];
            $data['code']     = $_GP['code'];

            $insert['description'] = json_encode($data);
            $insert['promoteType'] = 2;
            $insert['pname']       = '';
            $insert['condition']   = '0';
            if(empty($_GP['id'])){
                mysqld_insert('shop_pormotions',$insert);
            }else{
                mysqld_update('shop_pormotions',$insert,array('id'=>$_GP['id']));
            }
            $url = web_url('address');
            message('操作成功！',$url,'success');
        }
        $list    = mysqld_select("SELECT  * FROM " . table('shop_pormotions')." where promoteType=2" );
        $address = array();
        if(!empty($list['description'])){
            $address = json_decode($list['description'],true);
        }
        include page('yunfei/address_list');
    }
}


