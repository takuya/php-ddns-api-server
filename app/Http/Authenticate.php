<?php

namespace App\Http;

use Closure;
use Illuminate\Http\Request;
use function config;
use function response;

class Authenticate {
  
  public function handle( Request $req, Closure $next, $guard = null ) {
  
    if($req->get('token') !== config('app.token')){
      return response( 'Unauthorized.', 401 );
    }
    return $next($req);
  }
}
