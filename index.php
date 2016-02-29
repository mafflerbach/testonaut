<?php


if (file_exists('composer.lock') ) {
  header('Location: web');
}

?>


<html>
<head>

</head>
<body>
<div class="contentBox">
  <?php
  print('<h2>Welcome to testonaut</h2><p>Please run <code>php composer.phar install</code>.</p>');
  ?>
</div>
</body>
</html>