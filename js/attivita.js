$(function () {

    $(".attivita > button").on("click",function(event) {

        var formContainer = $(event.target).parent().children(".form-prenotazione-container");

        if(!$(event.target).data("espanso")) {
            formContainer.slideToggle();
            $(event.target).data("espanso",true);
        }
        else {
            var inputData = formContainer.find("input.data");
            var inputPosti = formContainer.find("input.posti");

            pulisciErrore(inputData.parent()[0].parentNode);
            pulisciErrore(inputPosti[0].parentNode);

            var data = validaData(inputData.val());
            if(!data) {
                notificaErrore(inputData.parent()[0].parentNode, "Inserire una data nel formato corretto.");
                return;
            }
            if(data.getTime() < (new Date()).getTime()) {
                notificaErrore(inputData.parent()[0].parentNode, "Impossibile prenotare per tale data. Inserire una data futura.");
                return;
            }

            if(inputPosti.val().length == 0 || isNaN(inputPosti.val()) || parseInt(inputPosti.val())  <= 0) {
                notificaErrore(inputPosti[0].parentNode, "Inserire un numero di posti valido (maggiore o uguale a uno)");
                return;
            }

            formContainer.find("form").submit();

            /*
            $.post("php/do_prenotazione.php", {
                attivita: form.find("input[name='attivita']").val(),
                data: formContainer.find(".data").val(),
                posti: formContainer.find(".posti").val()
            }, function (r) {
                risposta = JSON.parse(r);
                if(risposta.stato === 1) {
                    alert("Prenotazione inserita con successo");
                } else {
                    alert("Errore nell'inserimento della prenotazione: \n\n"+risposta.messaggio)
                }
                console.log("Risposta dalla pagina di inserimento della prenotazione: "+r);
            });*/
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



