$(document).ready(function () {
    check(0);
});
function url() {
    return 'main.php';
}
function call(func) {

    var dryrun = false;
    if ($('input[type="checkbox"]:checked').length > 0) {
        dryrun = true;
    }

    $("#output").append("\nplease wait...\n");
    $("#output").append("\n===================================================================\n");
    $("#output").append("Executing Started");
    $("#output").append("\n===================================================================\n");
    $.post('main.php',
        {
            "path": $("#path").val(),
            "command": func,
            "function": "command",
            "dryrun": dryrun
        },
        function (data) {
            $("#output").append(data);
            $("#output").append("\n===================================================================\n");
            $("#output").append("Execution Ended");
            $("#output").append("\n===================================================================\n");
        }
    );
}

function check(run) {
    if (run > 2) {
        return;
    }
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
                        "function": "extractComposer"
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
                        "function": "downloadComposer"
                    },
                    function (data) {
                        $("#output").append(data);
                        run++;
                        check(run);
                    }, 'text');
            }
        });
}
