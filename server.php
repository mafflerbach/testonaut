<?php

$data = $_REQUEST['canvas'];
$path = $_REQUEST['path'];

list($type, $data) = explode(';', $data);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);

file_put_contents(str_replace('\\', '\\', $path), $data);


?>