$(document).ready(function() {
    $("form").on("submit",function(event) {
        event.preventDefault();
        event.stopPropagation();

        $("div.alert").hide();

        $.post("php/do_login.php",$("form").serialize(),function(risposta){
            if(risposta == "1") {
                var redirectURL = $("#HTTP_REFERER").val();
                
                if(window.location == redirectURL) redirectURL = "index.php";

                $("div.alert.successo > a").attr("href",redirectURL);

                $("div.alert.successo").show();
                setTimeout(function() {
                    location.href = redirectURL;
                },2500);
            }
            else {
                $("div.alert.errore").show();
            }
        });
    });
});