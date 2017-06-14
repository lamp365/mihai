<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/6/1
 * Time: 13:09
$a = new \model\shopwap\testModel();
if($a->todo()){
    //操作成功 则继续业务  返回数据或者返回true
}else{
    //操作失败，返回false，并记录error信息， 控制器可读取error原因
    message($a->getError());
}
 */
namespace model\shopwap;

class testModel extends \model\publicModel
{
    public  function todo($name = '')
    {
        if($name){
            echo '有值';
            ppd($this->db);
            return true;
        }else{
            $this->error = '参数有误！';
            return false;
        }

    }
}