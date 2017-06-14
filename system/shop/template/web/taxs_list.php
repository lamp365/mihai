<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<body class="J_scroll_fixed">
    <h3 class="header smaller lighter blue">税率列表</h3>
    <div class="wrap jj">
        <div class="well form-search">
            
        <div class="table_list">
            <table width="100%" class="table table-striped table-bordered table-hover" id='data_table'>
                <thead id='table_head'>
                    <tr>
                        <th class="text-center" >编号</th>
                        <th width="70%" class="text-center" >商品类型</th>
                        <th class="text-center" >税率</th>
                        <th class="text-center" >操作</th>
                    </tr>
                </thead>
                <tbody id='table_body'>
                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        data = <?php  echo $list;?>;
        data = eval(data);
        // console.log(data);
        tr = '';
        for (var i = 0; i < data.length; i++) {
            td = '';
            td += '<td class="text-center">'+data[i]['id']+'</td>';
            td += '<td class="text-center">'+data[i]['type']+'</td>';
            percent = data[i]['tax']*100;
            if (percent.toString().split(".")[1] != null) {
                if (percent.toString().split(".")[1].length > 2) {
                    td += '<td class="text-center">'+percent.toFixed(2)+'%'+'</td>';
                }else{
                    td += '<td class="text-center">'+percent+'%'+'</td>';
                }
            }else{
                td += '<td class="text-center">'+percent+'%'+'</td>';
            }

            var e = new RegExp('myid',"g");
            change = "<td class='text-center'><a class='btn btn-xs btn-info' href='<?php echo web_url('taxs', array('op'=>'edit','id' => 'myid'))?>'><i class='icon-edit'></i>修改</a>&nbsp; <a class='btn btn-xs btn-danger' href='<?php  echo web_url('taxs', array('op'=>'delete','id' => 'myid'))?>' onclick='return confirm('此操作不可恢复，确认删除？');return false;'><i class='icon-edit'></i>删除</a></td>".replace(e, data[i]['id']);
            // console.log(change);
            td += change;
            
            tr+='<tr>'+td+'</tr>';
        };
        $("#table_body").append(tr);
    </script>
<?php  include page('footer');?>