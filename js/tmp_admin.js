$(function() {
    $("#imposta-data").asDatepicker();


    $("#impostazione-giorno form").on("submit", function (e) {
        e.preventDefault();
        e.stopPropagation();


        var divAlert = $("#impostazione-giorno .alert.errore");
        var formAlert = $("#impostazione-giorno form");

        pulisciErrori(divAlert,formAlert);

        if($("#nPosti").val().trim().length == 0){
            notificaErrore($("#Posti"),"Numero posti obbligatorio",divAlert,formAlert);
        }
        else if($("#nPosti").val() == "50"){
            notificaErrore($("#nPosti"),"Non puoi selezionare un numero posti uguale al valore di <span lang='en'>default</span>", divAlert, formAlert);
        }
        else {
            var data = $("#imposta-data").val();

            var nposti = $("#nPosti").val();
            //controllo lato client se la disponibilità per quella data è già stata modificata, guardo nella tabella
            if($("#"+data.replace(/\//g,'')).length){
                notificaErrore($("#nPosti"),"Disponibilità per la data selezionata già modificata", divAlert, formAlert);
            }
            var form = $("#impostazione-giorno form").serializeArray();
            form.push({name:"Disponibilita", value:"1"});
            $.post("pannello_admin.php", form, function(risposta) {
               try {
                   risposta = JSON.parse(risposta);
                   console.log("parse OK");
                   if(risposta.stato == "1") {
                       generaAlert('green','Succeso',risposta.messaggio );
                       $("#tabella-giorni").append("<tr id='"+data+"'><td>"+data+"</td><td>"+nposti+"</td><td><button data-target='"+data+"'class='btn-cancella' title='Elimina'>X</button></td></tr>");

                   }
                   else {
                       notificaErrore($("#imposta-data"),risposta.messaggio,divAlert, formAlert);
                   }
               }
               catch(e) {
                   generaAlertErroreGenerico();
               }
            });
        }

    });

});
