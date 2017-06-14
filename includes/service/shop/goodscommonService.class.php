<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/18
 * Time: 11:29
 */
namespace service\shop;

class goodscommonService extends \service\publicService
{
    /**
     * 根据模型id  来获取对应的 属性
     * @param $gtype_id
     * @param int $goods_id
     * @return bool|string
     */
    public  function goodsAttrInput($gtype_id,$goods_id=0)
    {
        $attributeList = mysqld_selectall("select * from ".table('goodstype_attribute')." where gtype_id={$gtype_id}");
        if(empty($attributeList)){
            $this->error = '暂无对应属性！';
            return false;
        }
        $str = '';
        foreach($attributeList as $key => $val) {
            if(empty($goods_id)){
                $curAttrVal = array('goods_attr_id' =>'','goods_id' => '','attr_id' => '','attr_value' => '','attr_price' => '');
            }else{
                $curAttrVal = mysqld_select("select * from ".table('goods_attr')." where attr_id={$val['attr_id']} and goods_id={$goods_id}");
            }

            $str .= "<tr class='attr_{$val['attr_id']}'>";
            $str .= "<td>{$val['attr_name']}</td> <td>";

            if($goods_id){
                $put_name_key  = $curAttrVal['goods_attr_id'].'@'. $val['attr_id'];
            }else{
                $put_name_key  = $val['attr_id'];
            }
            // 手工录入
            if($val['attr_input_type'] == 0)
            {
                if($goods_id){
                    $str .= "<input type='text' size='40' value='".$curAttrVal['attr_value']."' name='attritem[{$put_name_key}][]'  class='form-control' />";
                }else{
                    $str .= "<input type='text' size='40' value='".$val['attr_values']."' name='attritem[{$put_name_key}][]'  class='form-control' />";
                }

            }
            // 从下面的列表中选择（一行代表一个可选值）
            if($val['attr_input_type'] == 1)
            {

                $tmp_option_val = explode(',', $val['attr_values']);
                foreach($tmp_option_val as $k2=>$v2)
                {
                    // 编辑的时候 有选中值  提交上去的表单 key  {goods_attr_id}@{attr_id}  用于直接编辑
                    $v2 = preg_replace("/\s/","",$v2);
                    if($curAttrVal['attr_value'] == $v2)
                        $str .= "<input type='checkbox' name='attritem[{$put_name_key}][]' checked value='{$v2}' /> {$v2}&nbsp;";
                    else
                        $str .= "<input type='checkbox' name='attritem[{$put_name_key}][]'   value='{$v2}' /> {$v2}&nbsp;";
                }
            }

            $str .= "</td></tr>";
        }

        return  $str;
    }


    /**
     * 根据模型来获取对应的规格
     * @param $gtype_id
     * @param int $goods_id
     * @return bool|string
     */
    public function goodsSpecInput($gtype_id,$goods_id=0)
    {
        $specList = mysqld_selectall("select * from ".table('goodstype_spec')." where gtype_id={$gtype_id}");
        if(empty($specList)){
            $this->error = '暂无对应规格！';
            return false;
        }
        foreach($specList as $k => $v)
            $specList[$k]['spec_item'] = mysqld_selectall("select id,item_name from ".table('goodstype_spec_item')." where spec_id={$v['spec_id']} and status =1"); // 获取规格项

        $items_ids = array();
        if(!empty($goods_id)){
            $items_id  = mysqld_select("select GROUP_CONCAT(`spec_key` SEPARATOR '_') AS items_id from ".table('goods_spec_price')." where goods_id={$goods_id}");
            $items_ids = explode('_', $items_id['items_id']);
        }

        $html = '';
        foreach($specList as $k => $vo){
            $button = '';
            foreach($vo['spec_item'] as  $one_item){
                if(in_array($one_item['id'],$items_ids)){
                    $style = "btn-success";
                }else{
                    $style = 'btn-default';
                }
                $button .= " <button type='button' data-spec_id='{$vo['spec_id']}' data-item_id='{$one_item['id']}' class='btn btn-xs {$style}'>{$one_item['item_name']}</button>&nbsp;&nbsp;";
            }
            $html .= "<tr>
                        <td>{$vo['spec_name']}</td>
                        <td> {$button} </td>
                     </tr>";
        }

        return $html;
    }


    /**
     * 根据用户点击的规格得到具体的规格项
     * @param $spec_arr
     * @param $goods_id
     * @return string
     */
    public function goodsSpecInput_info($spec_arr,$goods_id)
    {
        // <input name="item[2_4_7][price]" value="100" /><input name="item[2_4_7][name]" value="蓝色_S_长袖" />
        /*$spec_arr = array(
            20 => array('7','8','9'),
            10=>array('1','2'),
            1 => array('3','4'),

        );  */
        // 排序  规则比较多的优先排  为了实现卡迪尔排序
        $spec_item_id_arr = array();
        foreach ($spec_arr as $k => $v) {
            $spec_arr_sort[$k] = count($v);
            $spec_item_id_arr  = array_merge($spec_item_id_arr,$v);
        }
        asort($spec_arr_sort);
        foreach ($spec_arr_sort as $key =>$val) {
            $spec_arr2[$key] = $spec_arr[$key];
        }
        $spec_id_arr          = array_keys($spec_arr2);

        $spec_item_table_list = combineDika($spec_arr2); //  获取 规格的 笛卡尔积
        //变成
        /**array(
            array('3','1','7'),
            array('3','1',8),
            array('3','1',9),
            array('3','2',7),
            array('3','2',8),
            array('3','2',9),
            array('4','1','7'),
            array('4','1',8),
            array('4','1',9), ~~~~~~~~
        ) **/
        //获取当前的 产品对应的 具体规格项的值
        $curentSpecPrice_speckey = $curentSpecPrice_idkey = array();
        if(!empty($goods_id)){
            $curentSpecPrice = mysqld_selectall("select * from ".table('goods_spec_price')." where goods_id={$goods_id}");
            foreach($curentSpecPrice as $item){
                $curentSpecPrice_speckey[$item['spec_key']]    = $item;
            }
        }

        $html = $tbody = '';
        $tr   = '<tr>';
        $speclist = array();
        foreach($spec_id_arr as $spec_id){
            $one_spec           = mysqld_select("select spec_id,spec_name from ".table('goodstype_spec')." where spec_id={$spec_id}"); // 规格表
            $speclist[$spec_id] = $one_spec['spec_name'];
            $tr .="<td><b>{$one_spec['spec_name']}</b></td>";    //规格名字
        }
        $tr .= "<td><b>价格</b></td>
                <td><b>库存</b></td>
                <td><b>SKU</b></td>
             </tr>";


        // 显示第二行开始
        $itemlist = array();
        foreach($spec_item_id_arr as $item_id){
            $one_item   = mysqld_select("select id,item_name,spec_id from ".table('goodstype_spec_item')." where id={$item_id} and status=1"); // 规格对应的项
            $itemlist[$item_id] = $one_item;   //具体对应的规格的项
        }

        foreach($spec_item_table_list as $item){
            $item_key_name = array();
            $tbody .= "<tr>";
            foreach($item as $item_id){
                $tbody .= "<td>{$itemlist[$item_id]['item_name']}</td>";

                $spec_id = $itemlist[$item_id]['spec_id'];
                $item_key_name[$item_id] = $speclist[$spec_id].':'.$itemlist[$item_id]['item_name'];
            }

            sort($item);
            $item_key  = implode('_', $item);
            $item_name = implode('@@', $item_key_name);

            $val_price = $val_store_count = $val_key_name = $val_sku = '';
            if(array_key_exists($item_key,$curentSpecPrice_speckey)){
                $val_price       = FormatMoney($curentSpecPrice_speckey[$item_key]['marketprice'],0);
                $val_store_count = $curentSpecPrice_speckey[$item_key]['store_count'];
                $val_sku         = $curentSpecPrice_speckey[$item_key]['sku'];
                $val_key_name    = $curentSpecPrice_speckey[$item_key]['key_name'];
            }
            $tbody .= "<td><input type='number' name='specitem[{$item_key}][marketprice]' class='form-control' value='{$val_price}'/></td>";
            $tbody .= "<td><input  type='number' name='specitem[$item_key][store_count]' class='form-control' value='{$val_store_count}'/></td>";
            $tbody .= "<td><input  type='number' name='specitem[$item_key][sku]' class='form-control' value='{$val_sku}'/>";
            $tbody .= " <input type='hidden' name='specitem[{$item_key}][key_name]' value='{$item_name}' /></td>";

            $tbody .= "</tr>";
        }
        $html = $tr . $tbody;
        return $html;

    }

}