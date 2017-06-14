<?php
namespace shopwap\controller;

class miyou extends \common\controller\basecontroller
{
	public function index()
	{
		set_time_limit(300);
		$member = array(
			'2017060116264',
			'2017060209737',
			'2017060116265',
			'2017060116266',
			'2017060209738',
		);
		$brand = mysqld_selectall("select * from ".table('shop_brand'));
		$brand_data = array();
		foreach($brand as $one){
			$goods = mysqld_selectall("select * from ".table('shop_goods')." where brand={$one['id']}");
			$brand_data[$one['id']] = $goods;
		}

		foreach($brand_data as $bid=>$item){
			$openid_key = $bid%4;
			$openid = $member[$openid_key];
			//创建店铺
//			$this->create_store($openid,$bid);
		}
	}

	public function create_store($openid,$bid)
	{
		$brand = mysqld_select("select brand from ".table('shop_brand')." where id={$bid}");
		$info  = mysqld_select ( "SELECT time_range FROM " . table ( 'store_shop_level' ) . " where rank_level=5");
		$store_shop['sts_shop_level']       = 5;
		$store_shop['sts_level_valid_time'] = strtotime("+".$info['time_range']." year");//防止閏年等問題
		$store_shop['sts_shenhe_time']      = time();
		$store_shop['sts_openid'] 	        = $openid;
		$store_shop['sts_info_status'] 	    = 0;
		$store_shop['sts_shop_type'] 	    = 3;
		$store_shop['sts_category_p1_id'] 	= 6;
		$store_shop['sts_category_p2_id'] 	= 8;
		$store_shop['sts_name'] 			= $brand['brand'];
		$store_shop['sts_physical_shop_name']= $brand['brand'];
		$store_shop['sts_contact_name']      = '小城市2964';
		$store_shop['sts_province']      	= '350000';
		$store_shop['sts_city']      		= '350200';
		$store_shop['sts_region']      		= '350206';
		$store_shop['sts_address']      	= '东街街道安福新村(福州市碧玉花园西)碧玉花园';
		$store_shop['sts_lat']            	= '26.083643';
		$store_shop['sts_lng']          	= '119.302535';
		$store_shop['sts_locate_add_1']     = '350000';
		$store_shop['sts_locate_add_2']     = '350200';
		$store_shop['sts_locate_add_3']     = '350206';
		$store_shop['sts_register_time']    = time();
		$store_shop['sts_creatime']    		= time();

		mysqld_insert('store_shop',$store_shop);
		$sts_id = mysqld_insertid();

		$identy_data['ssi_id']         = $sts_id;
		$identy_data['ssi_owner_name'] = '小城市2964';
		$identy_data['ssi_owner_shenfenhao'] = cbd_encrypt('350321198906152212',$openid);
		$identy_data['ssi_shenfenzheng'] = 'http://hinrc.oss-cn-shanghai.aliyuncs.com/201705/201705021519590832f0eb91b.jpg';
		$identy_data['ssi_yingyezhizhao'] = 'http://hinrc.oss-cn-shanghai.aliyuncs.com/201705/201705021519590832f0eb91b.jpg';
		$identy_data['ssi_xukezheng'] = 'http://hinrc.oss-cn-shanghai.aliyuncs.com/201705/201705021519590832f0eb91b.jpg';
		$identy_data['ssi_dianmian'] = 'http://hinrc.oss-cn-shanghai.aliyuncs.com/201705/201705021519590832f0eb91b.jpg';
		$identy_data['ssi_diannei'] = 'http://hinrc.oss-cn-shanghai.aliyuncs.com/201705/201705021519590832f0eb91b.jpg';
		mysqld_insert('store_shop_identity',$identy_data);

		//更新当前会员类型为商家类型
		mysqld_update("member",array('member_type'=>2),array('openid'=>$openid));

		//插入当前用户跟 店铺的关系
		$relat_data = array(
			'sts_id' => $sts_id,
			'openid' => $openid,
			'is_admin'   => 1,
			'createtime' => time(),
		);
		//查找是否 关系表 有其他表 有的话，该店铺不设置为默认，没有的话，该店铺设置为默认。
		$find = mysqld_select ( "SELECT * FROM " . table ( 'member_store_relation' ) . " where  openid='{$openid}'");
		if(empty($find)){
			$relat_data['is_default'] = 1;
		}
		mysqld_insert('member_store_relation',$relat_data);
	}
}