/**
 * Funzione che valida il form di registrazione ed, eventualmente (con return false) ne interrompe il submit
 * @returns {boolean}
 */

$(document).ready(function() {
    $("#JSAbilitato").val("1");

    $("form").on("submit",function(event){
        pulisciErrori();
        event.preventDefault();
        if(validaFormUtente(true)) {
            $.post($("form").attr("action"),$("form").serialize(),function(r) {
                rispostaJSON = JSON.parse(r);
                if(rispostaJSON.stato === 1)
                    $(".alert.successo").show();
                else {
                   $(".alert.errore").show();
                }
            });
        }
    });
});
