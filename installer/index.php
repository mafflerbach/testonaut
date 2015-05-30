<?php

?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>NoConsoleComposer for Testonaut</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                check();
            });
            function url() {
                return 'main.php';
            }
            function call(func) {
                $("#output").append("\nplease wait...\n");
                $("#output").append("\n===================================================================\n");
                $("#output").append("Executing Started");
                $("#output").append("\n===================================================================\n");
                $.post('main.php',
                    {
                        "path": $("#path").val(),
                        "command": func,
                        "function": "command"
                    },
                    function (data) {
                        $("#output").append(data);
                        $("#output").append("\n===================================================================\n");
                        $("#output").append("Execution Ended");
                        $("#output").append("\n===================================================================\n");
                    }
                );
            }
            function check() {
                $("#output").append('\nloading...\n');
                $.post(url(),
                    {
                        "function": "getStatus",
                        "password": $("#password").val()
                    },
                    function (data) {
                        if (data.composer_extracted) {
                            $("#output").html("Ready. All commands are available.\n");
                            $("button").removeClass('disabled');
                        }
                        else if (data.composer) {
                            $.post(url(),
                                {
                                    "password": $("#password").val(),
                                    "function": "extractComposer",
                                },
                                function (data) {
                                    $("#output").append(data);
                                    window.location.reload();
                                }, 'text');
                        }
                        else {
                            $("#output").html("Please wait till composer is being installed...\n");
                            $.post(url(),
                                {
                                    "password": $("#password").val(),
                                    "function": "downloadComposer",
                                },
                                function (data) {
                                    $("#output").append(data);
                                    check();
                                }, 'text');
                        }
                    });
            }
        </script>
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
            <h1>NoConsoleComposer for Testonaut</h1>
                <sup><a href="https://github.com/CurosMJ/NoConsoleComposer">NoConsoleComposer</a> </sup>
            <hr/>
            <h3>Commands:</h3>

            <div class="form-inline">
                <button id="self-update" onclick="del()" class="btn btn-success disabled">Update Composer</button>
                <br/><br/>
                <input type="text" id="path" style="width:300px;" class="form-control disabled"
                       placeholder="absolute path to project directory" value="<?php print(getWorkingDir());?>"/>
                <button id="install" onclick="call('install')" class="btn btn-success disabled">install</button>
                <button id="update" onclick="call('update')" class="btn btn-success disabled">update</button>
                <button id="update" onclick="call('dry-run')" class="btn btn-success disabled">dry-run</button>
            </div>
            <h3>Console Output:</h3>
            <pre id="output" class="well"></pre>
            <a class="btn btn-success" href="../">BACK</a>
        </div>
        <div class="col-lg-1"></div>
    </div>
    </body>
    </html>

<?php
function getWorkingDir()
{
    $dir = str_replace('\\', '/', __DIR__);
    return str_replace('/installer', '', $dir);
}

?>