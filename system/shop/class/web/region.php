<?php

namespace shop\controller;

class region extends \common\controller\basecontroller{

    public function index()
    {
        $result = mysqld_selectall("select region_id,region_code,region_name from ".table('region')." where parent_id = 1 and  is_open=1");
        include page('region_category/region_list');
    }

    public function open_region()
    {
        $_GP = $this->request;
        $parent = mysqld_selectall("select region_id,region_code,region_name from ".table('region')." where parent_id = 1");
        include page('region_category/open_region');
    }

    /**
     * 表单提交操作 处理 区域的开通
     */
    public function do_open()
    {
        $_GP = $this->request;
        if(count($_GP['region_id']) < 2){
            message('未选择对应的区域！',refresh(),'error');
        }

        foreach($_GP['region_id'] as $region_id){
            mysqld_update('region',array('is_open'=>1),array('region_id'=>$region_id));
        }
        message('开通成功！',refresh(),'success');
    }

    /**
     * 关闭区域
     */
    public function close_region()
    {
        $_GP = $this->request;
        if(empty($_GP['region_id'])){
            ajaxReturnData(0,'id参数有误！');
        }
        //此 region_id 是第三级的
        $address = mysqld_select("select region_id,parent_id from ".table('region')." where region_id={$_GP['region_id']}");
        mysqld_update('region',array('is_open'=>0),array('region_id'=>$_GP['region_id']));
        //是否 第三级的全部已经关闭了，全关闭了，第二级的也关闭
        $check_address  = mysqld_select("select region_id,parent_id from ".table('region')." where parent_id={$address['parent_id']} and is_open=1");

        if(empty($check_address)){
            //关闭第二级
            $city = mysqld_select("select region_id,parent_id from ".table('region')." where region_id={$address['parent_id']}");
            mysqld_update('region',array('is_open'=>0),array('region_id'=>$address['parent_id']));
            //是否第二级已经全部关闭，全部关闭了，则第一级的也关闭
            $check_city  = mysqld_select("select region_id,parent_id from ".table('region')." where parent_id={$city['parent_id']} and is_open=1");

            if(empty($check_city)){
                //关闭 第一级
                mysqld_update('region',array('is_open'=>0),array('region_id'=>$city['parent_id']));
            }
        }
        ajaxReturnData(1,'操作成功！');
    }

    /**
     * 获取下一级  已经开通的区域
     */
    public function get_hasOpenCity()
    {
        $_GP = $this->request;
        if(empty($_GP['region_id'])){
            ajaxReturnData(0,'参数有误！');
        }
        $next_region = mysqld_selectall("select region_id,region_code,region_name from ".table('region')." where parent_id = {$_GP['region_id']} and is_open=1");
        if(empty($next_region)){
            ajaxReturnData(0,'无数据！');
        }
        ajaxReturnData(1,'',$next_region);
    }

    public function mange()
    {
        $_GP = $this->request;
        #1构造查询条件where
//        $Where = "  where 1=1  ";
//        $_GP['reg_name'] && $Where .= "AND reg_name like '%" . trim($_GP['reg_name']) . "%' ";
//        $_GP['region_name'] && $Where .= "AND region_name  like '%" . trim($_GP['region_name']) . "%' ";

        $region = getProvincesOfRegion();
        $childrens=array();
        $region_category = mysqld_selectall("SELECT * FROM " . table('region') . "order by region_order ASC");
        foreach ($region_category as $cid => $cate) {
            if (!empty($cate['parent_id'])) {
                $childrens[$cate['parent_id']][$cate['region_id']] = array(
                    $cate['region_id'],   $cate['region_name'],
                    $cate['region_code'], $cate['region_is_default_qu'],
                );
            }
        }
        #2 sql查询
        //$result = mysqld_selectall("SELECT reg_cst_id,reg_name,region_name FROM " . table('region_custom') . " JOIN squdian_region  ON region_id = city_id $Where ORDER BY reg_cst_id ASC");
        //ppd($result);
        include page('region_category/region_mange');
    }


    public function limit_setting()
    {
        $_GP = $this->request;
        !$_GP['region_code'] && message('请输入正确的区域！', '', 'error');
        $info = mysqld_select("select * from " . table('region') . " where region_code=:region_code  limit 1", array(":region_code" => trim($_GP['region_code'])));
        $p_info = mysqld_select("select * from " . table('region') . " where region_id=:parent_id  limit 1", array(":parent_id" => $info['parent_id']));
//        ppd($p_info);

        $Where = ' where gc_deleted = 0  ';
        $resultWhere =$Where.' and gc_pid<>0 ';
        $ParentWhere =$Where.' and gc_pid=0 ';

        $Service = new \service\shop\IndustryService();
        $result =  $Service->getCategoryAndCount($_GP['region_code']);//查询并统计店铺数量
//        ppd($result);
        $ParentResult = mysqld_selectall("SELECT * FROM " . table('industry') . " {$ParentWhere} ORDER BY gc_pid ASC");
        $ParentIDVALUE =  array_column($ParentResult, "gc_name","gc_id");

        include page('region_category/region_limit_setting');
    }

    public function setDefault()
    {
        $_GP = $this->request;
        !$_GP['region_id'] && ajaxReturnData(0,'请输入正确的区域！');

        $find = mysqld_select("select * from ".table('region')." where region_id={$_GP['region_id']}");

        //先重置
        $ori_info = mysqld_update('region',array('region_is_default_qu'=>0),array('parent_id'=>$find['parent_id']) );
        $des_info = mysqld_update('region',array("region_is_default_qu"=>1),array('region_id'=>$_GP['region_id']));
        if($des_info){
            ajaxReturnData(1,LANG('COMMON_OPERATION_SUCCESS'));
        }else{
            ajaxReturnData(0,LANG('COMMON_OPERATION_FAILED'));
        }
    }

    public function batchSetLimit()
    {
        $_GP = $this->request;
        !$_GP['region_code'] && ajaxReturnData(0,'请输入正确的区域！');
        if(empty($_GP['displayLimit'])){
            ajaxReturnData(0,'参数有误！');
        }

        $_GP['displayLimit']= array_filter($_GP['displayLimit']);//过滤空值
        $Service = new \service\seller\regionService();
        foreach ($_GP['displayLimit'] as $industry_id_key => $id_order) {
            $Service->saveRegionCategroyLimit($_GP['region_code'],$industry_id_key,$id_order);
        }
        ajaxReturnData(1,LANG('COMMON_OPERATION_SUCCESS'));
    }
}



