<?php
$str              = 'valid';
$encrypt          = new orgelman\security\encrypt('sha256');
$encrypted        = $encrypt->encrypt($str, $key, $method = '')['encrypted'];

$decrypted        = $encrypt->decrypt($encrypted, $key, $method = '')['decrypted'];
