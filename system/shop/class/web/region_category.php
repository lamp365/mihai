<?php

$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
$tableName ='shop_category';

switch ($operation) {
    case "delete":
        !$_GP['id']&& message('没有对应ID', refresh(), 'error');
        mysqld_delete('region_category', array('rc_id' => $_GP['id']));
        message(LANG('COMMON_DELETE_SUCCESS'), refresh(), 'success');
        break;
    case "updateLimit":
        //ppd($_GP);
        !$_GP['displayLimit']&& message('请先添加区域', refresh(), 'error');
        foreach ($_GP['displayLimit'] as $pk_id => $limit) {
            mysqld_update('region_category', array('rc_region_limit'=>$limit), array('rc_id' => $pk_id));
        }
        message(LANG('COMMON_OPERATION_SUCCESS'), refresh(), 'success');
        break;
    
    case "post":
        !$_GP['rc_shop_cate_id']&& message('没有分类ID', refresh(), 'error');
        !$_GP['cate_3']&& message('请选择区域', refresh(), 'error');
              
        $find =	 mysqld_select("SELECT * FROM ".table('region_category')." where rc_shop_cate_id=:rc_shop_cate_id and rc_region_code =:rc_region_code", 
            array(
                ':rc_shop_cate_id' =>  intval($_GP['rc_shop_cate_id']),
                ':rc_region_code'=> intval($_GP['cate_3']))
            );
        $find && message('此区域已经添加，请直接设置对应区域', refresh(), 'error');
        
        $data = array(
                'rc_shop_cate_id' => intval($_GP['rc_shop_cate_id']),
                'rc_region_limit' => intval($_GP['limit']),
                'rc_region_code'  => intval($_GP['cate_3']),
                'rc_region_city_id'  => intval($_GP['city_id']),
                'rc_region_province_id'  => intval($_GP['province_id']),
        );
        
        if(!$_GP['id']){
            mysqld_insert('region_category', $data);
        }else{
            $data['rc_id'] =   $_GP['id'];
            mysqld_update('region_category', $data, array('reg_cst_id' => $id));
        }
        message(LANG('COMMON_OPERATION_SUCCESS'), refresh(), 'success');
        break;
    
    case "addRegion":
        $result = getProvincesOfRegion();
        $childrens = array();
        //*************查询区域数据，用来匹配中文名************//
        $regionService = new \service\seller\regionService();
        $region_category =  $regionService->getAllData();
        foreach ($region_category as $cid => $cate) {
            if (!empty($cate['parent_id'])) {
                $childrens[$cate['parent_id']][$cate['region_id']] = array(
                    $cate['region_id'], 
                    $cate['region_name'],
                    $cate['region_code'],
                );
            }
        }
        //分类ID换个KEY名字
        $_GP['rc_shop_cate_id']=$_GP['id'];
        unset($_GP['id']);
        
        //ppd($childrens);
        include page('region_category/addRegion');
        break;
    
    case "display":
        #1构造查询条件where
        $Where = ' where deleted = 0 and  enabled =1 ';
        if($_GP['rec_pid']){
            $Where .= " and  parentid =". intval($_GP['rec_pid']) ." ";
        }else{
            $Where .= " and  parentid =0 ";
        }
       
        $_GP['reg_name'] && $Where .= " AND name like '%" . trim($_GP['reg_name']) . "%' ";
//        $_GP['region_name'] && $Where .= "AND name  like '%" . trim($_GP['region_name']) . "%' ";
//        echo "SELECT * FROM " . table($tableName) . "  $Where ORDER BY id ASC";die();
        #2 sql查询
        $result = mysqld_selectall("SELECT * FROM " . table($tableName) . "  $Where ORDER BY parentid ASC");
        //echo("SELECT * FROM " . table($tableName) . " JOIN ".table('region')."  ON region_id = rec_city_id $Where ORDER BY rec_id ASC");die();
        if(checkIsAjax()){
            empty($result)?ajaxReturnData(0,''):ajaxReturnData(1,$result);
        }else{
            include page('region_category/region_category_list');
        }
        break;
        
         //属性列表
    case "listCityLimit"://列表页批量提交顺序更新
        //$_GP['id']= 1;
        if(empty($_GP['id'])){
            message('对不起，参数有误！',refresh(),'error');
        }
        $info =	 mysqld_select("SELECT * FROM ".table('shop_category')." where id=:id ", array(':id' => $_GP['id']));
        //ppd($info);
        $result = mysqld_selectall(
            "select * from ".table('region_category')
            ." join  ".table('region')."  on region_code= rc_region_code"
            ." where rc_shop_cate_id={$_GP['id']} order by rc_region_code desc"
        );
        if($result){
            //*************查询区域数据，用来匹配中文名************//
            $goodsService = new \service\seller\regionService();
            $groups =  $goodsService->getAllData();
            $result =  $Service->getCategoryAndCount(array('parentid'=>'<>0 ','rc_region_code'=>$_GP['region_code']));//查询并统计店铺数量
            $reGroup = array_column($groups, "region_name","region_id");
            //*************匹配中文名************//
            foreach ($result as $re_key=>$single) {
                $single['city_name'] = $reGroup[  $single['rc_region_city_id'] ];
                $single['province_name'] =  $reGroup[  $single['rc_region_province_id'] ];
                $result[$re_key] = $single;
            }
        }
        
        //ppd($info);
        include page('region_category/region_category_detail_list');
        break;
        
    case "batchSetOrder"://列表页批量提交,原来更新显示顺序，现在更新为分类默认限制数量
        if(empty($_GP['displayorder'])){
            message(LANG('COMMON_OPERATION_SUCCESS'), web_url('region_category', array('op' => 'display')), 'success');
        }else{
            foreach ( $_GP['displayorder'] as $id_key => $id_order) {
                if($id_order<=0){
                    continue;
                }
                mysqld_update($tableName, array('limit'=>  intval($id_order)), array('id' => $id_key));
            }
            message(LANG('COMMON_UPDATE_SUCCESS'), web_url('region_category', array('op' => 'display')), 'success');
        }
        break;
    case "UpdateLimitSingle"://列表页批量提交,原来更新显示顺序，现在更新为分类默认限制数量
        if(empty($_GP['id'])){
            ajaxReturnData(0, '缺少参数');
        }else{
            $id_order = $_GP['limit'];
            $id_key   = $_GP['id'];
            if($id_order<=0){
                ajaxReturnData(0, '非法输入，数量不能少于0');
            }
            $affect = mysqld_update($tableName, array('limit'=>  intval($id_order)), array('id' => $id_key));
            ajaxReturnData(1, LANG('COMMON_UPDATE_SUCCESS'));
        }
        break;
    
}


