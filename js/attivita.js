$(function () {
    $(".attivita > a").on("click",function(event) {

        var form = $(event.target).parent().children(".form-prenotazione-container");

        if(!$(event.target).data("espanso")) {
            form.slideToggle();
            $(event.target).data("espanso",true);
        }
        else {
            var inputData = form.find("input.data");
            var inputPosti = form.find("input.posti");

            pulisciErrore(inputData.parent()[0].parentNode);
            pulisciErrore(inputPosti[0].parentNode);

            console.log("Data da validare: "+inputData.val());

            if(!validaData(inputData.val())) {
                notificaErrore(inputData.parent()[0].parentNode, "Inserire una data valida");
                return;
            }

            if(inputPosti.val().length == 0 || isNaN(inputPosti.val()) || parseInt(inputPosti.val())  <= 0) {
                notificaErrore(inputPosti[0].parentNode, "Inserire un numero di posti valido (maggiore o uguale a uno)");
                return;
            }

            console.log("Invio i seguenti dati:");

            $.post("php/do_prenotazione.php", {
                attivita: form.find("input[name='attivita']").val(),
                data: form.find(".data").val(),
                posti: form.find(".posti").val()
            }, function (r) {
                risposta = JSON.parse(r);
                if(risposta.stato === 1) {
                    alert("Prenotazione inserita con successo");
                } else {
                    alert("Errore nell'inserimento della prenotazione: \n\n"+risposta.messaggio)
                }
                console.log("Risposta dalla pagina di inserimento della prenotazione: "+r);
            });
        }
    });

    $("input.posti").on("input",function(event){
        var totale = parseInt($(event.target).val()) * parseInt($(event.target).parent().parent().children(".prezzo-unitario").val());

        if(isNaN(totale)) totale=""; //Se il totale non è un mumero (ad esempio il valore del campo "quantita" è vuoto allora mostro un valore vuoto
        $(event.target).parent().parent().find("span.totale").text(totale);
    });

    $("input.data").asDatepicker();

    /*
    $("input.data").on("focusout",function (event) {
        if(!validaData($(event.target).val()))
            notificaErrore(event.target.parentNode,"Inserire una data valida");
    });*/

});

/*
* Funzione presa da:
* https://stackoverflow.com/questions/9062400/javascript-date-validation-for-mm-dd-yyyy-format-in-asp-net
 */
function validaData(d) {
    var match = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(d);
    if (!match) {
        // La data non è nel formato corretto
        return false;
    }
    match = d.split("/");

    var giorno   = parseInt(match[0], 10);
    var mese = parseInt(match[1], 10) - 1; // i mesi sono nell'intervallo 0-11, non 1-12
    var anno  = parseInt(match[2], 10);
    var date  = new Date(anno, mese, giorno);

    /* La funzione Date accetta qualsiasi parametro come anno, mese, giorno e lo converte
    * in una data valida. Quindi basta confrontare i valori del giorno, mese, anno in input
    * con quelli generati dall'oggetto date */
    return date.getDate() == giorno && date.getMonth() == mese && date.getFullYear() == anno;
}
