<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/12
 * Time: 17:49
 */
namespace shop\controller;

class shopruler extends \common\controller\basecontroller
{
    public function index()
    {
        //获取顶级菜单
        $rulerservice = new \service\seller\shoprulerService();
        $menudata     = $rulerservice->getSystemMenuRule();
        include page('shopruler/index');
    }

    /**
     * 添加顶级菜单
     */
    public function addmenu()
    {
        $_GP = $this->request;
        //获取顶级菜单
        $menu     = mysqld_selectall('select rule_id,rule_name from '.table('seller_rule')." where pid=0 order by sort asc");
        $editMenu = array();
        if(!empty($_GP['rule_id'])){
            $editMenu = mysqld_select("select * from ".table('seller_rule')." where rule_id={$_GP['rule_id']}");
        }
        include page('shopruler/addmenu');
    }

    /**
     * 添加顶级菜单  表单处理
     */
    public function do_addmenu()
    {
        $_GP = $this->request;
        if(empty($_GP['rule_name'])){
            message('菜单名不能为空！',refresh(),'error');
        }
        $modop = $_GP['modop'] ?: 'index';
        if(empty($_GP['pid'])){
            $data = array(
                'rule_name'  => $_GP['rule_name'],
                'sort' 	   => $_GP['sort'],
            );
        }else{
            if(empty($_GP['modname']) || empty($_GP['moddo'])){
                message('控制器和方法不能为空！',refresh(),'error');
            }
            $data = array(
                'rule_name'  => $_GP['rule_name'],
                'modname'    => $_GP['modname'],
                'moddo'      => $_GP['moddo'],
                'modop'      => $modop,
                'url'        => $_GP['modname'].'/'.$_GP['moddo'].'/'.$modop,
                'sort' 	     => $_GP['sort'],
                'act_type'   => $_GP['act_type'],
                'pid'        => $_GP['pid'],
            );
        }

        if(empty($_GP['rule_id'])){
            mysqld_insert('seller_rule',$data);
        }else{
            mysqld_update('seller_rule',$data,array('rule_id'=>$_GP['rule_id']));
        }
        message('操作成功！',refresh(),'success');
    }

    /**
     * 添加子菜单
     */
    public function add_sonmenu()
    {
        $_GP = $this->request;
        $editMenu = array();
        if(!empty($_GP['rule_id'])){
            //编辑
            $editMenu = mysqld_select("select * from ".table('seller_rule')." where rule_id={$_GP['rule_id']}");
            $menu     = mysqld_select("select * from ".table('seller_rule')." where rule_id={$editMenu['pid']}");
        }else{
            //新添加  先得到顶级父类
            $menu = mysqld_select("select * from ".table('seller_rule')." where rule_id={$_GP['pid']}");
        }
        include page('shopruler/add_sonmenu');
    }

    /**
     * 添加子菜单 表单提交处理
     */
    public function do_add_sonmenu()
    {
        $_GP = $this->request;
        if(empty($_GP['rule_name'])){
            message('菜单名不能为空！',refresh(),'error');
        }
        if(empty($_GP['modname']) || empty($_GP['moddo'])){
            message('控制器和方法不能为空！',refresh(),'error');
        }
        $modop = $_GP['modop'] ?: 'index';

        $data = array(
            'rule_name'  => $_GP['rule_name'],
            'modname'    => $_GP['modname'],
            'moddo'      => $_GP['moddo'],
            'modop'      => $modop,
            'url'        => $_GP['modname'].'/'.$_GP['moddo'].'/'.$modop,
            'sort' 	     => $_GP['sort'],
            'act_type'   => $_GP['act_type'],
            'pid'        => $_GP['pid'],
        );

        if(empty($_GP['rule_id'])){
            mysqld_insert('seller_rule',$data);
        }else{
            mysqld_update('seller_rule',$data,array('rule_id'=>$_GP['rule_id']));
        }
        message('操作成功！',refresh(),'success');
    }

    /**
     * 设置菜单排序
     */
    public function menusort()
    {
        $_GP = $this->request;
        //更新排序
        if(!empty($_GP['rule_id']) && !empty($_GP['sort'])){
            mysqld_update('seller_rule',array('sort'=>$_GP['sort']),array('rule_id'=>$_GP['rule_id']));
            ajaxReturnData(1,'操作成功');
        }
        ajaxReturnData(0,'操作失败');
    }

    public function sonmenuList()
    {
        $_GP = $this->request;
        //当前菜单
        $this_menu = mysqld_select("select * from ".table('seller_rule') ." where rule_id={$_GP['rule_id']}");
        //该菜单下的所有子菜单
        $sonmenu  = mysqld_selectall("select * from ".table('seller_rule') ." where pid={$_GP['rule_id']} order by sort asc");
        include page('shopruler/sonmenuList');
    }

    public function delmenu()
    {
        $_GP = $this->request;
        if(empty($_GP['rule_id'])){
            message('对不起，参数有误！',refresh(),'error');
        }
        if(is_array($_GP['rule_id'])){
            foreach($_GP['rule_id'] as $rule_id){
                mysqld_delete('seller_rule',array('rule_id'=>$rule_id));
            }
        }else{
            //有子类先删除
            mysqld_delete('seller_rule',array('pid'=>$_GP['rule_id']));
            mysqld_delete('seller_rule',array('rule_id'=>$_GP['rule_id']));
        }
        message('删除成功',refresh(),'success');
    }

    public function group()
    {
        $_GP = $this->request;
        //获取分组
        $field  = 'group_id,group_name,description,createtime';
        $group  = mysqld_selectall("select {$field} from ".table('seller_group'));
        include page('shopruler/group_list');
    }

    public function addgroup()
    {
        $_GP    = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        if(!empty($_GP['do_add'])){
            $res = $rulerservice->do_addgroup($_GP);
            if($res){
                message(LANG('COMMON_ADD_SUCCESS'),refresh(),'success');
            }else{
                message($rulerservice->getError(),refresh(),'error');
            }
        }

        //获取商家总的菜单节点
        $menulist      = $rulerservice->getSystemMenuRule();
        //其他权限
        $sellerActRule = \MenuEnum::$sellerActRule;
        $sellergroup   = array('rule'=>array(),'other_rule'=>array());
        include page('shopruler/addgroup');

    }

    /**
     * 编辑角色分组
     */
    public function editgroup()
    {
        $_GP    = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        if(!empty($_GP['do_add'])){
            $res = $rulerservice->do_addgroup($_GP);
            if($res){
                message(LANG('COMMON_OPERATION_SUCCESS'),refresh(),'success');
            }else{
                message($rulerservice->getError(),refresh(),'error');
            }
        }

        //获取商家总的菜单节点
        $menulist      = $rulerservice->getSystemMenuRule();
        //其他权限
        $sellerActRule = \MenuEnum::$sellerActRule;
        //获取改组的信息
        $sellergroup = mysqld_select("select * from ".table('seller_group')." where  group_id={$_GP['group_id']}");
        $sellergroup['rule']       = empty($sellergroup['rule']) ? array() : explode(',',$sellergroup['rule']);
        $sellergroup['other_rule'] = empty($sellergroup['other_rule']) ? array() : explode(',',$sellergroup['other_rule']);
        include page('shopruler/addgroup');
    }

    /**
     * 清除节点缓存
     */
    public function cleanMenu()
    {
        if(class_exists('Memcached')){
            $memcache = new \Mcache();
            $memcache->delete('getSeller_SystemMenuRule');
        }
        message('清除缓存成功',refresh(),'success');
    }
}