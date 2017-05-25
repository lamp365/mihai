<?php

/**
* memcache类
*/
class Mcache {
  
  public $host = 'localhost';
  public $post = 11211;
  public $m;  
  public $key_pre = 'cbd_';    //设置key前缀，以免同一个机器吗，多套应用导致key相同。

  function __construct() {
    $this->m = new Memcached();
    $this->m->addServer($this->host,$this->post);
  }

  /**
   * 使用memcached做数据库查询
   * 
   * @param string $sql：query查询语句
   * @param int $expiration：到期时间
   * @return boolean
   */
  public function mysqlm_query($sql, $expiration=0) {
    $key = md5($sql);
    $data = $this->get($key);
    if (!$data) {
      $data = mysqld_query($sql);
      $this->add($key, $data);
    }
    return $data;
  }

  /**
   * 初始化登陆信息
   * 
   * @param string $device_code：设备识别码
   * @return bool
   */
  public function init_msession($device_code,$account = array()) {
    if(empty($account)){
        $account = get_member_account(true);
    }
    $have_ac = $this->get($account['openid']);
    if (!empty($have_ac)) {
      //吧之前的设备号 标记登出为2
      $this->set($have_ac['device'].'_belogout', 2);
      $this->delete($have_ac['device']);
      $this->delete($account['openid']);
    }

    $rsa     = new Rsa();
    $app_key = $rsa->pass_key();   //得到一个code
    $account['app_key'] = $rsa->rsaPriEncrypt($app_key);  //通过code去用rsa加密为openssl格式

    //吧当前的设备号 标记登出为1
    $this->set($device_code.'_belogout', 1);
    $re1 = $this->set($account['openid'], array('account' => $account, 'device' => $device_code));
    $re2 = $this->set($device_code, array('account' => $account, 'device' => $device_code));

    if ($re1 AND $re2) {
      return $account['app_key'];
    }else{
      return false;
    }
  }

  /**
   * 判断是否被顶号
   * 
   * @param string $device_code：设备识别码
   * @return int
   */
  public function be_logout($device_code) {
    $be_logout = $this->get($device_code.'_belogout');
    if ($be_logout == 2) {
      //如果已经被人登出了 清空掉缓存信息
       $cache = $this->get($device_code);
       $this->set($device_code.'_belogout', 1);
       $this->delete($device_code);
       $this->delete($cache['account']['openid']);
    }

    return $be_logout;
  }

  /**
   * 返回登陆信息
   *
   * @param string $device_code：设备识别码
   * @return array
   */
  public function get_msession($device_code) {
    $dev = $this->get($device_code);
    if (!empty($dev)) {
      $mem = $dev['account'];
      //cookie过期 但是memcahe还存在用户的登录有效性，则帮用户再次设置session
      $new_mem = save_member_login('',$mem['openid']);
      return $new_mem;
    }else{
      return false;
    }
  }

  /**
   * 清除登陆信息
   *
   * @param string $device_code：设备识别码
   * @return bool
   */
  public function del_msession($device_code) {
    $account = get_member_account();
    $_SESSION    = array();
    session_destroy();
    $this->delete($device_code.'_belogout');
    $this->delete($device_code);
    $this->delete($account['openid']);
    return true;
  }

  /**
   * 存储一个元素
   * 
   * @param string $key：用于存储值的键名
   * @param mixed $value：存储的值
   * @param int $expiration：到期时间，默认为0(0不过期，可传小于60*60*24*30的秒数，超过这个秒数将被视为Unix时间戳)
   * @return boolean
   */
  public function set($key, $value, $expiration=0) {
    $key = $this->key_pre.$key;
    $re = $this->m->set($key, $value, $expiration);

    // log
    // logRecord(date("Y-m-d H:i:s", time()).'|set|key='.$key."|value=".$value."|re=".$re,'mcache');
    return $re;
  }

  /**
   * 存储多个元素
   * 
   * @param array $items：存放在服务器上的键／值对数组(array('key1' => 'value1','key2' => 'value2'))
   * @param int $expiration：到期时间
   * @return boolean
   */
  public function setMulti($items, $expiration=0) {
    $re = $this->m->setMulti($items, $expiration);
    return $re;
  }

  /**
   * 检索一个元素
   * 
   * @param string $key：要检索的元素的key
   * @param callback $cache_cb：通读缓存回掉函数或NULL
   * @return 
   */
  public function get($key, $cache_cb=NULL) {
    $key = $this->key_pre.$key;
    $re = $this->m->get($key);

    // log
    // logRecord(date("Y-m-d H:i:s", time()).'|get|key='.$key."|re=".$re,'mcache');
    return $re;
  }

  /**
   * 检索多个元素
   * 
   * @param string $keys：要检索的key的数组
   * @return 
   */
  public function getMulti($keys) {
    $re = $this->m->getMulti($keys);
    return $re;
  }

  /**
   * 检索服务器上所有的key
   * 
   * @return 
   */
  public function getAllKeys() {
    $re = $this->m->getAllKeys();
    return $re;
  }

  /**
   * 向一个新的key下面增加一个元素
   * 
   * @param string $key：用于存储值的键名
   * @param mixed $value：存储的值
   * @param int $expiration：到期时间
   * @return boolean
   */
  public function add($key, $value, $expiration=0) {
    $key = $this->key_pre.$key;
    $re = $this->m->add($key, $value, $expiration);
    return $re;
  }
  
  /**
   * 向已存在元素后追加数据
   * 
   * @param string $key：追加值的键名
   * @param mixed $value：追加的值
   * @return boolean
   */
  public function append($key, $value) {
    $key = $this->key_pre.$key;
    $re = $this->m->append($key, $value);
    return $re;
  }

  /**
   * 增加数值元素的值
   * 
   * @param string $key：要增加值的元素的key
   * @param int $offset：要将元素的值增加的大小
   * @return boolean
   */
  public function increment($key, $offset=NULL) {
    $key = $this->key_pre.$key;
    $re = $this->m->increment($key, $offset);
    return $re;
  }

  /**
   * 替换已存在key下的元素
   * 
   * @param string $key：用于存储值的键名
   * @param mixed $value：存储的值
   * @param int $expiration：到期时间
   * @return boolean
   */
  public function replace($key, $value, $expiration=0) {
    $key = $this->key_pre.$key;
    $re = $this->m->replace($key, $value, $expiration);
    return $re;
  }

  /**
   * 删除一个元素
   * 
   * @param string $key：要删除的key
   * @param int $time：服务端等待删除该元素的总时间(或一个Unix时间戳表明的实际删除时间)
   * @return boolean
   */
  public function delete($key, $time=NULL) {
    $key = $this->key_pre.$key;
    $re = $this->m->delete($key, $time);

    // log
    // logRecord(date("Y-m-d H:i:s", time()).'|delete|key='.$key."|re=".$re,'mcache');
    return $re;
  }

  /**
   * 删除多个元素
   * 
   * @param array $keys：要删除的keys
   * @param int $time：服务端等待删除该元素的总时间(或一个Unix时间戳表明的实际删除时间)
   * @return boolean
   */
  public function deleteMulti($keys, $time=NULL) {
    foreach($keys as &$val){
      $val = $this->key_pre.$val;
    }
    $re = $this->m->deleteMulti($keys, $time);
    return $re;
  }
}