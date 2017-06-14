<?php
   //获取店铺一级分类
  function getStoreCategoryAllparent($storeId=0,$filed = "id,name"){
    $category = mysqld_selectall("SELECT {$filed}  FROM " . table('store_shop_category') . "  where pid = 0 and store_shop_id = {$storeId} ORDER BY id ASC");
    return $category;
 }

    //根据一级分类获取对应的子类
  function getStoreCategoryChild($parentId=0,$filed = "id,name"){
    $category = mysqld_selectall("SELECT {$filed}  FROM " . table('store_shop_category') . "  where pid={$parentId}  ORDER BY id ASC");
    return $category;
 }
   
 //根据ID获取父类ID
  function getStoreCategory($Id=0,$filed = "id,name"){
    $category = mysqld_selectall("SELECT {$filed}  FROM " . table('store_shop_category') . "  where id={$Id}  ORDER BY id ASC");
    return $category;
 }

?>