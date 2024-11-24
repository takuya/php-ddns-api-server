<?php

namespace Tests;

use Illuminate\Support\Str;
use App\CloudflareTokenStore;
use Takuya\Cloudflare\CloudflareDNS;
use PHPUnit\Framework\TestCase as BaseTestCase;

class CloudflareDnsTest extends BaseTestCase {
  
  public function __construct( string $name ) {
    parent::__construct($name);
    $app =  require __DIR__.'/../bootstrap/app.php';
    $token = app(CloudflareTokenStore::class);
    $token = json_decode($token,JSON_OBJECT_AS_ARRAY);
    $this->domain = $token[0]['domain'];
    $this->key = $token[0]['key'];
  }
  
  public function test_cloudflare_find_dns_zone(){
    $api_token = $this->key;
    $domain =  $this->domain;
    //
    $ret = CloudflareDNS::availableZones($api_token);
    $this->assertIsArray($ret);
    //
    $ret = CloudflareDNS::findRecordId($api_token,"tp.{$domain}",'A');
    $this->assertIsString($ret);
    $this->assertNotEmpty($ret);
  }
  public function test_cloudfare_add_update_del_record(){
    $api_token = $this->key;
    $domain =  $this->domain;
    //
    $name = Str::lower(Str::random(5))."-phpunit.{$domain}";
    $cf = new CloudflareDNS($api_token);
    $ret['add'] = $cf->add($name,'1.1.1.1');
    $ret['list_add'] = $cf->list($name);
    $ret['update'] = $cf->update($name,'1.0.0.1');
    $ret['list_update'] = $cf->list($name);
    $ret['del'] = $cf->del($name);
    $this->assertEquals('1.1.1.1',$ret['list_add'][0]['content']);
    $this->assertEquals('1.0.0.1',$ret['list_update'][0]['content']);
    $this->assertEquals('1.0.0.1',$ret['update']['content']);
    $this->assertTrue($ret['del']);
  }
  public function test_cloudflare_add_del_dns_record(){
    $api_token = $this->key;
    $domain =  $this->domain;
    //
    $name = Str::lower(Str::random(5))."-phpunit.{$domain}";
    $cf = new CloudflareDNS($api_token);
    $ret['add'] = $cf->add($name,'1.1.1.1');
    $ret['list_exists'] = $cf->list($name);
    $ret['del'] = $cf->del($name);
    $ret['id'] = $cf->recordId($name);
    $ret['list_empty'] = $cf->list($name);
    
    $this->assertTrue($ret['add']);
    $this->assertIsArray($ret['list_exists']);
    $this->assertEquals(1, sizeof($ret['list_exists']));
    $this->assertArrayHasKey('id',$ret['list_exists'][0]);
    $this->assertNull($ret['id']);
    $this->assertEmpty($ret['list_empty']);
    $this->assertTrue($ret['del']);
  }
  public function test_cloudflare_find_record() {
    $api_token = $this->key;
    $domain =  $this->domain;
    $cf = new CloudflareDNS($api_token);
    $ret = $cf->get($domain);
    $this->assertTrue(sizeof($ret)>0);
  }
}
