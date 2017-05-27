/* $Id : common.js 4865 2007-01-31 14:04:10Z paulgao $ */

/* *
 * 添加商品到购物车 
 */
function addToCart(goodsId, cp, parentId)
{
  var goods        = new Object();
  var spec_arr     = new Array();
  var fittings_arr = new Array();
  var number       = 1;
  var formBuy      = document.forms['ECS_FORMBUY'];
  var quick		   = 0;

  // 检查是否有商品规格 
  if (formBuy)
  {
    spec_arr = getSelectedAttributes(formBuy);

    if (formBuy.elements['number'])
    {
      number = formBuy.elements['number'].value;
    }

	quick = 1;
  }

  goods.quick    = quick;
  goods.spec     = spec_arr;
  goods.goods_id = goodsId;
  goods.number   = number;
  goods.parent   = (typeof(parentId) == "undefined") ? 0 : parseInt(parentId);

  Ajax.call('buy.php?act=add_to_cart', 'cp='+ cp +'&goods=' + $.toJSON(goods), addToCartResponse, 'POST', 'JSON');
}

/**
 * 获得选定的商品属性
 */
function getSelectedAttributes(formBuy)
{
  var spec_arr = new Array();
  var j = 0;

  for (i = 0; i < formBuy.elements.length; i ++ )
  {
    var prefix = formBuy.elements[i].name.substr(0, 5);

    if (prefix == 'spec_' && (
      ((formBuy.elements[i].type == 'radio' || formBuy.elements[i].type == 'checkbox') && formBuy.elements[i].checked) ||
      formBuy.elements[i].tagName == 'SELECT'))
    {
      spec_arr[j] = formBuy.elements[i].value;
      j++ ;
    }
  }

  return spec_arr;
}

/* *
 * 处理添加商品到购物车的反馈信息
 */
function addToCartResponse(result)
{
  if (result.error > 0)
  {
    // 如果需要缺货登记，跳转
    if (result.error == 2)
    {
      if (confirm(result.message))
      {
        //location.href = 'user.php?act=add_booking&id=' + result.goods_id + '&spec=' + result.product_spec;
		location.href = 'kefu.php';
      }
    }
    // 没选规格，弹出属性选择框
    //else if (result.error == 6)
    //{
    //  openSpeDiv(result.message, result.goods_id, result.parent);
    //}
    else
    {
      alert(result.message);
    }
  }
  else
  {
    var cart_url = 'cart.php';

    if (result.ctype == '1')
    {
	  $("#buy_lay").show();
	  $("#buy_lay_frm").show();
	  $("#buy_lay_frm").css({"top":($(window).height()/2-70)+'px'});
    }else{
		location.href = cart_url;
	}
   
  }
}

/* *
 * 添加商品到收藏夹
 */
function collect(goodsId)
{
  Ajax.call('user.php?act=collect', 'id=' + goodsId, collectResponse, 'GET', 'JSON');
}

/* *
 * 处理收藏商品的反馈信息
 */
function collectResponse(result)
{
  alert(result.message);
}

/* *
 *  返回属性列表
 */
function getAttr(cat_id)
{
  var tbodies = document.getElementsByTagName('tbody');
  for (i = 0; i < tbodies.length; i ++ )
  {
    if (tbodies[i].id.substr(0, 10) == 'goods_type')tbodies[i].style.display = 'none';
  }

  var type_body = 'goods_type_' + cat_id;
  try
  {
    document.getElementById(type_body).style.display = '';
  }
  catch (e)
  {
  }
}

//该文件用来写一些公共的方法。以上估计没有别处用到，后期确认后，可进行删除掉
//该文件用来写一些公共的方法。以上估计没有别处用到，后期确认后，可进行删除掉
//该文件用来写一些公共的方法。以上估计没有别处用到，后期确认后，可进行删除掉

/**
 * 封装一个加载滚动获取下一页
 * 1、页面要加载append这一段
 * <input type="hidden" value="2" id="page"/>
  <div class="ajax_next_page">
    <img class="jiazai" src="../recouse/images/ajax-loader.gif"/>
    正在加载
  </div>
 <div class="ajax_next_page_foot"></div>

 2、当滚动到底部时请求数据
    function nextpage(){
		//滚动条到底部时就加载剩下数据
		$(window).scroll(function(){
			if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
				Refresh(url,json_data,'wai_func','#id');
				请求url  请求json对象数据  关联外面的一个方法，用于组装html    要追加到页面的id
			}
		})
	}
 */
