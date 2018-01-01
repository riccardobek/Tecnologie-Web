$(document).ready(function() {
    $("form").on("submit",function(event) {
        event.preventDefault();
        event.stopPropagation();

        $("div.alert").hide();

        $.post("php/do_login.php",$("form").serialize(),function(risposta){
            if(risposta == "1") {
                $("div.alert.successo").show();
                setTimeout(function() {
                    location.href = $("#HTTP_REFERER").val();
                },3000);
            }
            else {
                $("div.alert.errore").show();
            }
        });
    });
});