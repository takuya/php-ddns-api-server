<?php

namespace Tests;

use Illuminate\Support\Str;
use App\CloudflareTokenStore;
use Takuya\Cloudflare\CloudflareDNS;

class DDNS_UPDATE_Test extends TestCase {
  
  public function test_web_root_access() {
    //
    $res = $this->call('GET', '/');
    $this->assertEquals(200, $res->status());
    //
    $res = $this->get('/')->response;
    $this->assertEquals(200, $res->status());
  }
  
  public function test_web_auth_by_token_as_get_param() {
    /** @var \Illuminate\Testing\TestResponse $res */
    $res = $this->call('GET', '/auth?token='.config('app.token'));
    $this->assertEquals(200, $res->status());
    $this->assertStringContainsString('ok', $res->content());
  }
  
  public function test_web_show_cloudflare_tokens() {
    $res = $this->call('GET', '/cf_tokens?token='.config('app.token'));
    $this->assertEquals(200, $res->status());
    $res->assertHeader('Content-Type', 'application/json');
  }
  
  public function test_ddns_update_api() {
    $token = json_decode(app(CloudflareTokenStore::class), JSON_OBJECT_AS_ARRAY);
    $domain = $token[0]['domain'];
    $api_token = $token[0]['key'];
    // setup
    $name = Str::lower(Str::random(5))."-phpunit.{$domain}";
    $cf = new CloudflareDNS($api_token);
    $ret['add'] = $cf->add($name, '1.1.1.1');
    // // testing
    $res = $this->call('GET', "/ddns/{$name}/8.8.8.8?token=".config('app.token'));
    $ret['api'] = json_decode($res->content(), JSON_OBJECT_AS_ARRAY);
    // tear down
    $ret['del'] = $cf->del($name);
    $ret['get'] = $cf->get($name);
    // assertion
    $this->assertTrue($ret['add']);
    $this->assertEquals(
      preg_split(
        '/ /',
        'id zone_id zone_name name type content proxiable proxied ttl settings meta comment tags created_on modified_on'),
      array_keys($ret['api']));
    $this->assertEquals('8.8.8.8',$ret['api']['content']);
    $this->assertEquals('A',$ret['api']['type']);
    $this->assertTrue($ret['del']);
    $this->assertEmpty($ret['get']);
  }
}
