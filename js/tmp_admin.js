$(function() {
    $("#imposta-data").asDatepicker();


    $("#impostazione-giorno form").on("submint", function (e) {
        e.preventDefault();
        e.stopPropagation();

        if($("#nPosti").val() == "50"){
            notificaErrore($("#nPosti"),"Non puoi selezionare un numero posti uguale al valore di <span lang='en'>default</span>", $("#impostazione-giorno .alert.errore"), $("#impostazione-giorno form"));
        }
        else {
            var data = $('#imposta-data').val();
            var nposti = $("#nPosti").val();
            //controllo lato client se la disponibilità per quella data è già stata modificata, guardo nella tabella
            if($("#"+data).length){
                notificaErrore($("#nPosti"),"Disponibilità per la data selezionata già modificata", $("#impostazione-giorno .alert.errore"), $("#impostazione-giorno form"));
            }
            var form = $("#impostazione-giorno form").serializeArray();
            form.push({name:"Disponibilita", value:"1"});
            $.post("pannello_admin.php", form, function(risposta){
               try {
                   risposta = parse.JSON(rispota);
                   if(risposta.stato == "1") {

                       generaAlert('green','Succeso',risposta.messaggio );
                       $("#tabella-giorni").append("<tr id='"+data+"'><td>"+data+"</td><td>"+nposti+"</td><td><button data-target='"+data+"'class='btn-cancella' title='Elimina'>X</button></td></tr>");

                   }
               }
               catch(e) {
                   generaAlertErroreGenerico();
               }
            });
        }

    });

}
