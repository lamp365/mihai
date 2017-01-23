<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/10/13
 * Time: 17:05
 */
class MenuEnum {

    //菜单显示   把距离拉开是为了 以免以后中间要插入一些菜单
    const TUAN_GOU_MANGE        = '10';       //团购管理
    const SHOP_SALE_MANGE       = '30';       //出售中的宝贝
    const PRODUCT_MANGE         = '50';       //产品库管理
    const ORDER_MANGE           = '70';       //订单管理
    const DATA_REPORT_MANGE     = '90';       //数据报表
    const YUN_GOU_MANGE         = '100';       //一元云购
    const ARTICLE_MANGE         = '110';       //文章管理
    const TAXS_MANGE            = '130';       //税率管理
    const GEXING_MANGE          = '150';       //个性管理
    const MEMBER_MANGE          = '170';       //会员管理
    const YINGXIAO_MANGE        = '190';      //营销管理
    const SHOP_MANGE            = '210';      //商城配置
    const TEMPLATE_MANGE        = '230';      //模板设置
    const WECHAT_MANGE          = '250';      //微信设置
    const SOCIAL_MANGE          = '260';      //社区管理
    const APP_MANGE          	= '261';      //app管理
    const ROLE_MANGE            = '270';      //权限管理
    


    public static $getMenuEnumValues = array(
        self::TUAN_GOU_MANGE        => '换购管理',
        self::SHOP_SALE_MANGE       => '出售中的宝贝',
        self::PRODUCT_MANGE         => '产品库管理',
        self::ORDER_MANGE           => '订单管理',
        self::DATA_REPORT_MANGE     => '数据报表',
        self::YUN_GOU_MANGE         => '一元云购',
        self::ARTICLE_MANGE         => '文章管理',
        self::TAXS_MANGE            => '税率管理',
        self::GEXING_MANGE          => '个性设置',
        self::MEMBER_MANGE          => '会员管理',
        self::YINGXIAO_MANGE        => '营销管理',
        self::SHOP_MANGE            => '商城配置',
        self::TEMPLATE_MANGE        => '模板设置',
        self::WECHAT_MANGE          => '微信设置',
    	self::SOCIAL_MANGE          => '社区管理',
    	self::APP_MANGE          	=> 'app管理',
        self::ROLE_MANGE            => '权限管理'
    );

    public static $getMenuEnumUrl = array(
        self::TUAN_GOU_MANGE        => 'shop-mess',
        self::SHOP_SALE_MANGE       => 'shop-dish',
        self::PRODUCT_MANGE         => 'shop-category',
        self::ORDER_MANGE           => 'shop-order',
        self::DATA_REPORT_MANGE     => 'addon6',
        self::YUN_GOU_MANGE         => 'addon7',
        self::ARTICLE_MANGE         => 'addon8',
        self::TAXS_MANGE            => 'shop-taxrate',
        self::GEXING_MANGE          => 'shop-set',
        self::MEMBER_MANGE          => 'member-list',
        self::YINGXIAO_MANGE        => 'bonus-bonus',
        self::SHOP_MANGE            => 'shop-config',
        self::TEMPLATE_MANGE        => 'template-set',
        self::WECHAT_MANGE          => 'weixin-weixin',
    	self::SOCIAL_MANGE          => 'social-manage',
    	self::APP_MANGE          	=> 'app-manage',
        self::ROLE_MANGE            => 'user-user'
    );


    /*****系统人员权限  可以扩展*****/
    const ADMIN_RIGHT_MANAGER     = 10;
    const ADMIN_RIGHT_CS_MANAGER  = 20;
    const ADMIN_RIGHT_CS_COMMON   = 30;
    const ADMIN_RIGHT_RD_MANAGER  = 40;
    const ADMIN_RIGHT_RD_COMMON   = 50;
    const ADMIN_RIGHT_CS_EDITORIAL = 60;

    static public $getAdminRightValues = array(

        self::ADMIN_RIGHT_MANAGER       => '系统管理员',
        self::ADMIN_RIGHT_CS_COMMON     => '客服专员',
        self::ADMIN_RIGHT_CS_MANAGER    => '客服经理',
        self::ADMIN_RIGHT_RD_MANAGER    => '研发经理',
        self::ADMIN_RIGHT_RD_COMMON     => '研发专员',
        self::ADMIN_RIGHT_CS_EDITORIAL  => '编辑专员'
    );

    //角色权限关联  留着待用
    public static $roles = array(
        //系统管理员
        self::ADMIN_RIGHT_MANAGER => array(
//            self::TUAN_GOU_MANGE,
            self::SHOP_SALE_MANGE,
            self::PRODUCT_MANGE,
            self::ORDER_MANGE,
            self::DATA_REPORT_MANGE,
            self::ARTICLE_MANGE,
            self::TAXS_MANGE,
            self::GEXING_MANGE,
            self::MEMBER_MANGE,
            self::YINGXIAO_MANGE,
            self::SHOP_MANGE,
            self::TEMPLATE_MANGE,
            self::WECHAT_MANGE,
            self::ROLE_MANGE,

        ),
        //客服经理
        self::ADMIN_RIGHT_CS_MANAGER => array(

        ),
        //客服专员
        self::ADMIN_RIGHT_CS_COMMON => array(

        ),
        //研发经理
        self::ADMIN_RIGHT_RD_MANAGER => array(

        ),
        //研发专员
        self::ADMIN_RIGHT_RD_COMMON => array(

        ),
        //编辑专员
        self::ADMIN_RIGHT_CS_EDITORIAL => array(

        )

    );

    public static $dbFilesRule = array(
        'shop_goods'   => array(
            'status'        => '是否上架',
            'marketprice'   => '本店售价',
            'productprice'  => '市场售价',
        ),
        'shop_dish'    => array(
            'taxid'            => '保税设置',
            'status'           => '是否销售',
            'marketprice'      => '促销价格',
            'productprice'     => '参考价格',
            'type'             => '促销类型',
            'istime'           => '促销时间',
            'team_buy_count'   => '成团人数',
            'timeprice'        => '促销金额',
            'commision'        => '商品佣金比例',
            'vip_price'        => '会员价',
            'app_marketprice'  => 'APP端价格',
            'draw'             => '开启抽奖'
        )
    );

    public static $dbFiledValue = array(
        'shop_goods' => '产品库管理',
        'shop_dish'  => '出售中宝贝'
    );

}