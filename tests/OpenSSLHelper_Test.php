<?php

namespace Tests;
use PHPUnit\Framework\TestCase as BaseTestCase;


class OpenSSLHelper_Test extends  BaseTestCase{
  
  public function test_openssl_decrypt(){
    $enc_data = "U2FsdGVkX196swnbh3ABbOolawdPCF2JF0G5E/R7p3GODvrAMnnPITWUe6WEotnx";
    $pass="my_strong_password";
    $iter=1000*1000;
    $plain_text = openssl_equivalent_decrypt($enc_data,$pass,$iter);
    //
    $this->assertEquals("ABC=EDF=BBBB=CCC=XXXX",$plain_text);
  }

}