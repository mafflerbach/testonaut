<?php
$install = false;
if (file_exists('composer.lock') && file_exists('installer')) {
    $install = true;
}
if (file_exists('composer.lock') && !file_exists('installer') ) {
    header('Location: web');
}

?>


<html>
<head>
    <link rel="stylesheet/less" type="text/css" href="web/css/style.less"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.5.0/less.min.js"></script>

    <script src="web/js/jquery-2.1.3.js"></script>
    <script src="web/js/custom.js"></script>
</head>
<body>
<div class="contentBox">
<?php

if(!$install) {

    print('<h2>Welcome to testonaut</h2><p>Click <a href="installer" class="btn btn-primary">here</a> for installation.</p>');
} else {
    print('<h2>Welcome to testonaut</h2><p style="color:darkred"><span class="fa fa-warning"></span>Please delete the install dir in testonaut root! </p>');
}


?>
</div>
</body>
</html>