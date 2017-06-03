<?php

		$op= $operation = $_GP['op']?$_GP['op']:'display';
		$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
        if ($operation == 'display') {
            if (!empty($_GP['displayorder'])) {
                foreach ($_GP['displayorder'] as $id => $displayorder) {
                    mysqld_update('dish_list', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', web_url('disharea', array('op' => 'display')), 'success');
            }
            $children = array();
            $disharea = mysqld_selectall("SELECT * FROM " . table('dish_list') . "  where deleted=0  ORDER BY parentid ASC, displayorder DESC");
            foreach ($disharea as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($disharea[$index]);
                }
            }
            include page('yunfei/disharea_list');
        } elseif ($operation == 'post') {
            $parentid = intval($_GP['parentid']);
            $id = intval($_GP['id']);
            if (!empty($id)) {
                $disharea = mysqld_select("SELECT * FROM " . table('dish_list') . " WHERE id = '$id'");
            } else {
                $disharea = array(
                    'displayorder' => 0,
                );
            }
            if (!empty($parentid)) {
                $parent = mysqld_select("SELECT id, name FROM " . table('dish_list') . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', web_url('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GP['catename']) || empty($_GP['kuaidi'])) {
                    message('仓库名称，快递名称不能为空！');
                }
                $data = array(
                    'name' => $_GP['catename'],
                    'kuaidi' => $_GP['kuaidi'],
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
                    mysqld_update('dish_list', $data, array('id' => $id));
                } else {
                    mysqld_insert('dish_list', $data);
                    $id = mysqld_insertid();
                }
                message('更新成功！', web_url('disharea', array('op' => 'display')), 'success');
            }
            include page('yunfei/disharea');
        } elseif ($operation == 'delete') {
            $id = intval($_GP['id']);
            $disharea = mysqld_select("SELECT id, parentid FROM " . table('dish_list') . " WHERE id = '$id' and deleted=0 ");
            if (empty($disharea)) {
                message('抱歉，不存在或是已经被删除！', web_url('disharea', array('op' => 'display')), 'error');
            }
            mysqld_update('dish_list', array('deleted' => 1), array('id' => $id, 'parentid' => $id), 'OR');
            message('删除成功！', web_url('disharea', array('op' => 'display')), 'success');
        }
