$(function () {
    $(".form-prenotazione-container").hide();

    $(".chiudi-form-prenotazione").on("click", function () {
        $(this).prev().data("espanso",false);
        $(this).prev().prev().slideUp();
        $(this).hide();
        $(this).prev().show();
    });

    $(".attivita > .btn.btn-primary").on("click",function(event) {
        $(this).hide();
        var formContainer = $(event.target).parent().children(".form-prenotazione-container");


        if(!$(event.target).data("espanso")) {
            formContainer.slideDown();
            $(event.target).next().show();
            $(event.target).data("espanso",true);
        }
        else {
            var inputData = formContainer.find("input.data");
            var inputPosti = formContainer.find("input.posti");

            pulisciErrore(inputData.parent().parent());
            pulisciErrore(inputPosti.parent());

            var data = validaData(inputData.val());
            if(!data) {
                notificaErrore(inputData.parent().parent(), "Inserire una data nel formato corretto.");
                return;
            }
            if(data.getTime() < (new Date()).getTime()) {
                notificaErrore(inputData.parent().parent(), "Impossibile prenotare per tale data. Inserire una data futura.");
                return;
            }

            if(inputPosti.val().length == 0 || isNaN(inputPosti.val()) || parseInt(inputPosti.val())  <= 0) {
                notificaErrore(inputPosti.parent(), "Inserire un numero di posti valido (maggiore o uguale a uno)");
                return;
            }
            formContainer.find("form").submit();
        }
    });

    $("input.posti").on("input",function(event){
        var totale = parseInt($(event.target).val()) * parseInt($(event.target).parent().parent().children(".prezzo-unitario").val());

        if(isNaN(totale)) totale=""; //Se il totale non è un mumero (ad esempio il valore del campo "quantita" è vuoto allora mostro un valore vuoto
        $(event.target).parent().parent().find("span.totale").text(totale);
    });

    $("input.data").asDatepicker();

});



