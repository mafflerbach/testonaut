<?php

$data = $_REQUEST['canvas'];
$path = $_REQUEST['path'];

var_dump($_REQUEST);

list($type, $data) = explode(';', $data);
list(, $data)      = explode(',', $data);
$data = base64_decode($data);

file_put_contents('./'.$path, $data);


?>