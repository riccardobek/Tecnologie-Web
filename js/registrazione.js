$(document).ready(function() {
    $("#JSAbilitato").val("1");

    $("form").on("submit",function(event){
        //pulisciErrori($(".alert.errore"),$("form"));
        event.preventDefault();
        if(validaFormUtente(true)) {
            $.post($("form").attr("action"),$("form").serialize(),function(r) {
                rispostaJSON = JSON.parse(r);
                if(rispostaJSON.stato === 1)
                    $(".alert.successo").show();
                else {
                    notificaErrore(null,rispostaJSON.messaggio,$(".alert.errore"),$("form"));
                }
            });
        }
    });
});
