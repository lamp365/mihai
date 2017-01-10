<?php
/**
 * 健康文化接口
 * @var unknown
 */

$result = array();

$op = $_GP['op'];

switch($op)
{
	case 'detail':		//详情
		
		$member=get_member_account(true,true);
		
		if(!empty($member) AND $member != 3)
		{
			$openid = $member['openid'];
		}
		else{
			$openid = '';
		}
		
		$id = (int)$_GP['id'];		//文章ID
		
		
		if(empty($id))
		{
			$result ['message'] = '健康文化ID不能为空';
			$result ['code'] 	= 0;
		}
		else{
			$collectionCnt 	= mysqld_select("SELECT count(collection_id) cnt FROM " . table('article_collection') . " where article_id={$id} ");
			$commentCnt 	= mysqld_select("SELECT count(comment_id) cnt FROM " . table('article_comment') . " where article_id={$id} ");
			$articleInfo 	= mysqld_select("SELECT id,title,thumb,createtime FROM " . table('addon8_article')." where id={$id} and state=6");
				
			$result['data']['url'] 			= getArticleUrl($id,'healty',$openid,1);
			$result['data']['isCollection'] = isCollection($id,$member);				//是否已收藏
			$result['data']['collectionCnt']= $collectionCnt['cnt'];					//收藏数
			$result['data']['commentCnt']	= $commentCnt['cnt'];						//评论数
			$result['data']['share_url']	= getArticleUrl($id,'healty',$openid);		//分享链接
			$result['data']['articleInfo']	= $articleInfo;								//文章信息
			
			$result ['code'] 				= 1;
		}
		
		break;
		
	case 'dish_list':		//文章中的宝贝列表
		
		$id = (int)$_GP['id'];		//文章ID
		
		$sql = "SELECT id,title,thumb,content FROM " . table('addon8_article');
		$sql.= " WHERE state=6 ";					//健康文化
		$sql.= " and id={$id} ";
		
		$articleInfo = mysqld_select($sql);
		
		if($articleInfo)
		{
			$dishIds = analyzeShopContent($articleInfo['content'],true);
			
			if(!empty($dishIds))
			{
				$where =' a.id in('.implode(",", $dishIds).')  and a.status=1 and a.deleted=0';
			
				$dish_list = get_goods(array('field'=>'a.id,a.p1,a.p2,a.p3,a.title,a.productprice,a.marketprice,a.thumb,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.commision,a.sales,a.total,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market ',
											'table'	=>'shop_dish',
											'where'	=> $where,
											'order'	=> $order
											
				));
				$result ['data']['dish_list'] 	= $dish_list;
				$result ['code'] 				= 1;
			}
			else{
				$result ['data']['dish_list'] 	= array();
				$result ['code'] 				= 1;
			}
		}
		else{
			$result ['message'] = '健康文化不存在';
			$result ['code'] 	= 0;
		}
		
		break;
		
	default:			//列表
		
		$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
		$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS id,title,thumb,createtime FROM " . table('addon8_article');
		$sql.= " WHERE state=6 ";					//健康文化
		$sql.= " order by createtime desc";
		$sql.= " limit ".(($page-1)*$limit).','.$limit;
		
		$arrArticle = mysqld_selectall($sql);
		
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
		
		$result['data']['article'] 	= $arrArticle;
		$result['data']['total'] 	= $total['total'];
		$result['code'] 			= 1;
		
		break;
}

echo apiReturn($result);
exit;


/**
 * 文章是否已经收藏
 *
 * @param $article_id:int 文章ID
 * @param $member:用户登录信息
 *
 * @return boolean
 */
function isCollection($article_id,$member)
{
	//已登录
	if(!empty($member) AND $member != 3)
	{
		//收藏信息
		$collection = mysqld_select("SELECT collection_id FROM " . table('article_collection') . " where article_id={$article_id} and openid=:openid",array(':openid' => $member['openid']));

		//未收藏时
		if(empty($collection))
		{
			return 0;
		}
		else{
			return 1;
		}
	}
	//未登录
	else{
		return 0;
	}
}