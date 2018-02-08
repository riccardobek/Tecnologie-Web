$(function() {
    //Scheda Prenotazioni
    stileCellaPagamento();

    assegnaVoto();
    stampaStorico();

    //richiesta AJAX per la cancellazione di una prenotazione
    $(".button-holder > .btn-cancella").on("click", function () {
        var target = $(this).attr("data-target");
        var data = validaData($('#'+target).find(".giorno").text());
        var timeDiff = data - (new Date());
        var giorniDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        if(giorniDiff<2) {
            generaAlert('red','Errore',"Non puoi cancellare la prenotazione con due giorni di anticipo.");
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

    $("#annulla-modifica-pwd").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".mostra-modifica-password").hide(function () {
            //se ho generato in precedenza lo span di successo lo elimino, se non c'è non succede nulla
            $("#modifica").show();
            $('#successo').remove();
            $(labelPassword).text(testoModificaPwd);
            $("input[type=password]").val('');
            $(".mostra-modifica").show();
        });
    });



    //Modifica dati account
    $("#invio-dati").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".error").each(function () {
            pulisciErrore($(this));
        });

        if(validaFormUtente(false)) {
            $.post("php/modifica_dati_utente.php",$("form").serialize(),function(risposta) {
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

    $("#annulla").on("click",function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(":text, :password").attr('disabled','disabled');
        ripristinaDatiInizialiForm(datiForm);
        $(".error").each(function () {
            pulisciErrore($(this));
        });
        $(".mostra-modifica").slideUp(200, function () {
            $(this).hide();
        });
    });

    //Eliminazione account
    $("#elimina-account").on("click", function () {
        $.confirm({
            boxWidth: calcolaDimensioneDialog(),
            useBootstrap: false,
            title: 'Conferma',
            content: "Procedere con l'eliminazione dell'account? ",
            buttons: {
                Procedi:{
                    btnClass: 'btn-red',
                    action: function(){
                        $.confirm({
                            boxWidth: calcolaDimensioneDialog(),
                            useBootstrap: false,
                            title: 'Conferma',
                            content: "Dopo l'eliminazione verrà automaticamente effettuato il logout e il tuo account sarà eliminato. Confermi di voler di eliminare l'account?",
                            buttons: {
                                eliminaAccount: {
                                    text: 'Elimina Account',
                                    btnClass: 'btn-red',
                                    action: function () {eliminaAccount();}
                                },
                                Annulla: {}
                            }
                        });
                    }
                },
                Annulla: {}
            }
        });
    });
});

/****** FUNZIONI *******/
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
    sistemaSchede(target);
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

function salvaDatiForm(){
    var inputs = $("input").not(":input[type=submit], :input[type=password]");
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

function stampaStorico() {
    $("#stampa-storico").on("click" ,function () {
        $(".schede-prenotazioni").hide();
        $("#storico").show();
        window.print();
        $(".schede-prenotazioni").show();
    });
}

function controlloBottone() {
    if($(".scheda-wrapper").length==0){
        $("#stampa-schede").hide();
        $("<h2>Non ci sono prenotazioni attive</h2>").insertAfter($("#prenotazioni h1"));
    }

}