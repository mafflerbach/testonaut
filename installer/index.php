<?php

?>
  <!DOCTYPE html>
  <html>
  <head>
    <title>Installer for Testonaut</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="../web/css/themes/bootstrap.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="../web/js/vendor/jquery-2.1.3.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <style>
      #output {
        width: 100%;
        height: 350px;
        overflow-y: scroll;
        overflow-x: hidden;
      }
    </style>
  </head>
  <body>
  <div class="row">
    <div class="col-lg-1"></div>
    <div class="col-lg-10">
      <h1>Installer for Testonaut</h1>
      <hr/>

      <div class="form-inline">
        <button id="install" onclick="call('install')" class="btn btn-success disabled">install</button>
        <button id="update" onclick="call('update')" class="btn btn-success disabled">update</button>

        <input type="checkbox"
               name="dry-run"
               id="dry-run"/>

        <label class="checkbox"
               for="dry-run">dry run</label>

      </div>
      <h3>Console Output:</h3>
      <pre id="output" class="well"></pre>
      <a class="btn btn-success" href="../">BACK</a>
    </div>
    <div class="col-lg-1"></div>
  </div>

  <p>Thanks for inspiration: <a href="https://github.com/CurosMJ/NoConsoleComposer">NoConsoleComposer</a></p>
  </body>
  </html>

<?php


?>