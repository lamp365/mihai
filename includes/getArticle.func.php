<?php
function getArticle($num=0,$state=0){
    $where = ' and state =  '.$state;
    $limit = ' limit '.$num;
	if ($state <= 1){
	   $category = mysqld_selectall("SELECT * FROM " . table('addon8_article_category') . "  where deleted=0 ".$where." ORDER BY parentid ASC, displayorder DESC ".$limit);
	   foreach ($category as $index => $row) {
		if ( $state != 1){
           $where ='';
		   $article = mysqld_selectall("SELECT * FROM " . table('addon8_article') . "  where pcate = ".$row['id']." ".$where." ORDER BY  displayorder DESC ".$limit);
           $category = $article;
		}else{
		   $article = mysqld_selectall("SELECT * FROM " . table('addon8_article') . "  where pcate = ".$row['id']." ".$where." ORDER BY  displayorder DESC ".$limit);
           $category[$index]['article'] = $article;
		}
	  }
	}else{
          $category = mysqld_selectall("SELECT * FROM " . table('addon8_article') . "  where 1 = 1".$where." ORDER BY  displayorder DESC ".$limit);
	}
	
	return $category;
}

function get_artile_tree($data){
	$result = array();
	if(!empty($data)){
		foreach($data as $key => $row){
			$temp   = array();
			$result[$key]['name'] = $row['name'];
			if(!empty($row['article'])){
				foreach($row['article'] as $art){
					$temp[$art['id']] = $art['title'];
				}
			}
			$result[$key]['son'] = $temp;
		}
	}
	return $result;

}