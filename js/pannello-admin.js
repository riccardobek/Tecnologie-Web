$(function() {

    //------SEZIONE GESTISCI ATTIVITA'--------

    //Macroattivita
    $("#crea-macro").on("click", function(){
        $("#finestra-crea-macro").show();
    });

    $("#finestra-crea-macro #annulla-macro").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        $("#finestra-crea-macro").fadeOut("Slow",function(){
            $(this).find("input[type=text],textarea").val('');
        });
        $(".error").each(function () {
            pulisciErrore($(this));
        });
    });

    $("#finestra-crea-macro input[type=submit]").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        if(validaFormModifica("finestra-crea-macro")){
            $.post("php/macroattivita.php", $("#finestra-crea-macro form").serialize()+"&nuovaMacro=true",function(risposta){
                risposta = JSON.parse(risposta);
                if(risposta.stato == 1) {
                    generaAlert('green',"Successo",risposta.messaggio);
                }
                else {
                    generaAlert('red',"Errore",risposta.messaggio);
                }
            });
        }
    });

    $("#mod-macro").on("click", function(){
        $("#finestra-crea-macro").show();
    });


    //bottone nuova attivita
    $(".btn-nuova-attivita").on("click", function () {
        var titoloMacro = $(this).attr("data-info");
        var idMacro = $(this).attr("id");
        $("#nuova-attivita h2").prepend("<span>"+titoloMacro+" - </span>");
        $("#nuova-scheda-attivita").show();
        $("#nome").focus();
    });

    //Disabilito gli input dei vari form delle schede attività tranne gli input della dialog pre creare una nuova attività
    $(".schede-attivita").find("input[type=text], textarea").attr('disabled','disabled');

    $("#nuova-attivita button").on("click",function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(".error").each(function () {
            pulisciErrore($(this));
        });
        $("#nuova-attivita").find("input[type=text],textarea").val('');
        $(".overlay").fadeOut('Slow', function () {
            $("#nuova-attivita h2 span").remove();
            $("#macro").remove();
        });
    });

    //bottone elimina attività
    $(".elimina-attivita").on("click", function () {

        //prendo l'attributo data target per sapere quale scheda eliminare
        var idScheda = $(this).attr("data-target");
        //finestra di dialogo con ri chiesta AJAX
        //al successo dell'eliminazione rimuovo la scheda
        sistemaSchede(idScheda);
    });

    $("#nuova-attivita input[type=submit]").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var divDaAggiornare = $(this).next("div");

        if(validaFormModifica("nuova-attivita")) {
            var idMacro = $("#macro").text();
            $.post("php/modifica_attivita.php", $("#nuova-attivita form").serialize()+"&nuovaAttivita=true"+"&"+"idMacro="+idMacro, function (risposta) {
                risposta = JSON.parse(risposta);
                if(risposta.stato == 1) {
                    var nSchedeModulo = ($("#gruppo-macro-"+idMacro+" .scheda-wrapper").length)%2;
                    var classe = "";
                    if(nSchedeModulo == 0)
                        classe = 'pari';
                    else
                        classe = 'dispari';
                    $.alert( {
                        boxWidth: calcolaDimensioneDialog(),
                        useBootstrap: false,
                        type: 'green',
                        title: 'Successo',
                        content: risposta.messaggio,
                        buttons: {
                            Ok: {
                                action: function () {
                                    $(".overlay").fadeOut('Slow', function () {
                                        $("#nuova-attivita h2 span").remove();
                                        $("#macro").remove();
                                    });
                                    $.post("pannello_admin.php",
                                        $("#nuova-attivita form").serialize()+"&nuovaScheda=1"+"&"+"Classe="+classe+"&"+"Codice="+risposta.CodiceAtt,
                                        function(ris) {
                                            $(ris).insertBefore($("#gruppo-macro-"+risposta.idMacro+" .clearfix"));
                                    });
                                }
                            }
                        }
                    });
                }
                else {
                    if(risposta.hasOwnProperty('Tipo')) {
                        notificaErrore($("#nuova-attivita #nome").parent(),risposta.messaggio);


                    }
                    else {
                        generaAlert('red',"Errore",risposta.messaggio);
                    }
                }
            });
        }
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
        $(".error").each(function () {
            pulisciErrore($(this));
        });

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
                risposta = JSON.parse(risposta);
                if(risposta.stato == 1) {
                    campiDati[target] = salvaDati(target);
                    generaAlert('green',"Successo",risposta.messaggio);
                }
                else {
                    generaAlert('red',"Errore",risposta.messaggio);
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
                            risposta = JSON.parse(risposta);
                            if(risposta.stato == 1) {
                                generaAlert('green', 'Successo', risposta.messaggio);
                            }
                            else{
                                generaAlert('red', 'Errore', risposta.messaggio);
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
    var inputs = $("#"+target).find("textarea,input[type=text]");
    $(".error").each(function () {
        pulisciErrore($(this));
    });
    $(inputs).each(function () {
        if($(this).val().trim().length == 0){
            notificaErrore($(this).parent(),"Il campo "+' '+$(this).attr("name")+' '+" non può essere vuoto");
            valido = false;
        }
    });
    return valido;
}

function toggleEventi() {}