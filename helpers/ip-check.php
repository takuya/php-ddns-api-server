<?php


$IPV6_REGEX="\(\([0-9A-Fa-f]\{1,4\}:\)\{1,\}\)\(\([0-9A-Fa-f]\{1,4\}\)\{0,1\}\)\(\(:[0-9A-Fa-f]\{1,4\}\)\{1,\}\)";
$IPV4_REGEX="[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}";

function is_ip_v6($ip_addr):bool {
  return (bool)filter_var($ip_addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
}
function is_ip_v4($ip_addr):bool {
  return (bool)filter_var($ip_addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
}

return [
  'IPV4_REGEX' => $IPV4_REGEX,
  'IPV6_REGEX' => $IPV6_REGEX,
];





