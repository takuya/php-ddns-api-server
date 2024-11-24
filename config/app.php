<?php
return [
  "name"            => env('APP_NAME','Lumen'),
  "env"             => env('APP_ENV',"local"),
  "debug"           => env('APP_DEBUG',true),
  "url"             => env('APP_URL',"http://localhost"),
  "timezone"        => env('APP_TIMEZONE',"UTC"),
  "locale"          => env("APP_LOCALE","en"),
  "fallback_locale" => 'en',
  "key"             => env("APP_KEY"),
  "cipher"          => "AES-256-CBC",
  //
  'token'=> env('APP_TOKEN'),
  'cf_token_cache_enabled'=>env('APP_CF_TOKEN_CACHE_ENABLED'),
];
