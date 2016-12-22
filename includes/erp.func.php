<?php
/*
ERP操作方法
*/

/*基本参数定义*/
define("ERP_APPKEY","U1CITYCKTEST");
define("ERP_APPSECRET","U1CITYCKTESTJJKIUNHB");
define("ERP_POSTURL","http://wqbopenapi.ushopn6.com/wqbbatch/api.rest");

/**
 * 查询商品类别
 * 
 * @return array 接口返回
 *  <Code>101</Code>  // 101：成功，102：失败，103：系统异常，104：找不到相关数据
 *  <Message>操作成功</Message>  // 返回接口状态
 *  <Result>  // 商品类别数据节点(Code节点返回101时，才有该节点的值)
 *  <Api_Pro_Class>
 *        <ClassId>类别编号</ClassId>
 *        <ClassName>类别名称</ClassName> 
 *        <Class_N>1</Class_N>  // 1：大类 2：小类
 *        <Class_N_Id>1</Class_N_Id>  // 大类ID，与<ClassId>对应，如果是大类<Class_N_Id>=<ClassId>
 *  </Api_Pro_Class>
 *  </Result>
 */
function GetProClass() {
    $fun_name = __FUNCTION__;

    $data_ary = array();
    
    return run_link($fun_name, $data_ary);
}

/**
 * 查询商品信息
 * 
 * @param $proNo string 商品货号
 * @param $proClassCode string  商品类别
 * @param $proSale string 商品状态
 * @param $IsBatch string  是否批次管理
 * @param $IsGuarantee string 是否保质期管理
 * @param $startTime string  商品添加时间
 * @param $endTime string 商品添加时间
 * @param $pageIndex string  页数
 * @param $pageSize string  每页数量
 * 
 * @return array 接口返回
 *  <Code>101</Code>  // 101：成功，102：失败，103：系统异常，104：找不到相关数据
 *  <Message>操作成功</Message>  // 返回接口状态
 *  <SumNum>商品数量</SumNum>  // Code节点返回101时，才有该节点
 *  <Result>  // 商品信息数据节点(Code节点返回101时，才有该节点的值)
 *  <Api_ProductInfo>
 *        <ProId>商品编号</ProId>
 *        <ProTitle>商品标题</ProTitle>
 *        <ProNo>商品货号</ProNo>
 *        <Barcode>商品条形码</Barcode>
 *        <ProBrand>商品品牌</ProBrand>
 *        <ProClass>商品类别</ProClass>
 *        <ProShux>商品属性</ProShux>
 *        <ProWeight>商品重量</ProWeight>
 *        <ProSimg>商品图片</ProSimg>
 *        <ProRemark>商品说明</ProRemark>
 *        <ProSale>商品状态</ProSale>  // 0：销售中，1：下架，2：全部
 *        <Pro_Unit>商品单位</Pro_Unit>
 *        <ProTagPrice>市场价</ProTagPrice>
 *        <ProFxPrice>分销价</ProFxPrice>
 *        <ProRetPrice>网店销售价</ProRetPrice>
 *        <ProAddTime>商品创建时间</ProAddTime>
 *        <IsBatch>是否批次管理商品</IsBatch>// 0：非批次管理商品 1：批次管理商品
 *        <IsGuarantee>是否保质期商品</IsGuarantee>// 0：非保质期管理1：保质期管理
 *        <GPeriodNum>商品保质期</GPeriodNum>
 *        <GPeriod_Warn>商品预警天数</GPeriod_Warn>
 *        <Rates>关税税率</Rates> 
 *        <AddedValueRates>增值税税率</AddedValueRates> 
 *        <ConsumptionRates>消费税税率</ConsumptionRates> 
 *        <ProductSpec>  // 商品SKU信息列表
 *              <Api_ProductSkuInfo>
 *                     <ProColorName>商品颜色</ProColorName>
 *                    <ProSizesName>商品规格</ProSizesName>
 *                    <ProSkuNo>商品Sku</ProSkuNo>
 *                    <SkuBarcode>商品Sku条形码</SkuBarcode>
 *                    <Weight>商品重量</Weight>
 *              </Api_ProductSkuInfo>
 *              <Api_ProductSkuInfo>
 *                     <ProColorName>商品颜色</ProColorName>
 *                    <ProSizesName>商品规格</ProSizesName>
 *                    <ProSkuNo>商品Sku</ProSkuNo>
 *                    <SkuBarcode>商品Sku条形码</SkuBarcode>
 *                    <Weight>商品重量</Weight>
 *              </Api_ProductSkuInfo>
 *        </ProductSpec>
 *  </Api_ProductInfo>
 *  </Result>
 */
function GetProducts($proNo='',$proClassCode='',$proSale='',$IsBatch='',$IsGuarantee='',$startTime='',$endTime='',$pageIndex='',$pageSize='') {
    $fun_name = __FUNCTION__;

    $data_ary = array(
       "proNo"=>$proNo,
       "proClassCode"=>$proClassCode,
       "proSale"=>$proSale,
       "IsBatch"=>$IsBatch,
       "IsGuarantee"=>$IsGuarantee,
       "startTime"=>$startTime,
       "endTime"=>$endTime,
       );
    if (!empty($pageIndex)) {
        $data_ary['pageIndex'] = $pageIndex;
    }
    if (!empty($pageSize)) {
        $data_ary['pageSize'] = $pageSize;
    }
    
    return run_link($fun_name, $data_ary);
}

/**
 * 查询商品库存
 * 
 * @param $proSkuNo string 商品Sku(商品货号\条形码)
 *
 * @return array 接口返回
 *  <Code>101</Code>  // 101：成功，102：失败，103：系统异常
 *  <Message>操作成功</Message>  // 返回接口状态
 *  <Result>  // 商品库存(Code节点返回101时，才有该节点的值)
 *  <Api_Pro_Sku_Info>
 *        <ProTitle>商品标题</ProTitle>
 *        <ProNo>商品货号</ProNo> 
 *        <ProColorName>商品颜色名称</ProColorName>
 *        <ProSizesName>商品规格名称</ProSizesName>
 *        <ProSkuNo>商品Sku</ProSkuNo>
 *        <ProCount>商品Sku库存数量</ProCount>
 *        <BatchKuc>
 *          <BatchKucInfo>//注：只有对接仓库端的时候才有这个节点 
 *           <PBCId>商品批次Id</PBCId>
 *           <BatchNo>商品批次号Id</BatchNo>
 *           <Quantity>商品可发货数量</Quantity>
 *           <KwZyQuantity>商品库位占用数量</KwZyQuantity>
 *           <ZkQuantity>商品库位总库存数量</ZkQuantity>
 *          </BatchKucInfo>
 *          <BatchKucInfo>//注：只有对接仓库端的时候才有这个节点 
 *           <PBCId>商品批次Id</PBCId>
 *           <BatchNo>商品批次号Id</BatchNo>
 *           <Quantity>商品可发货数量</Quantity>
 *           <KwZyQuantity>商品库位占用数量</KwZyQuantity>
 *           <ZkQuantity>商品库位总库存数量</ZkQuantity>
 *          </BatchKucInfo>
 *        </BatchKuc>
 *  </Api_Pro_Sku_Info>
 *  </Result>
 */
function GetProductSkuInfo($proSkuNo) {
    $fun_name = __FUNCTION__;

    $data_ary = array(
       "proSkuNo"=>$proSkuNo,
       );

    return run_link($fun_name, $data_ary);
}

/**
 * 更新商品库存信息
 * 
 * @param $proSkuNo string 商品Sku
 * @param $proNum string 库存数量
 * @param $batchCode string  批次号(商品有开启批次管理，该字段必传[由数字和字母组成，长度不超过20个字符]；商品未开启批次管理，该字段只能传'混批'，或不传默认混批)
 *
 * @return array 接口返回
    <Code>101</Code>  //  101：成功，102：失败，103：系统异常
    <Message>更新成功</Message>  //  返回接口状态
 */
function UpdateProductSkuNum($proSkuNo, $proNum, $batchCode='') {
    $fun_name = __FUNCTION__;

    $data_ary = array(
       "proSkuNo"=>$proSkuNo,
       "proNum"=>$proNum,
       );
    if (!empty($batchCode)) {
        $data_ary['batchCode'] = $batchCode;
    }

    return run_link($fun_name, $data_ary);
}

/**
 * 更新商品库存信息（增量与减量）
 * 
 * @param $proSkuNo string 商品Sku
 * @param $proNum string  库存数量
 * @param $oType string  更新方式(类型[0：增量(默认) 1：减量])
 * @param $batchCode string  批次号(商品有开启批次管理，该字段必传[由数字和字母组成，长度不超过20个字符]；商品未开启批次管理，该字段只能传'混批'，或不传默认混批)
 * @param $orderNo string  外部调整单号（非必填项，值：不能重复）
 *
 * @return array 接口返回
    <Code>101</Code>  //  101：成功，102：失败，103：系统异常
    <Message>更新成功</Message>  //  返回接口状态
    <Result>系统调整单号</Result>  //  返回系统调整单号
 */
function UpdateProSkuInventory($proSkuNo, $proNum, $oType='0', $orderNo='', $batchCode='') {
    $fun_name = __FUNCTION__;

    $data_ary = array(
       "proSkuNo"=>$proSkuNo,
       "proNum"=>$proNum,
       "oType"=>$oType,
       );
    if (!empty($batchCode)) {
        $data_ary['batchCode'] = $batchCode;
    }
    if (!empty($orderNo)) {
        $data_ary['orderNo'] = $orderNo;
    }

    return run_link($fun_name, $data_ary);
}

/**
 * 批量更新商品库存信息（增量与减量）
 * 
 * @param $proSku array 商品Sku(例：array(array('OType'=>OType,'ProNum'=>ProNum,'SkuNo'=>SkuNo,'batchCode'=>batchCode(非必需),'mfgDate'=>mfgDate(批次号生产日期,非必需)),array))
 * @param $orderNo string  外部调整单号（非必填项，值：不能重复）
 *
 * @return array 接口返回
    <Code>101</Code>  //  101：成功，102：失败，103：系统异常
    <Message>更新成功</Message>  //  返回接口状态
    <Result>系统调整单号</Result>  //  返回系统调整单号
 */
function BatchUpdateProSkuInventory($proSku, $orderNo='') {
    $fun_name = __FUNCTION__;
    $data_ary = array(
       "proSku"=>json_encode($proSku),
       );
    if (!empty($orderNo)) {
        $data_ary['orderNo'] = $orderNo;
    }

    return run_link($fun_name, $data_ary);
}

/**
 * 批次查询
 * 
 * @param $batchCode string 批次号
 * @param $startTime string 批次添加时间
 * @param $endTime string 批次结束时间
 * @param $pageIndex string 页数
 * @param $pageSize string 每页数量
 *
 * @return array 接口返回
    <Code>101</Code>  // 101：成功，102：失败，103：系统异常，104：找不到相关数据
    <Message>查询成功</Message>  // 返回接口状态
    <SumNum>批次号数量</SumNum>  // Code节点返回101时，才有该节点
    <Result>  // 批次信息数据节点(Code节点返回101时，才有该节点的值)
    <Api_ProBatchCodeInfo>
          <Id>批次ID</Id>
          <BatchCode>批次号<BatchCode>
          <MfgDate>生产时间<MfgDate>
          <DueDate>到期时间<DueDate>
    </Api_ProBatchCodeInfo>
    </Result>
 */
function GetProBatchNo($batchCode='', $startTime='', $endTime='', $pageIndex='', $pageSize='') {
    $fun_name = __FUNCTION__;

    $data_ary = array(
       "batchCode"=>$batchCode,
       "startTime"=>$startTime,
       "endTime"=>$endTime,
       );
    if (!empty($pageIndex)) {
        $data_ary['pageIndex'] = $pageIndex;
    }
    if (!empty($pageSize)) {
        $data_ary['pageSize'] = $pageSize;
    }

    return run_link($fun_name, $data_ary);
}

/**
 * 加密链接操作
 * 
 * @param $method 接口名
 * @param $data  接口参数
 *
 * @return array 接口返回
 */
function run_link($method, $data) {
    if (empty($method)) {
        return false;
    }
    $apiFormat = "json";
    $appKey = ERP_APPKEY;
    $appSecret = ERP_APPSECRET;
    $apiMethod ="IOpenAPI.".$method;
    $postData = postdata_filed($data);//返回加密数据格式
    $inputStr = strtolower(str_replace(" ","",$appSecret.$apiMethod."appKey".$appKey.$postData));//转小写  
    $sort = mbstringtoarray($inputStr,'utf-8');
    sort($sort,SORT_STRING); 
    $str = "";
    for($i=0;$i<count($sort);$i++) {
     $str.=$sort[$i];
    }
    $paramStr = $str;
    $sToken = md5(iconv('utf-8',"utf-8",$paramStr));
    $post_data_filed = postdata_filed($data, "=");//返回POST参数格式
    $postData = "user=".$appKey."&method=".$apiMethod."&token=".$sToken."&format=".$apiFormat."&appKey=".$appKey.$post_data_filed;
    $postUrl = ERP_POSTURL;
    $response = phpCurlPost($postUrl,$postData); 
    return $response;
}

// 入参处理
function postdata_filed($array, $type="") {
    $data = "";
    if($type == "=") {
        foreach($array as $key=>$value) {
            $data.="&".$key."=".$value;
        }
    }else{
        foreach($array as $key=>$value) {
            $data.=$key.$value;
        }   
    }
    return $data;
}

//分割字符
function mbstringtoarray($str,$charset) {
    $strlen=mb_strlen($str);
    while($strlen) {
        $array[]=mb_substr($str,0,1,$charset);
        $str=mb_substr($str,1,$strlen,$charset);
        $strlen=mb_strlen($str);
    }
    return $array;
}

//post 提交
function phpCurlPost($url,$postStr = "") {
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postStr);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($curl);
    $result = json_decode($response, true);
    $w_num = 3;
    while (empty($result) && $result['Code']!='101' && $w_num>0) {
        $response = curl_exec($curl);
        $result = json_decode($response, true);
        $w_num -= 1;
    }
    curl_close($curl);

    return $result;
}