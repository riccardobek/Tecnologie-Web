$(function() {


    //------SEZIONE GESTISCI ATTIVITA'--------
    //Disabilito gli input dei vari form delle schede attività
    $("input[type=text], textarea").attr('disabled','disabled');

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

    //listener per tasto cancella modifiche ad una attività
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
        console.log("#nome-"+target);
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
        if(validaFormModifica(target)){
            $.post("php/modifica_attivita.php",$("#"+target).find("form").serialize()+"&"+"idAttivita="+target, function(risposta) {
                risposta = JSON.parse(risposta);
                if(risposta.stato == 1){
                    campiDati[target] = salvaDati(target);
                    generaAlert('green',"Successo",risposta.messaggio);
                }
                else{
                    generaAlert('red',"Errore",risposta.messaggio);
                }
            });
        }
        else{

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
    if($("#nome-"+target).val().trim().length == 0){
        notificaErrore($("#nome-"+target).parent(),"Il campo non può essere vuoto");
        valido = false;
    }
    if($("#descrizione-"+target).val().trim().length == 0){
        notificaErrore($("#descrizione-"+target).parent(),"Il campo non può essere vuoto");
        valido = false;
    }
    if($("#prezzo-"+target).val().trim().length == 0){
        notificaErrore($("#prezzo-"+target).parent(),"Il campo non può essere vuoto");
        valido = false;
    }

}
