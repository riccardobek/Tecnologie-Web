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
            formContainer.find("input.data").trigger("change"); //carica il numero dei posti disponibili
            formContainer.find("input.data").filter(":first").focus();
        });
        $(event.target).next().show(); //Mostro il tasto "Chiudi"
        $(event.target).hide();

    });

    $("input.data").on("change",function() {
        getPostiDisponibili($(this).parent().parent().parent());
    });

    $("input.data, input.posti").on("focus",function() {
        if($(this).hasClass("error")) $(this).removeClass("error");
    });

    $(".form-prenotazione-container > form").on("submit",function (event) {

        var inputData = $(this).find("input.data");
        var inputPosti = $(this).find("input.posti");

        var divErrore = $(this).children(".alert.errore");
        pulisciErrori(divErrore,$(this));

        var data = validaData(inputData.val());
        if(!data) {
            notificaErrore(inputData, "Inserire una data nel formato corretto.",divErrore,$(this));
            return false;
        }
        if(data.getTime() < (new Date()).getTime()) {
            notificaErrore(inputData, "Impossibile prenotare per tale data. Inserire una data futura.",divErrore,$(this));
            event.preventDefault();
            event.stopPropagation();
            return false;
        }

        if(inputPosti.val().length == 0 || isNaN(inputPosti.val()) || parseInt(inputPosti.val())  <= 0) {
            notificaErrore(inputPosti, "Inserire un numero di posti valido (maggiore o uguale a uno)",divErrore,$(this));
            event.preventDefault();
            event.stopPropagation();
            return false;
        }

        var postiDisponibili = parseInt($(this).data("posti-disponibili"));
        if(!controllaConsistenzaPosti($(this),inputPosti)) {
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    });

    $("input.posti").on("input",function(event){
        controllaConsistenzaPosti($(this).parent().parent(),$(this));

        //Da qui in poi calcolo il totale
        var totale = parseInt($(event.target).val()) * parseInt($(event.target).parent().parent().children(".prezzo-unitario").val());

        if(isNaN(totale)) totale=""; //Se il totale non è un mumero (ad esempio il valore del campo "quantita" è vuoto) allora mostro un valore vuoto
        $(event.target).parent().parent().find("span.totale").text(totale);

    });

    $("input.data").asDatepicker();
    triggeraChangeInputData();
});

/**
 * Funzione che recupera (grazie ad una chiamata ajax) il numero di posti disponibili per la data selezionata
 * @param form il form relativo all'attività al quale ci si sta riferendo
 */
function getPostiDisponibili(form) {
    var divErrore = form.children("div.alert.errore");

    var inputPosti = form.find(".posti");
    var inputData = form.find(".data");

    $.post("attivita.php", {
        getPostiDisponibili: true,
        Data: inputData.val()
    },function(responseText) {

        if(responseText.length > 0) {
            var postiDisponibili = parseInt(responseText);
            form.data("posti-disponibili",postiDisponibili);
            controllaConsistenzaPosti(form,inputPosti);
        }
        else {
            form.data("posti-disponibili",-1);
        }
    });
}

/**
 * Funzione che controlla la consistenza dei posti selezionati. Nello specifico verifica se il numero di posti selezionati
 * è maggiore della disponibilità. Se si mostra un mesaggio di errore
 * @param form il form relativo all'attività di riferimento
 * @param inputPosti il campo di testo grazie al quale sono inseriti i posti
 * @returns {boolean} true se il numero di posti selezionati è accettabile, false altrimenti
 */
function controllaConsistenzaPosti(form,inputPosti) {
    var postiDisponibili = parseInt(form.data("posti-disponibili"));
    var divErrore = form.children(".alert.errore");

    if (parseInt(inputPosti.val()) > postiDisponibili) {
        notificaErrore(inputPosti,"Sono disponibili al massimo "+postiDisponibili+" posti.",divErrore,form);
        return false;
    }
    else {
        pulisciErrori(divErrore,form);
    }
    return true;
}

/**
 * Funzione che abilita l'evento "change" per gli input di tipo date che hanno un datepicker
 */
function triggeraChangeInputData() {
    $(".data").on("focus",function() {
        var valoreIniziale = $(this).val();
        $(this).on("blur", function() {
            if(valoreIniziale != $(this).val()) {
                $(this).trigger("change");
            }
            $(this).off("blur");
        });
    });
}
