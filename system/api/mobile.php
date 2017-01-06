<?php
defined('SYSTEM_IN') or exit('Access Denied');

require WEB_ROOT . '/system/member/lib/rank.php';

class apiAddons extends BjSystemModule
{
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		//不是debug模式下
		if(!API_DEBUG)
		{
			$objRsa 		= new Rsa();			//RSA 加解密已签名验证相关类对象
			$url_parts 		= parse_url($_SERVER['REQUEST_URI']);
			$url_filename 	= pathinfo($url_parts['path']);
			$url_filename 	= $url_filename['filename'];
			
			//RSA加密的token为空时
			if(empty($_REQUEST['token']))
			{
				$result['message']	= '访问请求不被允许';
				$result['code'] 	= 0;
			
				echo apiReturn($result);
				exit;
			}
			//签名串为空时
			elseif(empty($_REQUEST['sign']))
			{
				$result['message']	= '访问请求不被允许';
				$result['code'] 	= 0;
					
				echo apiReturn($result);
				exit;
			}
			else{
				//待签名数据
				$signToken = $objRsa->public_decrypt(trim($_REQUEST['token']));
					
				if(empty($signToken))
				{
					$result['message']	= '访问请求不被允许';
					$result['code'] 	= 0;
						
					echo apiReturn($result);
					exit;
				}
				elseif($signToken!=$url_filename)
				{
					$result['message']	= '访问请求不被允许';
					$result['code'] 	= 0;
					
					echo apiReturn($result);
					exit;
				}
				//签名验证不通过时
				elseif(!$objRsa->getSignVerify($signToken,$_REQUEST['sign']))
				{
					$result['message']	= '访问请求不被允许';
					$result['code'] 	= 0;
						
					echo apiReturn($result);
					exit;
				}
			}
		}
	}
	
	
	public function do_register()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_send_sms_code()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_reset_password()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_member_info()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_category()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_identity()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_dish_list()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_commodity_details()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_address()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_shopping_cart()
	{
		$this->__mobile(__FUNCTION__);
	}
	public function do_login()
	{
		$this->__mobile(__FUNCTION__);
	}
	public function do_banner()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_guess_like()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_my_order()
	{
		$this->__mobile(__FUNCTION__);
	}
	
	public function do_sale_dish_list()
	{
		$this->__mobile(__FUNCTION__);
	}

    public function do_order_detail()
    {
        $this->__mobile(__FUNCTION__);
    }

    public function do_order_operation()
    {
        $this->__mobile(__FUNCTION__);
    }
	
    public function do_express()
    {
        $this->__mobile(__FUNCTION__);
    }
	
	public function do_comment()
    {
        $this->__mobile(__FUNCTION__);
    }

    public function do_wallet()
    {
        $this->__mobile(__FUNCTION__);
    }
	
    public function do_confirm()
    {
    	$this->__mobile(__FUNCTION__);
    }
	
    public function do_place_order()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_collection()
    {
        $this->__mobile(__FUNCTION__);
    }

    public function do_browsing_history()
    {
        $this->__mobile(__FUNCTION__);
    }

    public function do_praise()
    {
        $this->__mobile(__FUNCTION__);
    }
    
    public function do_hottopic()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_auto_keyword()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_search()
    {
    	$this->__mobile(__FUNCTION__);
    }

    public function do_group_details()
    {
        $this->__mobile(__FUNCTION__);
    }
    
    public function do_systime()
    {
    	$this->__mobile(__FUNCTION__);
    }

    public function do_shop()
    {
        $this->__mobile(__FUNCTION__);
    }
	
	 public function do_im()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_aftersales()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_payment_order()
    {
    	$this->__mobile(__FUNCTION__);
    }

    public function do_bonus()
    {
        $this->__mobile(__FUNCTION__);
    }

    public function do_feedback()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_app_version()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_password()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_mcache()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_note()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_note_comment()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_note_collection()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_follow()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_headline()
    {
    	$this->__mobile(__FUNCTION__);
    }
	
    public function do_headline_collection()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_headline_comment()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_followed_info()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_article()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_article_comment()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_article_collection()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_cate_dish_list()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_app_video()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_member_list()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_third_login()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_account_bind()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_account_unbind()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_account_rebind()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_change_mobile()
    {
    	$this->__mobile(__FUNCTION__);
    }
    
    public function do_payment_type()
    {
    	$this->__mobile(__FUNCTION__);
    }
	
    public function getPaytypebycode($code)
    {
    	$paytype = 2;
    	if ($code == 'delivery') {
    		$paytype = 3;
    	}
    	if ($code == 'gold') {
    		$paytype = 1;
    	}
    	return $paytype;
    }
}


