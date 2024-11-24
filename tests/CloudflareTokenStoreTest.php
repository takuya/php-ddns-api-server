<?php

namespace Tests;

use App\CloudflareTokenStore;

class CloudflareTokenStoreTest extends TestCase {
  public function test_cloudflare_token_store_new(){
    //
    $json_str = '
    [
      {"domain": "example.tld","key": "1234"},
      {"domain": "sample.tld","key": "abc"}
    ]';
    $cf_token = new CloudflareTokenStore($json_str);
    //
    $this->assertEquals('1234',$cf_token->getToken('example.tld'));
    $this->assertEquals('abc',$cf_token->getToken('sample.tld'));
    $this->assertEmpty($cf_token->getToken('xxxxx.ccc'));
    $result = json_decode((string)$cf_token,JSON_OBJECT_AS_ARRAY);
    $this->assertJsonStringEqualsJsonString($cf_token,$json_str);
  }
  public function test_cloudflare_token_service_container(){
    $token = app(CloudflareTokenStore::class);
    $this->assertNotEmpty($token);
    
  }
}
