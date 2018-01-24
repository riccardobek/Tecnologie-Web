$(function() {
    $(".tabcontent").first().show();
    $(".tablinks").first().addClass("active");
    $(".tablinks").on("click", function (e) {
        var tabTarget = $(this).attr("data-target");
        $(".tablinks").removeClass("active");
        $(this).addClass("active");
        $(".tabcontent").hide();
        $('#' + tabTarget).show();
    });
    toggleMostra();
});

function toggleMostra() {
    $(".rate-button").click(function(){
        $(this).hide();
        $(this).next(".submit-rate").show();
    });
    $(".cancel").click(function(){
        $(this).parent().hide();
        $(this).parent().prev().show();
    });
}


function eliminaPrenotazione(codicePrenotazione) {
    var successo = false;
    $.post("php/delete_prenotazione.php", {idPrenotazione: codicePrenotazione}, function (risposta) {
        risposta = JSON.parse(risposta);
        if(risposta.stato == 1) {
            //successo
            rispostaEliminiazionePrenotazione(codicePrenotazione);
            successo = true;
        }
        else{
            generaAlert('red','Errore',risposta.messaggio);
            successo = false;
        }
        return successo;
    });
}

function eliminaAccount(idUtente = 0) {
    var successo = false;
    $.post("php/delete_account.php",{IDUtente:idUtente}, function(risposta) {
        risposta = JSON.parse(risposta);
        if(risposta.stato == 1) {
            //successo
            successo = true;
        }
        else{
            successo = false;
        }
        return successo;
    });
}