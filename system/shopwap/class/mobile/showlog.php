<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/6/29
 * Time: 16:13
 */
namespace shopwap\controller;

class showlog extends \common\controller\basecontroller
{
    public function index()
    {
        $_GP = $this->request;
        $find = $_GP['key'];
        if(empty($find))
            $find = '*';

        $listFile = glob(WEB_ROOT.'/logs/'.$find);
        $str = '';
        foreach( $listFile as $file) {
            $file     = str_replace(WEB_ROOT,'',$file);
            $file_arr = explode('/',$file);
            $parame   = array_pop($file_arr);
            $url      = mobile_url('showlog',array('op'=>'see','file'=>$parame));
            $str .= "<a href='{$url}'>{$parame}</a><br/>";
        }
        echo $str;
    }

    function see()
    {
        $_GP = $this->request;
        if(empty($_GP['file'])){
            die('参数有误！');
        }
        $file = WEB_ROOT.'/logs/'.$_GP['file'];
        if(!file_exists($file)){
            die('文件不存在！');
        }
        $content = file_get_contents($file);
        $url     = mobile_url('showlog',array('op'=>'clean','file'=>$_GP['file']));
        $url2     = mobile_url('showlog');
        echo "<a href='{$url}'>清除内容</a> | <a href='{$url2}'>返回列表</a><br/></hr>";
        echo $content;
    }

    public function clean()
    {
        $_GP = $this->request;
        if(empty($_GP['file'])){
            die('参数有误！');
        }
        $file = WEB_ROOT.'/logs/'.$_GP['file'];
        if(!file_exists($file)){
            die('文件不存在！');
        }
        file_put_contents($file,' ');
        $url     = mobile_url('showlog',array('op'=>'see','file'=>$_GP['file']));
        header("location:".$url);
    }

}