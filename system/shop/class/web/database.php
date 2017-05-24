<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/11/12
 * Time: 11:55
 */
$op = $_GP ['op'];

switch($op){
    case 'list' :
        $db      = new DatabaseBack();
        $tables  = $db->getTables();
        include page('database_list');
        break;

    case 'back':
        $db      = new DatabaseBack();
        if(!empty($_GP['sqlname'])){
            $table   = $_GP['sqlname'];
            $res     = $db->backup($table);
        }else{
            $res     = $db->backup();
        }
         echo $res;
        break;

    case 'huanyuan':
        //列出备份文件
        $path     = WEB_ROOT.'/databack';
        $datalist = '';
        if(is_dir($path)){
            $flag = \FilesystemIterator::KEY_AS_FILENAME;
            $glob = new \FilesystemIterator($path,  $flag);
            //20161112145639.sql
            //20161112145639_squsian_main.sql
            if(!empty($glob)){
                foreach ($glob as $name => $file) {
                    if(strpos($name,'_')){
                        $arr = explode('_',$name);
                    }else{
                        $arr = explode('.',$name);
                    }
                    $timecode = $arr['0'];
                    $timeArr = sscanf($timecode, '%4s%2s%2s%2s%2s%2s');

                    $date = "{$timeArr[0]}-{$timeArr[1]}-{$timeArr[2]}";
                    $time = "{$timeArr[3]}:{$timeArr[4]}:{$timeArr[5]}";
                    $datalist[] = array(
                        'name' => $name,
                        'size' => $file->getSize(),
                        'date' => $date,
                        'time' => $time
                    );
                }
            }

        }
        include page('database_huanyuan');
        break;

    case 'deldb' :
        $path = WEB_ROOT.'/databack/'. $_GP['dbname'];
        array_map("unlink", glob($path));
        if(count(glob($path))){
            die(showAjaxMess('1002','备份文件删除失败，请检查目录权限！'));
        }else{
            die(showAjaxMess('200','备份文件删除成功！'));
        }
        break;

    case 'import' :   //还原导入数据
        $sqlfile = $_GP['sqlfile'];
        if(empty($sqlfile))
            die(showAjaxMess('1002','参数有误！'));

        $db   = new DatabaseBack();
        $data = $db->import($sqlfile);
        ppd($data);
        break;
}