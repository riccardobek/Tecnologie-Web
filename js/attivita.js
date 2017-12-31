$(function () {
    $(".attivita > a").on("click",function(event) {
        if(!$(event.target).data("espanso")) {
            $(event.target).parent().children(".form-prenotazione-container").slideToggle();
            $(event.target).data("espanso",true);
        }
        else
            $.post("php/do_prenotazione.php",$(event.target).parent().find("form").serialize(), function(r) {
                console.log(r);
            });
    });

    $("input.quantita").on("input",function(event){
        var totale = parseInt($(event.target).val()) * parseInt($(event.target).parent().parent().children(".prezzo-unitario").val());

        if(isNaN(totale)) totale=""; //Se il totale non è un mumero (ad esempio il valore del campo "quantita" è vuoto allora mostro un valore vuoto
        $(event.target).parent().parent().find("span.totale").text(totale);
    });

    $("input.data").on("focusout",function (event) {
        if(!validaData($(event.target).val()))
            notificaErrore(event.target.parentNode,"Inserire una data valida");
    });
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

    var giorno   = parseInt(match[3], 10);
    var mese = parseInt(match[2], 10) - 1; // i mesi sono nell'intervallo 0-11, non 1-12
    var anno  = parseInt(match[3], 10);
    var date  = new Date(anno, mese, giorno);

    /* La funzione Date accetta qualsiasi parametro come anno, mese, giorno e lo converte
    * in una data valida. Quindi basta confrontare i valori del giorno, mese, anno in input
    * con quelli generati dall'oggetto date */
    return date.getDate() == day && date.getMonth() == month && date.getFullYear() == year;
}
