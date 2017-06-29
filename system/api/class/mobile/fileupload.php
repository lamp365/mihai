<?php

/**
 * Author: 王敬
 */

namespace api\controller;
use api\controller;

class fileupload extends \common\controller\basecontroller{

    /**
     * //seller模块上传图片,可以根据具体业务再拓展代码  不需要继承base
     */
    public function index(){
//        ajaxReturnData(1,'success',array('pic_url'=> $upload['path']));//测试
        $_GP=  $this->request;
        $default_name= $_GP['upload_name']?$_GP['upload_name']:'file';
        $uploadByAli= $_GP['uploadByAli']?$_GP['uploadByAli']:1;
        $pic_group= $_GP['pic_group']?$_GP['pic_group']:'';
        $type= $_GP['type']?$_GP['type']:'image';
       
             
        if( $_FILES[$default_name] ){
            $upload =  file_upload($_FILES[$default_name],$uploadByAli, $pic_group,$type) ;
            if (is_error($upload)) {
               ajaxReturnData(0,   $upload['message']);		//文件上传有错时
			}
            ajaxReturnData(1,'success',array('pic_url'=> $upload['path']));
        }else{
            ajaxReturnData(0,  LANG('请检查表单'));
        }
       
    }

}
