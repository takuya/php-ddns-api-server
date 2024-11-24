<?php

namespace Takuya\Cloudflare;

class CloudflareDNS {
  
  
  public function __construct( public string $api_token,
                               string $zone=null ) {
  }
  public static function availableZones($api_token):array{
    $token   = new \Cloudflare\API\Auth\APIToken($api_token);
    $adapter = new \Cloudflare\API\Adapter\Guzzle($token);
    $zone = new \Cloudflare\API\Endpoints\Zones($adapter);
    $ret = collect($zone->listZones()->result)->pluck('id','name');
    return $ret->toArray();
  }
  public function zoneId($name){
    return static::findZoneId($this->api_token,$name);
  }
  public function recordId($name,$type='A'){
    return static::findRecordId($this->api_token,$name,$type);
  }
  public function list($name){
    return static::listRecord($this->api_token,$name);
  }
  
  public static function findZoneId( $api_token ,$domain_name):?string {
    $base_domain_name =join('.',array_slice(preg_split('/\./',$domain_name),-2,2));
    $token   = new \Cloudflare\API\Auth\APIToken($api_token);
    $adapter = new \Cloudflare\API\Adapter\Guzzle($token);
    $zone = new \Cloudflare\API\Endpoints\Zones($adapter);
    $zone_id = $zone->getZoneID($base_domain_name);
    return $zone_id;
    
  }
  public static function listRecord($api_token ,$domain_name):array{
    $zone_id = static::findZoneId($api_token,$domain_name);
    $token   = new \Cloudflare\API\Auth\APIToken($api_token);
    $adapter = new \Cloudflare\API\Adapter\Guzzle($token);
    $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
    $ret = $dns->listRecords(...['zoneID'=>$zone_id,'name'=> $domain_name??'','perPage'=>100]);
    $ret = collect($ret->result)->map(fn($e)=>collect($e)->toArray() );
    return $ret->toArray();
  }
  public static function findRecord($api_token ,$domain_name,$type=null):array{
    $zone_id = static::findZoneId($api_token,$domain_name);
    $token   = new \Cloudflare\API\Auth\APIToken($api_token);
    $adapter = new \Cloudflare\API\Adapter\Guzzle($token);
    $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
    $ret = $dns->listRecords(...['zoneID'=>$zone_id,'name'=> $domain_name??'','type'=>$type,'perPage'=>100]);
    return collect($ret->result)->toArray() ?? [];
  }
  public static function findRecordId( $api_token ,$domain_name,$type):?string{
    $ret = static::findRecord($api_token ,$domain_name,$type);
    $ret = collect($ret)->select(['name','id','type']);
    return collect($ret->get(0)??null)->get('id');
  }
  public function add($name,$addr,$type='A',$cf_proxied=false):bool{
    $base_domain_name =join('.',array_slice(preg_split('/\./',$name),-2,2));
    $token   = new \Cloudflare\API\Auth\APIToken($this->api_token);
    $adapter = new \Cloudflare\API\Adapter\Guzzle($token);
    $zone = new \Cloudflare\API\Endpoints\Zones($adapter);
    $z_id = $zone->getZoneID($base_domain_name);
    $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
    $ret = $dns->addRecord(...[
                         'zoneID'=>$z_id,
                         'type'=>'A',
                         'name'=>$name,
                         'content'=>$addr,
                         'proxied'=>$cf_proxied  # ← TXT のとき、ココを忘れないようにしてください。
                       ]);
    
    return $ret;
  }
  public function del($name):bool{
    $rid = $this->recordId($name);
    $zid = $this->zoneId($name);
    $token   = new \Cloudflare\API\Auth\APIToken($this->api_token);
    $adapter = new \Cloudflare\API\Adapter\Guzzle($token);
    $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
    return $dns->deleteRecord($zid,$rid);
  }
  public function update( $name, $addr, $type='A' , $cf_proxied = false ) {
    $rid = $this->recordId($name,$type);
    $zid = $this->zoneId($name);
    $token   = new \Cloudflare\API\Auth\APIToken($this->api_token);
    $adapter = new \Cloudflare\API\Adapter\Guzzle($token);
    $dns = new \Cloudflare\API\Endpoints\DNS($adapter);
    $ret = $dns->updateRecordDetails($zid,$rid,[
      'type'=>$type,
      'name'=>$name,
      'content'=>$addr,
      'proxied'=>$cf_proxied
    ]);
    return collect($ret?->result)->toArray() ?? [];
  }
  public function get($name, $type='A'){
    return static::findRecord($this->api_token,$name,$type);
  }

}

