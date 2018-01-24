$(function() {
    //Scheda Prenotazioni
    stileCellaPagamento();

    assegnaVoto();

    //richiesta AJAX per la cancellazione di una prenotazione
    $(".button-holder > .btn-cancella").on("click", function () {
        var target = $(this).attr("data-target");
        var data = validaData($('#'+target).find(".giorno").text());
        var timeDiff = data - (new Date());
        var giorniDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        if(giorniDiff<2) {
           generaAlert('red','Eroore',"Non puoi cancellare la prenotazione con 2 giorni di anticipo.");
        }
        else {
            $.confirm({
                boxWidth: calcolaDimensioneDialog(),
                useBootstrap: false,
                type: 'blue',
                title: 'Conferma',
                content: "Procedere con l'eliminazione della prenotazione?",
                buttons: {
                    Procedi:{
                        btnClass: 'btn-blue',
                        action: function(){
                            eliminaPrenotazione(target);
                        }
                    },
                    Annulla:  {}
                }
            });
        }
    });

    //Scheda Account
    var datiForm = salvaDatiForm();

    $("input[type=text], input[type=password]").attr('disabled','disabled');

    $(".mostra-modifica, .mostra-modifica-password").hide();

    $("#modifica").on("click", function () {
        $(".mostra-modifica").slideDown(200);
        $(":text, :password").not('#username').removeAttr('disabled');
    });

    //cambio password
    var labelPassword = $("label[for='vecchia-password']");
    var testoModificaPwd = "Modifica password:";

    $("#vecchia-password").on("focus", function () {
        $("#modifica").hide();
        $(labelPassword).text("Password corrente: ");
        //se ho generato in precedenza lo span di successo lo elimino, se non c'è non succede nulla
        $('#successo').remove();
        $(".mostra-modifica-password").show();
        //nascondo il tasto modifica alla fine del form per evitare confusione
        $(".mostra-modifica").hide();
    });

    $("#bottone-modifica-password").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //controllo se le password combaciano
        $(".mostra-modifica").show();
        if(validaCampiCambioPwd()) {
            //le password vanno bene faccio un richiesta di modifica della pwd
            $.post($("form").attr("action"), {VecchiaPwd: $("#vecchia-password").val(), NuovaPwd: $("#password").val()}, function (risposta) {
                risposta = JSON.parse(risposta);
                if(risposta.stato == 1) {
                    $(labelPassword).text(testoModificaPwd);
                    $(".mostra-modifica-password").hide();
                    //se ho generato in precedenza lo span di successo lo elimino, se non c'è non succede nulla
                    $('#successo').remove();
                    $("#vecchia-password").parent().append("<span class='successo'>"+risposta.messaggio+"</span>");
                }
                else{
                   notificaErrore($("#vecchia-password").parent(), risposta.messaggio);
                }
            });
        }
    });

    $("#annulla-modifica-pwd").on("click", function () {
        $(".mostra-modifica-password").hide(function () {
            //se ho generato in precedenza lo span di successo lo elimino, se non c'è non succede nulla
            $("#modifica").show();
            $('#successo').remove();
            $(labelPassword).text(testoModificaPwd);
            $("input[type=password]").val('');
            $(".mostra-modifica").show();
        });
    });

    //annulla inserimento dati form
    $("#annulla").on("click",function () {
        $(":text, :password").attr('disabled','disabled');
        ripristinaDatiInizialiForm(datiForm);
        $(".error").each(function () {
            pulisciErrore($(this));
        });
        $(".mostra-modifica").slideUp(200, function () {
            $(this).hide();
        });
    });

    //Modifica dati account
    $("form").on("submit", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".error").each(function () {
           pulisciErrore($(this));
        });

        if(validaFormUtente(false)) {
           $.post($("form").attr("action"),$("form").serialize(),function(risposta) {
                risposta = JSON.parse(risposta);
                if(risposta.stato == 1){
                    generaAlert('green','Successo',risposta.messaggio);
                    datiForm = salvaDatiForm();
                }
                else{
                    notificaErrore($("#email").parent(),risposta.messaggio);
                }

            });
        }
    });
});


/****** FUNZIONI *******/

function salvaDatiForm(){
    var inputs = $('input').not(":input[type=submit], :input[type=password]");
     var datiForm = {};
    $(inputs).each(function () {
        datiForm[$(this).attr("id")] = $(this).val();
    });
    return datiForm;
}

function ripristinaDatiInizialiForm(oggettoDatiForm ){
    var inputs = $('input').not(":input[type=submit], :input[type=password]");
    $("input[type=password]").val('');
    $(inputs).each(function () {
        $(this).val(oggettoDatiForm[$(this).attr("id")]);
    });
}

function stileCellaPagamento(){
    $(".pagamento").each(function () {
        if( $(this).text() === "Non pagato")
            $(this).css("color","#B80000");
        else
            $(this).css("color","#34ba49");
  });
}


function calcolaDimensioneDialog() {
    var larghezzaSchermo = $( window ).width();
    return (larghezzaSchermo <= 768) ? "80%" : "20em";
}

function assegnaVoto(){
    $(".accept").click(function(){
        var output=$(this).prev().find("option:selected").text();
        var idPrenotazione = $(this).attr("id");
        $.post("pannello_utente.php", {voto: output, codicePren: idPrenotazione, funzione: 1}, function (risposta) {
            risposta = JSON.parse(risposta);
            if(risposta.stato == 1) {
                generaAlert('green','Valutazione effettuata',risposta.messaggio);
            }
            else{
                generaAlert('red','Errore',risposta.messaggio);
            }
        });
    });
}


function rispostaEliminiazionePrenotazione(target) {
    var pari = $('#'+target).parent().nextAll(".pari");
    var dispari = $('#'+target).parent().nextAll(".dispari");

    $('#'+target).parent().slideUp('Slow', function(){
        $(this).remove();
    });
    dispari.removeClass("dispari").addClass("pari");
    pari.removeClass("pari").addClass("dispari");
}


function validaCampiCambioPwd(){
    var campiValidi = true;

    var vecchiaPwd = $("#vecchia-password");
    var password = $("#password");
    var password2 = $("#password2");


    if(vecchiaPwd.val().trim().length == 0) {
        notificaErrore(vecchiaPwd.parent(), "Inserire la password corrente");
        campiValidi = false;
    }

    if(!validaPassword(password,password2))
        campiValidi = false;

    return campiValidi;
}




