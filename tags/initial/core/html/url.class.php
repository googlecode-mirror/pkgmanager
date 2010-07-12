<?php
// B.H.

class html_url {
  public $is_absolute; // is the url absolute or relative
  public $schema; // http/https etc
  public $host;
  public $path;
  public $query;
  
  public function __construct($url) {
    list($path,$query) = explode('?',$url,2);
    if (!empty($query)) $this->parse_query($query);
    if (preg_match('#^([a-z0-9]+)://(.*)$#i',$path,$m)) {
      $this->is_absolute = true;
      $this->schema = $m[1];
      list($host,$path) = explode('/',$m[2],2);
      $this->host = trim($host,'/');
      $this->path = trim($path,'/');
    } else {
      $this->is_absolute = false;
      $this->path = $path;
    }
    //echo "<pre>"; var_dump($this); die();
  }
  
  private function parse_query($query) {
    $q = explode('&',$query);
    foreach($q as $qstr) {
      list($name,$val) = explode('=',$qstr);
      if (!empty($name)) $this->query[$name] = urldecode($val);
    }
  }
  
  private function query_to_str($query_merge=null) {
    $query = $this->query;
    if (empty($query)) $query = $query_merge;
    elseif (is_array($query_merge)) $query = array_merge($query,$query_merge);
    if (empty($query)) return '';
    $res = array();
    foreach ($query as $name => $val) $res[] = $name."=".urlencode($val);
    return implode('&',$res);
  }
  
  public function get_url($query_merge=null) {
    if ($this->is_absolute) {
      $str = "{$this->schema}://{$this->host}/{$this->path}";
      $qstr = $this->query_to_str($query_merge);
      if (!empty($qstr)) $str .= '?'.$qstr;
    } else {
      $str = $this->path;
      $qstr = $this->query_to_str($query_merge);
      if (!empty($qstr)) $str .= '?'.$qstr;
    }
    return $str;
  }
  
  public function __toString() {
    return $this->get_url();
  }
  
  public function clone_url($query_merge=null) {
    $new_url = clone $this;
    if (empty($new_url->query)) $new_url->query = $query_merge;
    elseif (is_array($query_merge)) $new_url->query = array_merge($new_url->query,$query_merge);
    return $new_url;
  }
  
  public function set_host($schema,$host) {
    $this->is_absolute = true;
    $this->schema = $schema;
    $this->host = $host;
    $this->path = ltrim($this->path,'/');
  }
}

?>