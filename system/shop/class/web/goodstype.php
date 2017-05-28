<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/12
 * Time: 17:49
 */
namespace shop\controller;

class goodstype extends \common\controller\basecontroller
{

    /**
     * 模型管理
     */
    public function lists()
   {
       $_GP = $this->request;

       //优先展示第一级分类的
       $parent_category  = getCategoryAllparent();
       $first_son        = array();
       if($_GP['status'] == null || $_GP['status'] == -1){
           $where = "1 = 1";
       }else{
           $where = "status = {$_GP['status']}";
       }

       if(!empty($_GP['p1'])){
           $first_son   = getCategoryByParentid($_GP['p1']);
           $where .= " and p1={$_GP['p1']}";
       }
       if(!empty($_GP['p2'])){
           $where .= " and p2={$_GP['p2']}";
       }

       $psize =  30;
       $pindex = max(1, intval($_GP["page"]));
       $limit = ' limit '.($pindex-1)*$psize.','.$psize;

       $goodstype  = mysqld_selectall("SELECT * FROM " . table('goodstype') . "  where {$where} order by id desc {$limit}");
       $total      = mysqld_selectcolumn("select count('id') from ".table('goodstype')." where {$where}");
       $pager      = pagination($total, $pindex, $psize);

       include page('goodattr/goodstype_list');
   }

    /**
     * 添加模型
     */
    public function add_gtype()
    {
        $_GP = $this->request;
        if(checksubmit('sure_add')){
            if(empty($_GP['gtype_name']) || empty($_GP['p1'])){
                message("分类和名字不能为空！",refresh(),'error');
            }

            $data = array(
                'gtype_name' => $_GP['gtype_name'],
                'p1'   =>  intval($_GP['p1']),
                'p2'   =>  intval($_GP['p2']),
            );
            if(empty($_GP['id'])){
                mysqld_insert('goodstype',$data);
            }else{
                mysqld_update('goodstype',$data,array('id'=>$_GP['id']));
            }
            message('操作成功！',refresh(),'success');
        }


        $edit_gtype  = $first_son = array();
        if(!empty($_GP['id'])){
           $edit_gtype = mysqld_select("select * from ".table('goodstype')." where id={$_GP['id']}");
           $first_son  = getCategoryByParentid($edit_gtype['p1']);
        }
        //优先展示第一级分类
        $parent_category  = getCategoryAllparent();

        include page('goodattr/add_gtype');
    }

    //模型下下架
    public function set_status()
    {
        $_GP = $this->request;
        if(empty($_GP['id'])){
            message('对不起，参数有误！',refresh(),'error');
        }
        mysqld_update('goodstype',array('status'=>$_GP['status']),array('id'=>$_GP['id']));
        message('操作成功！！',refresh(),'success');
    }

    //属性列表
    public function gattr_list()
    {
        $_GP = $this->request;
        if(empty($_GP['id'])){
            message('对不起，参数有误！',refresh(),'error');
        }
        $gtype    = mysqld_select("select * from ".table('goodstype')." where id={$_GP['id']}");
        $attrlist = mysqld_selectall("select * from ".table('goodstype_attribute')." where gtype_id={$_GP['id']} order by attr_id desc");
        include page('goodattr/gattr_list');
    }

    //添加或者编辑显示页面
    public function add_attr()
    {
        $_GP = $this->request;
        if(empty($_GP['gtype_id'])){
            message('对不起，参数有误！',refresh(),'error');
        }
        $gtype    = mysqld_select("select * from ".table('goodstype')." where id={$_GP['gtype_id']}");

        $edit_attr = array();
        if(!empty($_GP['id'])){
            //说明是编辑
            $edit_attr = mysqld_select("select * from ".table('goodstype_attribute')." where attr_id={$_GP['id']}");
        }
        include page('goodattr/add_attr');
    }
    //提交表单操作
    public function do_add_atr()
    {
        $_GP = $this->request;
        if(empty($_GP['gtype_id'])){
            message('对不起，参数有误！',refresh(),'error');
        }
        if(empty($_GP['attr_name'])){
            message('对不起，请输入属性名称！',refresh(),'error');
        }
        $data = array(
            'attr_name'       => $_GP['attr_name'],
            'gtype_id'        => $_GP['gtype_id'],
            'attr_input_type' => $_GP['attr_input_type'],
            'attr_values'     => empty($_GP['attr_values']) ? '' : implode(',',$_GP['attr_values']),
        );
        if(empty($_GP['id'])){
            $res = mysqld_insert('goodstype_attribute',$data);
        }else{
            $res = mysqld_update('goodstype_attribute',$data,array('attr_id'=>$_GP['id']));
        }
        if($res){
            message('操作成功！',refresh(),'success');
        }else{
            message('操作失败！',refresh(),'error');
        }
    }
    //规格列表
    public function gspec_list()
    {
        $_GP = $this->request;

        if(empty($_GP['id'])){
            message('对不起，参数有误！',refresh(),'error');
        }
        $gtype    = mysqld_select("select * from ".table('goodstype')." where id={$_GP['id']}");
        $speclist = mysqld_selectall("select * from ".table('goodstype_spec')." where gtype_id={$_GP['id']} order by spec_id desc");
        foreach($speclist as $key =>  $spec_one){
            $speclist[$key]['spec_item'] = mysqld_selectall("select id,item_name,status from ".table('goodstype_spec_item')." where spec_id={$spec_one['spec_id']}");
        }
        include page('goodattr/gspec_list');
    }
    //添加或者编辑显示页面
    public function add_spec()
    {
        $_GP = $this->request;
        if(empty($_GP['gtype_id'])){
            message('对不起，参数有误！',refresh(),'error');
        }
        $total_spec = mysqld_selectall("select spec_id from ".table('goodstype_spec')." where gtype_id={$_GP['gtype_id']}");
        if(count($total_spec) >= 2){
            message('最多只能创建两个规格！',refresh(),'error');
        }
        $gtype    = mysqld_select("select * from ".table('goodstype')." where id={$_GP['gtype_id']}");

        $edit_spec = $edit_spec_item = array();
        if(!empty($_GP['id'])){
            //说明是编辑
            $edit_spec      = mysqld_select("select * from ".table('goodstype_spec')." where spec_id={$_GP['id']}");
            $edit_spec_item = mysqld_selectall("select * from ".table('goodstype_spec_item')." where spec_id={$_GP['id']}");
        }

        include page('goodattr/add_spec');
    }

    //提交 操作 添加 规格
    public function do_add_spec()
    {
        $_GP = $this->request;
        if(empty($_GP['gtype_id'])){
            ajaxReturnData(0,'对不起，参数有误！');
        }
        if(empty($_GP['spec_name'])){
            ajaxReturnData(0,'对不起，请不要为空！');
        }

        $data = array(
            'spec_name'       => $_GP['spec_name'],
            'gtype_id'        => $_GP['gtype_id']
        );
        if(empty($_GP['spec_id'])){
            $res = mysqld_insert('goodstype_spec',$data);
            $spec_id = mysqld_insertid();
        }else{
            $res = mysqld_update('goodstype_spec',$data,array('spec_id'=>$_GP['spec_id']));
            $spec_id = $_GP['$spec_id'];
        }

        if(empty($spec_id)){
            ajaxReturnData(0,'操作失败！');
        }else{
            ajaxReturnData(1,$spec_id);
        }

    }
    //提交 操作 添加 规格项
    public function do_add_specitem()
    {
        $_GP = $this->request;
        $spec_id = $_GP['spec_id'];
        if(empty($spec_id)){
            ajaxReturnData(0,'对不起，参数有误！');
        }
        if(empty($_GP['item_name'])){
            ajaxReturnData(0,'名字不能为空！');
        }

        $spec_data = array(
            'spec_id'    => $spec_id,
            'item_name'  => $_GP['item_name'],
        );
        mysqld_insert("goodstype_spec_item",$spec_data);
        if($item_id = mysqld_insertid()){
            ajaxReturnData(1,$item_id);
        }else{
            ajaxReturnData(0,'操作失败');
        }

    }

    /**
     * 设置规格项的 禁用 与启用
     */
    public function setitem_status()
    {
        $_GP = $this->request;
        if(empty($_GP['item_id'])){
            ajaxReturnData(0,'对不起，参数有误！');
        }
        mysqld_update('goodstype_spec_item',array('status'=>intval($_GP['status'])),array('id'=>$_GP['item_id']));
        ajaxReturnData(1,'操作成功！');
    }

    //modal弹框显示 属性或者规格
    public function gtype_info()
    {
        $_GP   = $this->request;
        $error = '';
        if(empty($_GP['id']) || empty($_GP['type'])){
            $error = '参数有误';
        }
        if(empty($error)){
            if($_GP['type'] == 'attr'){
                $attr_list = mysqld_selectall("select * from ".table('goodstype_attribute')." where gtype_id={$_GP['id']}");
            }else{
                $gtype    = mysqld_select("select * from ".table('goodstype')." where id={$_GP['id']}");
                $spec_list = mysqld_selectall("select * from ".table('goodstype_spec')." where gtype_id={$_GP['id']} order by spec_id desc");
                foreach($spec_list as $key =>  $spec_one){
                    $spec_list[$key]['spec_item'] = mysqld_selectall("select id,item_name,status from ".table('goodstype_spec_item')." where spec_id={$spec_one['spec_id']}");
                }
            }
        }
        include page('goodattr/gtype_info');
    }
}
