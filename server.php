<?php

$data = $_REQUEST['canvas'];
$path = $_REQUEST['path'];

list($type, $data) = explode(';', $data);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);

var_dump(str_replace('\\', '\\', $path));

file_put_contents(str_replace('\\', '\\', $path), $data);


?>