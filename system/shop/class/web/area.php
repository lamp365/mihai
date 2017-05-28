<?php

		$op= $operation = $_GP['op']?$_GP['op']:'display';
		$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
        if ($operation == 'display') {
            if (!empty($_GP['displayorder'])) {
                foreach ($_GP['displayorder'] as $id => $displayorder) {
                    mysqld_update('mess_list', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', web_url('area', array('op' => 'display')), 'success');
            }
            $children = array();
            $area = mysqld_selectall("SELECT * FROM " . table('mess_list') . "  where deleted=0  ORDER BY parentid ASC, displayorder DESC");
            foreach ($area as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($area[$index]);
                }
            }
            include page('area_list');
        } elseif ($operation == 'post') {
            $parentid = intval($_GP['parentid']);
            $id = intval($_GP['id']);
            if (!empty($id)) {
                $area = mysqld_select("SELECT * FROM " . table('mess_list') . " WHERE id = '$id'");
            } else {
                $area = array(
                    'displayorder' => 0,
                );
            }
            if (!empty($parentid)) {
                $parent = mysqld_select("SELECT id, name FROM " . table('mess_list') . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', web_url('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GP['catename'])) {
                    message('抱歉，请输入分类名称！');
                }
                $data = array(
                    'name' => $_GP['catename'],
                    'enabled' => intval($_GP['enabled']),
                    'displayorder' => intval($_GP['displayorder']),
                    'isrecommand' => intval($_GP['isrecommand']),
                    'description' => $_GP['description'],
                    'parentid' => intval($parentid),
                );
                if (!empty($_GP['thumb_del'])) {
                	$data['thumb'] = '';
                }
                if (!empty($_FILES['thumb']['tmp_name'])) {
                    file_delete($_GP['thumb_old']);
                    $upload = file_upload($_FILES['thumb']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['thumb'] = $upload['path'];
                }
             
                if (!empty($id)) {
                    unset($data['parentid']);
                    mysqld_update('mess_list', $data, array('id' => $id));
                } else {
                    mysqld_insert('mess_list', $data);
                    $id = mysqld_insertid();
                }
                message('更新分类成功！', web_url('area', array('op' => 'display')), 'success');
            }
            include page('area');
        } elseif ($operation == 'delete') {
            $id = intval($_GP['id']);
            $area = mysqld_select("SELECT id, parentid FROM " . table('mess_list') . " WHERE id = '$id' and deleted=0 ");
            if (empty($area)) {
                message('抱歉，分类不存在或是已经被删除！', web_url('area', array('op' => 'display')), 'error');
            }
            mysqld_update('mess_list', array('deleted' => 1), array('id' => $id, 'parentid' => $id), 'OR');
            message('分类删除成功！', web_url('area', array('op' => 'display')), 'success');
        }
