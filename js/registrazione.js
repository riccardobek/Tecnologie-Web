/**
 * Funzione che valida il form di registrazione ed, eventualmente (con return false) ne interrompe il submit
 * @returns {boolean}
 */

$(document).ready(function() {
    $("#JSAbilitato").val("1");

    $("form").on("submit",function(event){
        $(".alert").hide();
        event.preventDefault();
        if(validaForm()) {
            $.post($("form").attr("action"),$("form").serialize(),function(r) {
                rispostaJSON = JSON.parse(r);
                if(rispostaJSON.stato === 1)
                    $(".alert.successo").show();
                else {
                    $(".alert.errore").show().text(rispostaJSON.messaggio);
                }
            });
        }
    });
});


function validaForm() {
    pulisciErrori();

    var formValido = true;

    var email = document.getElementById("email");
    //espressione regolare che valida un'email a grandi linee. Presa da
    //https://stackoverflow.com/questions/46155/how-to-validate-email-address-in-javascript quarta risposta
    if (/[^\s@]+@[^\s@]+\.[^\s@]+/.test(email.value.trim()) == false) {
        notificaErrore(email.parentNode, "Inserire un'email valida");
        formValido = false;
    }

    var username = document.getElementById("username");
    if (username.value.trim().length == 0) {
        notificaErrore(username.parentNode, "Inserire uno username valido");
        formValido = false;
    }

    var password = document.getElementById("password");
    var password2 = document.getElementById("password2");

    if (password.value.trim().length == 0) {
        notificaErrore(password.parentNode, "Inserire una password valida");
        formValido = false;
    }
    else if (password2.value.trim().length == 0) {
        notificaErrore(password2.parentNode, "Si prega di ripetere la password");
        formValido = false;
    }
    else if (password.value != password2.value) {
        notificaErrore(password2.parentNode, "Le password non combaciano");
        formValido = false;
    }

    return formValido;
}