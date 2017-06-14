<?php

/**
 * author: 王敬
 */

namespace shop\controller;

use common\controller\basecontroller;

class shop_level_manage extends basecontroller {

    protected $myTableName = 'store_shop_level';
    private $loginUser = null;
    private $typeText = array('1' => '区代理', '2' => '市代理', '3' => '省代理');

    public function index() {
//        include page('shop/advlist');exit();
        $_GP = $this->request;

        $list = mysqld_selectall('SELECT * FROM ' . table($this->myTableName) . " order by rank_level asc");
        foreach($list as &$one){
            $one['money'] = FormatMoney($one['money'],0);
        }
        include page('level/rank_list');
    }

    private function addedit() {//GET页面
        $_GP = $this->request;
        if ($_GP['rank_level']) {
            $where = '  where rank_level = ' . $_GP['rank_level'];
            $info = mysqld_select("SELECT * FROM " . table($this->myTableName) . $where);
            $info['money'] = FormatMoney($info['money'],0);
        }
//        ppd($result);
        include page('level/rank');
    }

    public function add() {
        if (checksubmit('submit')) {
            $_GP = $this->request;
            $this->core_post($_GP);
        } else {
            $this->addedit();
        }
    }

    public function edit() {//POST接口
        if (checksubmit('submit')) {
            $_GP = $this->request;
            $this->core_post($_GP);
        } else {
            $this->addedit();
        }
    }

    public function delete() {//POST接口
        $_GP = $this->request;
        !$_GP['rank_level'] && message('没有对应ID', refresh(), 'error');
        //检查有没有下挂的店铺
        mysqld_delete($this->myTableName, array('rank_level' => $_GP['rank_level']));
        message(LANG('COMMON_DELETE_SUCCESS'), refresh(), 'success');
    }

    private function formValidate($data) {
//        
//        //ppd($data);
//        if(trim($data['gc_name'])){
//            $where =  " where gc_name ='".trim($data['gc_name'])."'";
//            $find = mysqld_selectall("SELECT * FROM " . table($this->myTableName) . " {$where}");
//            if($find){
//                checkIsAjax()?ajaxReturnData(0,  LANG('此分类名已存在')): message( LANG('此分类名已存在'),refresh(),'error');
//            }
//        }
    }
    

    private function core_post($_GP) {
//        ppd($_GP,$_FILES);
        $this->formValidate($_GP);
        empty($_GP['rank_name']) && message("等级名称不能空", refresh(), 'error');
        
        #2数组处理和组织
        $data = array(
            'rank_name'  => $_GP['rank_name'],
            'money'      => FormatMoney($_GP['money']),
            'is_free'    => intval($_GP['is_free']),
            'time_range' => intval($_GP['time_range']),
            'dish_num'   => intval($_GP['dish_num']),
            'level_type' => intval($_GP['level_type'])
        );
        if (!empty($_FILES['icon']['tmp_name'])) {
            $upload = file_upload($_FILES['icon']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['icon'] = $upload['path'];
        }
        if (!empty($_FILES['wap_icon']['tmp_name'])) {
            $upload = file_upload($_FILES['wap_icon']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            $data['wap_icon'] = $upload['path'];
        }
        #3 插入数据
        if (empty($_GP['rank_level'])) {
            //每次新增寻找最大值，然后新增。等于手动自增1
            $sql = "select rank_level FROM " . table($this->myTableName) . " order by rank_level desc";
            $maxLevel = mysqld_select($sql);
            $data['rank_level'] = $maxLevel['rank_level'] + 1;
//            ppd($data);
            mysqld_insert('store_shop_level', $data);
            $effect = mysqld_insertid();
        } else {
//             ppd($data);
            $effect = mysqld_update('store_shop_level', $data, array('rank_level' => $_GP['rank_level']));
        }
        #4返回结果
        if (checkIsAjax()) {
            $effect !== false ? ajaxReturnData(1, LANG('COMMON_OPERATION_SUCCESS')) :
                    ajaxReturnData(0, LANG('COMMON_OPERATION_FAILED'));
        } else {
            message(LANG('COMMON_OPERATION_SUCCESS'), web_url('shop_level_manage'), 'success');
        }
    }

    
    
    public function dialogChoose() {
        $_GP = $this->request;
        include page('level/dialog');
    }
    public function UpgradeType() {
        $_GP = $this->request;
//        ppd($_GP);
//        $_GP['level_type'] = 3;$_GP['rank_level'] = 6;
        $info = mysqld_select("SELECT * FROM " . table($this->myTableName) ." where rank_level=  ".$_GP['rank_level'] );
        $info['level_type']==$_GP['level_type'] && message("未改动等级", refresh(), 'success');
        
        if( $info['level_type']>$_GP['level_type'] ){//降级
            $where = " where level_type >= {$_GP['level_type']} and  rank_level<={$info['rank_level']}";
        }else{//升级
            $where = " where level_type < {$_GP['level_type']} and  rank_level>={$info['rank_level']}";
        }   
        $sql = "update squdian_store_shop_level A  set level_type={$_GP['level_type']} ".$where;
        $effect = mysqld_query($sql);
        $effect !== false ?  message(LANG('COMMON_OPERATION_SUCCESS'), web_url('shop_level_manage'), 'success') :
        message(LANG('COMMON_OPERATION_FAILED'), web_url('shop_level_manage'), 'error');
        
    }
    
    public function sectionPost() {
        $_GP = $this->request;
//        ppd($_GP);
        $arr= array('dish_num','time_range','money');
        foreach ($arr as $name) {
            foreach ($_GP[ $name ] as $pk_id => $value) {
                if($name = 'money'){
                    $value = FormatMoney($value,1);
                }
                mysqld_update( $this->myTableName, array($name=>$value), array('rank_level' => $pk_id) );
            } 
        }
        message(LANG('COMMON_OPERATION_SUCCESS'), web_url('shop_level_manage'), 'success');
    }
    
}
