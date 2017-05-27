<?php
   $success = new LtCookie();
   $order =  $success->getCookie('success');
   if ( !empty($order) ){
      $order = unserialize($order);
   }
   include themePage('noidentity');