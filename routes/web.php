<?php
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\CloudflareTokenStore;
use Takuya\Cloudflare\CloudflareDNS;

$router->get('/', function () use ( $router ) { return $router->app->version(); });
$router->get('/auth',      ['middleware' => ['auth'],function(){return 'token is ok.';}]);
$router->get('/cf_tokens', ['middleware' => ['auth'],function(){return json_decode(app(CloudflareTokenStore::class));}]);
$router->get('/ddns/{domain}/{ip}/', ['middleware' => ['auth'], function ( $domain, $ip ) {
  $token = app(CloudflareTokenStore::class)->getToken($domain);
  if(empty($token)){
    return response( 'bad domain ( no token for this ).', 400 );
  }
  $type = null;
  is_ip_v4($ip) && $type = 'A';
  is_ip_v6($ip) && $type = 'AAAA';
  if (empty($type)){
    return response( 'bad ip.', 400 );
  }
  
  $cf = new CloudflareDNS($token);
  if (empty($cf->get($domain))){
    return response( 'bad domain ( no record on cloudflare).', 400 );
  }
  $ret = $cf->update($domain,$ip,$type);
  //
  return $ret;
},]);


