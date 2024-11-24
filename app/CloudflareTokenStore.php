<?php

namespace App;

class CloudflareTokenStore {
  
  /**
   * @var array|mixed|string
   */
  private mixed $storage;
  
  public function __construct( array|string $json ) {
    if( is_string($json) ) {
      $json = json_decode($json);
    }
    if( is_array($json) ) {
      $this->storage = [];
      foreach ($json as $e) {
        $this->storage[$e->domain] = $e->key;
      }
    } else {
      $this->storage = $json;
    }
  }
  
  public function getToken( $domain ) :?string{
    $base_domain_name = join('.', array_slice(preg_split('/\./', $domain), -2, 2));
    
    return $this->storage[$base_domain_name] ?? null;
  }
  public function toJson(){
    $ret  = [];
    foreach ($this->storage as $k => $v) {
      $ret[] = ['domain'=>$k,'key'=>$v];
    }
    $opt = JSON_UNESCAPED_LINE_TERMINATORS|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;
    return json_encode($ret,$opt);
  }
  
  public function __toString():string {
    return $this->toJson();
  }
}

