<?php
class arrayiconv    
{    
static protected $in;    
static protected $out;    
/**   
  * 静态方法,该方法输入数组并返回数组   
  *   
  * @param unknown_type $array 输入的数组   
  * @param unknown_type $in 输入数组的编码   
  * @param unknown_type $out 返回数组的编码   
  * @return unknown 返回的数组   
  */   
static public function Conversion($array,$in,$out)    
{    
  self::$in=$in;    
  self::$out=$out;    
  return self::arraymyicov($array);    
}    
/**   
  * 内部方法,循环数组   
  *   
  * @param unknown_type $array   
  * @return unknown   
  */   
static private function arraymyicov($array)    
{    
  foreach ($array as $key=>$value)    
  {    
   $key=self::myiconv($key);    
   if (!is_array($value)) {    
    $value=self::myiconv($value);    
   }else {    
    $value=self::arraymyicov($value);    
   }    
   $temparray[$key]=$value;    
  }    
  return $temparray;    
}    
/**   
  * 替换数组编码   
  *   
  * @param unknown_type $str   
  * @return unknown   
  */   
static private function myiconv($str)    
{   
  
 // return iconv(self::$in,self::$out."//TRANSLIT",$str) ;
  return mb_convert_encoding($str,self::$out,self::$in);    
}    
}
?>