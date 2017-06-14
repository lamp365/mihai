<?php
if ($_GP['op'] == 'vote') {
    if (isset($_GP['pid'])) {
        
        $vote = mysqld_select("SELECT * FROM " . table('vote') . " where datediff(createtime,now())=0 ");
        $pid = $_GP['pid'];
        if (is_array($vote) && count($vote) > 0) {
            echo json_encode(array(
                "result" => "faild"
            ));
        } else {
            $member = get_member_account(false);
            $member = member_get($member['openid']);
            if (empty($member['openid'])) {
                $member = get_member_account(false);
                $member['createtime'] = time();
            }
            
            $is_login = is_login_account();
            
            if ($is_login) {
                $ctime = date("Y-m-d h:i:s", time());
                $data = array(
                    "openid" => $member['openid'],
                    "pid" => $_GP['pid'],
                    "createtime" => $ctime
                );
                $result = mysqld_insert('vote', $data);
                $votenum = mysqld_select("SELECT * FROM " . table('user_advise') . " WHERE deleted=0 and id = :id", array(
                    ':id' => $pid
                ));               
                if (is_array($votenum) & count($votenum) > 0) {                  
                    $upresult=mysqld_update('user_advise', array("vote" => $votenum['vote'] + 1), array("id" => $pid));                  
                }
                if (isset($result)) {
                    echo json_encode(array(
                        "result" => "success"
                    ));
                } else {
                    echo json_encode(array(
                        "result" => "faild"
                    ));
                }
            } else {
                echo json_encode(array(
                    "result" => "nologin"
                ));
            }
        }
    }
    exit();
}

$pindex = max(1, intval($_GP["page"]));
$psize = 1;
$condition = '';
$sortfield = " ";

$list = mysqld_selectall("SELECT * FROM " . table('user_advise').' where deleted=0 and thumb is not null');
$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('user_advise') . " where deleted=0 and thumb is not null ");
foreach ( $list as $key=>$value ) {
    $list[$key]['thumb'] = imgThumb($value['thumb'],156,156);
}
$pager = pagination($total, $pindex, $psize, $url = '', $context = array(
    'before' => 0,
    'after' => 0,
    'ajaxcallback' => ''
));

include themePage('dishedlist');