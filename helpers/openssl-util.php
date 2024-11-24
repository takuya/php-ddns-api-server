<?php
/**
 * OpenSSL で base64 で暗号化したものを平文に戻す。
 * `openssl enc -e -aes-256-cbc -pbkdf2 -iter ((1000*1000)) -in $f_in -out $f_out-k $pass -base64`
 * @param string $encrypted_base64
 * @param  string $pass
 * @param int    $iter
 * @param string $cipher
 * @return bool|string
 */
function openssl_equivalent_decrypt( string $encrypted_base64,
                                     string $pass,
                                     int    $iter = 1000*1000,
                                     string $cipher = "AES-256-CBC" ):bool|string {
  $encrypted_base64 = file_exists($encrypted_base64)? file_get_contents($encrypted_base64):$encrypted_base64;
  $encrypted = base64_decode($encrypted_base64);
  $iv_len = openssl_cipher_iv_length($cipher);
  $data = substr($encrypted, $iv_len);
  if( ! str_starts_with($encrypted, 'Salted__') ) {
    return false;
  }
  //
  $salt = substr($encrypted, strlen('Salted__'), $iv_len - strlen('Salted__'));
  $key_len = strlen('Salted__'.$salt) + $iv_len;
  $key_and_iv = openssl_pbkdf2($pass, $salt, $key_len + $iv_len, $iter, 'sha256');
  $key = substr($key_and_iv, 0, $key_len);
  $iv = substr($key_and_iv, $key_len, $iv_len);
  
  //
  return openssl_decrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}

