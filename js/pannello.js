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
    $.post("php/delete_prenotazione.php", {idPrenotazione: codicePrenotazione}, function (risposta) {
        risposta = JSON.parse(risposta);
        if(risposta.stato == 1) {
            //successo
            rispostaEliminiazionePrenotazione(codicePrenotazione);
        }
        else{
            generaAlert('red','Errore',risposta.messaggio);
        }
    });
}

function eliminaAccount(idUtente = 0) {
    $.post("php/delete_account.php",{IDUtente:idUtente}, function(risposta) {
        risposta = JSON.parse(risposta);
        if(risposta.stato == 1) {
           //richiesta di eliminazione dal pannello utente, mostro un dialog diverso e reindirizzo alla home
            if(idUtente == 0){
                var testo = risposta.messaggio+'. Verrai reindirizzato alla pagina principale.';
                $.alert({
                    boxWidth: calcolaDimensioneDialog(),
                    useBootstrap: false,
                    title:'Successo',
                    type:'green',
                    content: testo,
                    buttons:{
                        OK:{ action: function () {
                                setTimeout(function() {
                                    location.href ="php/do_logout.php";
                                },1000);
                            }
                        }
                    }
                });
            }
            //richiesta di eliminazione dal pannello admin bisogna solo mostrare un dialog
            else{
                eliminaRigaTabella(idUtente);
                generaAlert('green','Successo',risposta.messaggio);
            }
            return true;
        }
        else{
            generaAlert('red','Errore',risposta.messaggio);
            return false;
        }
    });

}