$(function() {

    //------SEZIONE GESTISCI ATTIVITA'--------

    //Nuova Macroattivita
    $("#crea-macro").on("click", function() {
        $("#label-dialog2").text("Nuova macroattività");
        $("#finestra-macro").show();
        $("#finestra-macro input[type=submit]").attr("data-fun","0");

    });

    $("#finestra-macro input[type=submit]").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        var tipoOperazione = $(this).attr("data-fun");
        console.log(tipoOperazione);
        if(validaFormModifica("finestra-macro")) {
            if(tipoOperazione == "0"){
                $.post("php/macroattivita.php", $("#finestra-macro form").serialize() + "&"+"nuovaMacro=1", function (risposta) {
                    try {
                        risposta = JSON.parse(risposta);
                        if (risposta.stato == 1) {
                            generaAlert('green', "Successo", risposta.messaggio);
                        }
                        else {
                            generaAlert('red', "Errore", risposta.messaggio);
                        }
                    }
                    catch(e) {
                        generaAlertErroreGenerico();
                    }
                });
            }
            else{
                $.post("php/macroattivita.php", $("#finestra-macro form").serialize(), function (risposta) {
                    try {
                        risposta = JSON.parse(risposta);
                        if (risposta.stato == 1) {
                            generaAlert('green', "Successo", risposta.messaggio);
                        }
                        else {
                            generaAlert('red', "Errore", risposta.messaggio);
                        }
                    }
                    catch(e) {
                        generaAlertErroreGenerico();
                    }
                });
            }

        }
    });

    $("#annulla-macro").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        $("#label-dialog2").text("Nuova macroattività");
        $("#finestra-macro").fadeOut("Slow",function(){
            pulisciErrori($("#finestra-macro").find(".alert.errore"),$("#finestra-macro").find("form"));
            $(this).find("input[type=text],textarea").val('');
        });

    });

    //Modifica macro attivita
    aggiungiEventiMacroAttivita();



    //bottone nuova attivita
    $(".btn-nuova-attivita").on("click", function () {
        var titoloMacro = $(this).attr("data-info");
        var idMacro = $(this).attr("data-target");
        $("#nuova-attivita h2").prepend("<span>"+titoloMacro+" - </span>");
        $("#nuova-attivita input[type=submit]").attr("data-macro",idMacro);
        $("#nuova-scheda-attivita").show();
        $("#nome").focus();
    });


    $("#nuova-attivita button").on("click",function(e) {
        e.preventDefault();
        e.stopPropagation();
        pulisciErrori($("#nuova-attivita").find(".alert.errore"),$("#nuova-attivita").find("form"));
        fadeDialogoNuovaAttivita();
    });

    //aggiungo listener alle schede attività
    aggiugngiEventiSchedeAttivita();


    $("#nuova-attivita input[type=submit]").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var divDaAggiornare = $(this).next("div");

        if(validaFormModifica("nuova-attivita")) {
            var idMacro = $(this).attr('data-macro');
            $.post("php/modifica_attivita.php", $("#nuova-attivita form").serialize()+"&nuovaAttivita=true"+"&"+"idMacro="+idMacro, function (risposta) {
                try {
                    risposta = JSON.parse(risposta);
                    if (risposta.stato == 1) {
                        var nSchedeModulo = ($("#gruppo-macro-" + risposta.idMacro + " .scheda-wrapper").length) % 2;
                        var classe = "";
                        if (nSchedeModulo == 0)
                            classe = 'pari';
                        else
                            classe = 'dispari';
                        $.alert({
                            boxWidth: calcolaDimensioneDialog(),
                            useBootstrap: false,
                            type: 'green',
                            title: 'Successo',
                            content: risposta.messaggio,
                            buttons: {
                                Ok: {
                                    action: function () {
                                        $.post("pannello_admin.php",
                                            $("#nuova-attivita form").serialize() + "&nuovaScheda=1" + "&" + "Classe=" + classe + "&" + "Codice=" + risposta.CodiceAtt,
                                            function (ris) {
                                                $(ris).insertBefore($("#gruppo-macro-" + risposta.idMacro + ' ' + ".inserimento-scheda"));
                                                togliEventiSchedeAttivita();
                                                fadeDialogoNuovaAttivita();
                                                aggiugngiEventiSchedeAttivita();
                                            });
                                    }
                                }
                            }
                        });
                    }
                    else {
                        if (risposta.hasOwnProperty('Tipo')) {
                            notificaErrore($("#nuova-attivita #nome"), risposta.messaggio);
                        }
                        else {
                            generaAlert('red', "Errore", risposta.messaggio);
                        }
                    }
                }
                catch(e) {
                    generaAlertErroreGenerico();
                }
            });
        }
    });


    //--------SEZIONE GESTISCI UTENTI---------
    //Eliminazione di un account
    $("#usr-manager .btn-cancella").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var target = $(this).attr('data-target');
        $.confirm({
            boxWidth: calcolaDimensioneDialog(),
            useBootstrap: false,
            title: 'Conferma',
            content: "Procedere con l'eliminazione dell'account? ",
            buttons: {
                Procedi: {
                    btnClass: 'btn-red',
                    action: function () {
                        eliminaAccount(target);
                    }
                },
                Annulla:{}
            }
        });
    });

    //reimposta password
    $("#usr-manager .btn-reimposta").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var target = $(this).attr('data-target');
        var userName = $('#'+target).children(".username").text();
        $.confirm({
            boxWidth: calcolaDimensioneDialog(),
            useBootstrap: false,
            title: 'Conferma',
            content: "Procedere con il reset della password dell'account: "+userName+"?",
            buttons: {
                Procedi: {
                    btnClass: 'btn-blue',
                    action: function () {
                        $.post("php/modifica_dati_utente.php",{IDUtente:target},function (risposta) {
                            try {
                                risposta = JSON.parse(risposta);
                                if (risposta.stato == 1) {
                                    generaAlert('green', 'Successo', risposta.messaggio);
                                }
                                else {
                                    generaAlert('red', 'Errore', risposta.messaggio);
                                }
                            }
                            catch(e) {
                                generaAlertErroreGenerico();
                            }
                        });
                    }
                },
                Annulla:{}
            }
        });
    });


    //----SEZIONE GESTISCI PRENOTAZIONI----
    //Cancella prenotazione
    $("#res-manager .btn-cancella").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var target = $(this).attr('data-target');
        $.confirm({
            boxWidth: calcolaDimensioneDialog(),
            useBootstrap: false,
            title: 'Conferma',
            content: "Procedere con l'eliminazione della prenotazione ? ",
            buttons: {
                Procedi: {
                    btnClass: 'btn-red',
                    action: function () {
                        eliminaPrenotazione(target);
                    }
                },
                Annulla:{}
            }
        });
    });
    /*
    var etichette = [];
    var valoriGrafico = [];

    $(".numero-prenotazioni").each(function(){
        etichette.push($(this).data("target"));
        valoriGrafico.push(parseInt($(this).text()));
    });

    //Statistiche
    var data = {
        labels: etichette,
        series: valoriGrafico
    };

    new Chartist.Pie('.ct-chart', data);
    */
    $(".pay").click(function (e){
        e.preventDefault();
        e.stopPropagation();
        var target =  $(this).attr("data-target");
        var bottoneCliccato = $(this);

        $.post("pannello_admin.php", {confermaPagamento:"1", codicePrenotazione:target}, function (risposta) {
            try {
                risposta = JSON.parse(risposta);
                if (risposta.stato == 1) {
                    generaAlert('green', 'Pagamento effettuato', risposta.messaggio);
                    var rigaTabella = bottoneCliccato.parent();
                    bottoneCliccato.remove();
                    rigaTabella.text("Pagamento effettuato");
                }
                else {
                    generaAlert('red', 'Errore', risposta.messaggio);
                }
            }
            catch(e) {
                generaAlertErroreGenerico();
            }
        });
    });
});


function eliminaRigaTabella(target) {
    $('#'+target).slideUp('Slow', function () {
        $('#'+target).remove();
    });
}

function rispostaEliminiazionePrenotazione(target) {
    eliminaRigaTabella(target);
}

//funzione che permette di salvare i dati dei form delle varie schede attività
//
function salvaDati(target) {
     var campiDati = $("#"+target).find("input[type=text], textarea");
     var datiSalvati = {};
     $(campiDati).each(function () {
         datiSalvati[$(this).attr("class")] = $(this).val();
     });
     return datiSalvati;
}

//funzione che notifica gli errori nei vari campi dati del form di modifica delle attività
function validaFormModifica(target) {
    var valido = true;
    var targetSelector = $("#"+target);
    var divAlert = targetSelector.find(".alert.errore");
    var formErr = targetSelector.find("form");
    var inputs = targetSelector.find("textarea,input[type=text]");
    pulisciErrori(divAlert, formErr);
    $(inputs).each(function () {
        if($(this).val().trim().length == 0) {
            notificaErrore($(this),"Il campo "+' '+$(this).attr("name")+' '+" non può essere vuoto",divAlert, formErr );
            valido = false;
        }
    });
    return valido;
}


function aggiugngiEventiSchedeAttivita() {

    //Disabilito gli input dei vari form delle schede attività tranne gli input della dialog pre creare una nuova attività
    $(".schede-attivita").find("input[type=text], textarea").attr('disabled','disabled');

    //event listener per il bottone elimina attività
    $(".elimina-attivita").on("click", function () {
        //prendo l'attributo data target per sapere quale scheda eliminare
        var idScheda = $(this).attr("data-target");
        //finestra di dialogo con ri chiesta AJAX
        //al successo dell'eliminazione rimuovo la scheda
        sistemaSchede(idScheda);
    });

    //array associativo per il vari campi dati delle varie schede
    var campiDati = {};
    //Quando si preme il tasto modifica i campi di testo vengono abilitati e si mostra il bottone di annulamento delle modifiche
    $(".schede .modifica").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();

        $(this).hide();
        $(this).prev().show();
        //mostro il pulsante annulla modifiche
        $(this).next().show();

        //seleziono l'id del div del pulsante premuto
        var target = $(this).attr('data-target');
        $("#"+target).find("textarea,input[type=text]").removeAttr('disabled');

        //salvo i dati dei vari campi
        campiDati[target] = salvaDati(target);
    });

    //listener per tasto cancella modifiche di un' attività
    $(".schede .bottone-annulla").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        $(this).hide();
        $(this).prevAll(".salva-dati").hide();
        $(this).prev().show();
        //elimino le notifiche di errore
        var formPadre = $(this).parent().parent();
        var divAlert = formPadre.parent().find(".alert.errore");

        pulisciErrori(divAlert,formPadre);
        //ripristino dati
        var target = $(this).attr('data-target');
        $("#nome-"+target).val(campiDati[target]["nome-attivita"]);
        $("#descrizione-"+target).val(campiDati[target]["descrizione"]);
        $("#prezzo-"+target).val(campiDati[target]["prezzo"]);

        //disabilito i campi di testo
        $("#"+target).find("textarea,input[type=text]").attr('disabled','disabled');
    });

    $(".salva-dati").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var target = $(this).attr('data-target');
        if(validaFormModifica(target)) {
            $.post("php/modifica_attivita.php",$("#"+target).find("form").serialize()+"&"+"idAttivita="+target, function(risposta) {
                try {
                    risposta = JSON.parse(risposta);
                    if(risposta.stato== 1) {
                        campiDati[target] = salvaDati(target);
                        generaAlert('green',"Successo",risposta.messaggio);
                    }
                    else {
                        generaAlert('red',"Errore",risposta.messaggio);
                    }
                }
                catch(e) {
                    generaAlertErroreGenerico();
                }
            });
        }
    });
}


function togliEventiSchedeAttivita() {
    $(".elimina-attivita").off("click");
    $(".salva-dati").off("click");
    $(".schede .modifica").off("click");
    $(".schede .bottone-annulla").off("click");
}

function fadeDialogoNuovaAttivita() {
    $("#nuova-scheda-attivita").fadeOut('Slow', function () {
        $("#nuova-attivita").find("input[type=text],textarea").val('');
        $("#nuova-attivita h2 span").remove();
    });
}

//lo scopo di questa funzione è di utilizzare la stessa finestra dialogo per la creazione e per la modifica di una macro attività
//il parametro tipo indica quale funzione si vuole utilizzare: creazione o modifica
function aggiungiEventiMacroAttivita() {
    $(".mod-macro").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        var idMacro = $(this).parent().attr("data-target");
        $.post("pannello_admin.php", {RichiestaMacro: idMacro}, function(macro) {
            try {
                macro = JSON.parse(macro);
                $("#label-dialog2").text(macro.Nome + " - Modifica");
                $("#nome-macro").val(macro.Nome);
                $("#descrizione-macro").val(macro.Descrizione);
                $("#finestra-macro").show();
                $("#finestra-macro input[type=submit]").attr("data-fun", "1");
            }
            catch(e) {
                generaAlertErroreGenerico();
            }
        });
    });
}

//blocca lo scroll se premo il tasto crea nuova macroattività
function bloccaScroll(){
    $("body").css({"overflow" : "hidden"});
}
//sblocca lo scroll se premo annulla
function sbloccaScroll(){
    $("body").css({"overflow":"scroll"});
}