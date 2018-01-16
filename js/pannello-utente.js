$(document).ready(function(){
    $(".tabcontent").first().show();
    $(".tablinks").first().addClass("active");
    $(".tablinks").on("click",function (e) {
       var tabTarget = $(this).attr("data-target");
       $(".tablinks").removeClass("active");
        $(this).addClass("active");
       $(".tabcontent").hide();
       $('#'+tabTarget).show();
    });



    $(window).on("resize",function() {

    });


    //Scheda Prenotazioni
    stileCellaPagamento();
    //richiesta AJAX per la cancellazione di una prenotazione
    $(".btn-cancella").on("click", function () {
        var target = $(this).attr("data-target");
        var data = validaData($('#'+target).find(".giorno").text());
        var timeDiff = data - (new Date());
        var giorniDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        if(giorniDiff<2) {
            alert("NON puoi cancellare");
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
                        action: function () {
                            $.post("php/delete_prenotazione.php",{
                                idPrenotazione: target
                            },function (risposta) {
                                risposta = JSON.parse(risposta);
                                alert(risposta.messaggio);
                                if(risposta.stato == 1) {
                                    //successo
                                    var pari = $('#'+target).parent().nextAll(".pari");
                                    var dispari = $('#'+target).parent().nextAll(".dispari");

                                    $('#'+target).parent().slideUp('Slow', function(){
                                        $(this).remove();
                                    });

                                    dispari.removeClass("dispari").addClass("pari");
                                    pari.removeClass("pari").addClass("dispari");
                                }
                            });
                        }
                    },
                    Annulla:  {
                       
                    }
                }
            });

        }

    });

    //Scheda Account
    $datiForm = salvaDatiForm();

    $(":text").attr('disabled','disabled');

    $(".mostra-modifica").hide();
    $("#modifica").on("click",function () {
        $(".mostra-modifica").slideDown(200);
        $(":text").not('#username').removeAttr('disabled');

    });
    //Modifica dati account
    $("form").on("submit", function (e) {
        //prima di fare il submit controllo la validità dei dati modificati
        if(validaFormUtente()) {
            alert("ok");

        }
        else{
            alert("nope");
            e.preventDefault();
            e.stopPropagation();
        }

    });

    $("#annulla").on("click",function () {
        $(":text").attr('disabled','disabled');
        ripristinaDatiInizialiForm($datiForm);
        $(".mostra-modifica").slideUp(200, function () {
            $(this).hide();
        });
    });
});

function salvaDatiForm(){
    var inputs = $('input').not(':input[type=submit]');
     var datiForm = {};
    $(inputs).each(function () {
        datiForm[$(this).attr("id")] = $(this).val();
    });
    return datiForm;
}

function ripristinaDatiInizialiForm(oggettoDatiForm){
    var inputs = $('input').not(':input[type=submit]');
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

/**
* Funzione che elimina tutti i messaggi di errore dai vari campi del form
*/
function pulisciErrori() {
    var elementi = document.getElementsByClassName("field-container");
    for(var i=0; i<elementi.length; i++) {
        pulisciErrore(elementi[i]);
    }
}

/**
 * Funzione che elimina il messaggio di errore (se esiste) dal div.field-container passato come parametro
 * @param targetElement il div.field-container dal quale rimuovere l'eventuale messaggio di errore
 */
function pulisciErrore(targetElement) {
    if(targetElement.className.match("error")) {
        //Se l'elemento targetNode ha un errore (quindi ha la classe error) la tolgo
        targetElement.className = targetElement.className.replace("error", "");

        //Prendo tutti i figli del div.field-container che sto esaminando e rimuovo lo span
        var figli = targetElement.childNodes;

        for(var i=0; i<figli.length; i++) {
            //Itero sui figli del div.field-container che sto esaminando alla disperata ricerca dello span da rimuovere
            if(figli[i].nodeName.toLowerCase() == "span") {
                //quando l'ho trovato lo rimuovo
                targetElement.removeChild(figli[i]);
            }
        }
    }
}


function calcolaDimensioneDialog() {
    var larghezzaSchermo = $( window ).width();
    return (larghezzaSchermo <= 768) ? "80%" : "20em";
}