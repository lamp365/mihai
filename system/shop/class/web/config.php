<?php
namespace shop\controller;

class config extends \common\controller\basecontroller
{
    public function index()
    {
        $_GP = $this->request;
        $settings=globaSetting();

        if (checksubmit("submit")) {

            $cfg = array(
                'shop_openreg' => intval($_GP['shop_openreg']),
                'shop_regcredit' => intval($_GP['shop_regcredit']),
                'shop_keyword' => $_GP['shop_keyword'],
                'shop_description' => $_GP['shop_description'],
                'shop_title' => $_GP['shop_title'],
                'shop_icp' => $_GP['shop_icp'],
                'shop_tel'=>$_GP['shop_tel'],
                'shop_address'=>$_GP['shop_address'],
                'shop_kfcode' => htmlspecialchars_decode($_GP['shop_kfcode']),
                'shop_tongjicode' => htmlspecialchars_decode($_GP['shop_tongjicode']),
                'help' =>   htmlspecialchars_decode($_GP['help'])
            );

            if (!empty($_FILES['shop_logo']['tmp_name'])) {
                $upload = file_upload($_FILES['shop_logo']);
                if (is_error($upload)) {
                    message($upload['message'], '', 'error');
                }
                $shoplogo = $upload['path'];
            }
            if(!empty($shoplogo)) {
                $cfg['shop_logo']=$shoplogo;
            }

            refreshSetting($cfg);
            message('保存成功', 'refresh', 'success');
        }

        $qq_info = '';
        if(!empty($settings['shop_kfcode'])){
            $qq_info = json_decode($settings['shop_kfcode'],true);
        }
        include page('setting/setting');
    }

    public function otherSet()
    {
        $_GP = $this->request;
        if(checksubmit('doadd')){
            $com_gold = $_GP['com_gold'];
            $com_gold = (float)$com_gold/100;
            $credit_ratio = $_GP['credit_ratio'];
            $com_credit   = $_GP['com_credit'];
            $teller_limit = $_GP['teller_limit'];

            $cfg = array(
                'com_gold'        => $com_gold,
                'credit_ratio'    => $credit_ratio,
                'com_credit'      => $com_credit,
                'teller_limit'    => $teller_limit,
            );
            refreshSetting($cfg);
            message("设置成功！",refresh());
        }
        $sett = globaSetting();
        include page('setting/other_setting');
    }
}
