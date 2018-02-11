$(function () {
    $(".form-prenotazione-container").hide();
    $(".solo-js").show();
    $("input[name='JSAbilitato']").val("1");

    $(".chiudi-form-prenotazione").on("click", function () {
        $(this).prev().prev().slideUp();
        $(this).prev().show(); //Mostro il tasto "Voglio prenotare"
        $(this).hide();
    });

    $(".attivita > button.btn.btn-primary").on("click",function(event) {
        var formContainer = $(event.target).parent().children(".form-prenotazione-container");
        formContainer.slideDown(function() {
            formContainer.find("input.data").filter(":first").focus();
        });
        $(event.target).next().show(); //Mostro il tasto "Chiudi"
        $(event.target).hide();

    });

    $(".form-prenotazione-container > form").on("submit",function (event) {

        var inputData = $(this).find("input.data");
        var inputPosti = $(this).find("input.posti");

        pulisciErrore(inputData.parent().parent());
        pulisciErrore(inputPosti.parent());

        var data = validaData(inputData.val());
        if(!data) {
            alert("Data nel formato non corretto");
            notificaErrore(inputData, "Inserire una data nel formato corretto.",$(this).children(".alert.errore"),$(this));
            return false;
        }
        if(data.getTime() < (new Date()).getTime()) {
            alert("Data non futura");
            notificaErrore(inputData, "Impossibile prenotare per tale data. Inserire una data futura.",$(this).children(".alert.errore"),$(this));
            event.preventDefault();
            event.stopPropagation();
            return false;
        }

        if(inputPosti.val().length == 0 || isNaN(inputPosti.val()) || parseInt(inputPosti.val())  <= 0) {
            alert("Numero di posti non valido");
            notificaErrore(inputPosti, "Inserire un numero di posti valido (maggiore o uguale a uno)",$(this).children(".alert.errore"),$(this));
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });

    $("input.posti").on("input",function(event){
        var totale = parseInt($(event.target).val()) * parseInt($(event.target).parent().parent().children(".prezzo-unitario").val());

        if(isNaN(totale)) totale=""; //Se il totale non è un mumero (ad esempio il valore del campo "quantita" è vuoto) allora mostro un valore vuoto
        $(event.target).parent().parent().find("span.totale").text(totale);
    });

    $("input.data").asDatepicker();

});



