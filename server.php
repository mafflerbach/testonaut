<?php

$data = $_REQUEST['canvas'];
$path = $_REQUEST['path'];

list($type, $data) = explode(';', $data);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);
$path = str_replace('\\', '\\', $path);
$path = str_replace('//', '/', $path);
file_put_contents($path, $data);


?>