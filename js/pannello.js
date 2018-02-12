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

function stileCellaPagamento(){
    $(".pagamento").each(function () {
        if( $(this).text() === "Non pagato")
            $(this).css("color","#A80000");
        else
            $(this).css("color","#0F6600");
    });
}

function eliminaPrenotazione(codicePrenotazione) {
    $.post("php/delete_prenotazione.php", {idPrenotazione: codicePrenotazione}, function (risposta) {
        try {
            risposta = JSON.parse(risposta);
            if(risposta.stato == 1) {
                //successo
                rispostaEliminiazionePrenotazione(codicePrenotazione);
            }
            else{
                generaAlert('red','Errore',risposta.messaggio);
            }
        }
        catch(e) {
            generaAlertErroreGenerico();
        }

    });
}



function eliminaAccount(idUtente) {
    if(idUtente == undefined) idUtente = 0;

    var id = idUtente.replace("utente-","");
    $.post("php/delete_account.php",{IDUtente:idUtente}, function(risposta) {
        try{
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
                    return true;
                }
                else {
                    //elimino nel pannello admin tutti gli elemente che si riferiscono all'utente eliminato
                   $("[data-user='"+id+"']").each(function() {
                       $(this).remove();
                   });
                    eliminaRigaTabella(idUtente);
                    generaAlert('green','Successo',risposta.messaggio);
                    return true;
                }
            }
            else{
                generaAlert('red','Errore',risposta.messaggio);
                return false;
            }
        }
        catch(e) {
            generaAlertErroreGenerico();
        }
    });
}

//salva i dati dei form
function salvaDatiForm(target){
    var inputs = $("#"+target+' '+"input,textarea").not(":input[type=submit], :input[type=password]");
    var datiForm = {};
    $(inputs).each(function () {
        datiForm[$(this).attr("id")] = $(this).val();
    });
    return datiForm;
}

function sistemaSchede(target) {
    var pari = $('#'+target).nextAll(".pari");
    var dispari = $('#'+target).nextAll(".dispari");

    var gruppoSchede = $("#"+target).parent();

    $('#'+target).slideUp('Slow', function(){
        $(this).remove();

        if(gruppoSchede.find(".separatore").length > 0 ) {
            gruppoSchede.parent().find(".separatore").remove();

            var elementiPerRiga = 0;
            gruppoSchede.children().each(function() {
                elementiPerRiga++;
                if(elementiPerRiga == 2) {
                    $(this).after($("<div class='separatore'></div>"));
                    elementiPerRiga = 0;
                }
            });
        }
    });
    dispari.removeClass("dispari").addClass("pari");
    pari.removeClass("pari").addClass("dispari");
}