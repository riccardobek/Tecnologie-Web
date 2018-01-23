$(function() {
    //Scheda Prenotazioni
    stileCellaPagamento();

    assegnaVoto();

    //richiesta AJAX per la cancellazione di una prenotazione
    $(".button-holder > .btn-cancella").on("click", function () {
        var target = $(this).attr("data-target");
        var data = validaData($('#'+target).find(".giorno").text());
        var timeDiff = data - (new Date());
        var giorniDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        if(giorniDiff<2) {
            $.alert ({
                boxWidth: calcolaDimensioneDialog(),
                useBootstrap: false,
                type: 'red',
                title: 'Errore',
                content: "Non puoi cancellare la prenotazione con 2 giorni di anticipo."
            });
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
                        action: eliminaPrenotazione(target)
                    },
                    Annulla:  {}
                }
            });
        }
    });

    //Scheda Account
    $datiForm = salvaDatiForm();

    $("input[type=text], input[type=password]").attr('disabled','disabled');

    $(".mostra-modifica, .mostra-modifica-password").hide();


    $(".modifica").on("click", function () {
        $(".mostra-modifica").slideDown(200);
        $(":text, :password").not('#username').removeAttr('disabled');
        $("input[type=password]").val('');
    });

    $("#modifica-password")
    $("#annulla").on("click",function () {
        $(":text").attr('disabled','disabled');
        ripristinaDatiInizialiForm($datiForm);
        $(".mostra-modifica").slideUp(200, function () {
            $(this).hide();
        });
    });

    //Modifica dati account
    $("form").on("submit", function (e) {
        e.preventDefault();
        //prima di fare il submit controllo la validità dei dati modificati
        if(validaFormModifica()) {
            //Chiedo conferma della modifica
            console.log( $( "form" ).serialize() );
           // $.post($("form").attr("action"),)
        }
        else {
            e.preventDefault();
            e.stopPropagation();
        }
    });
});


/****** FUNZIONI *******/

function salvaDatiForm(){
    var inputs = $('input').not(":input[type=submit], :input[type=password]");
     var datiForm = {};
    $(inputs).each(function () {
        datiForm[$(this).attr("id")] = $(this).val();
    });
    return datiForm;
}

function ripristinaDatiInizialiForm(oggettoDatiForm ){
    var inputs = $('input').not(":input[type=submit], :input[type=password]");
    $("input[type=password]").val('');
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


function calcolaDimensioneDialog() {
    var larghezzaSchermo = $( window ).width();
    return (larghezzaSchermo <= 768) ? "80%" : "20em";
}

function assegnaVoto(){
    $(".accept").click(function(){
        var output=$(this).prev().find("option:selected").text();
        var idPrenotazione = $(this).attr("id");
        $.post("pannello_utente.php", {voto: output, codicePren: idPrenotazione, funzione: 1}, function (risposta) {
            risposta = JSON.parse(risposta);
            if(risposta.stato == 1) {
                $.alert({
                    boxWidth: calcolaDimensioneDialog(),
                    useBootstrap: false,
                    type: 'green',
                    title: 'Valutazione effettuata',
                    content: risposta.messaggio
                });
            }
            else{
                $.alert ({
                    boxWidth: calcolaDimensioneDialog(),
                    useBootstrap: false,
                    type: 'red',
                    title: 'Errore',
                    content: risposta.messaggio
                });
            }
        });
    });
}

//Ho usato javascript puro per esercizio
function validaFormModifica() {
    //valida i campi in comune con il form di registrazione
    var formValido = true;

    var email = document.getElementById("email");
    //espressione regolare che valida un'email a grandi linee. Presa da
    //https://stackoverflow.com/questions/46155/how-to-validate-email-address-in-javascript quarta risposta
    if (/[^\s@]+@[^\s@]+\.[^\s@]+/.test(email.value.trim()) == false) {
        notificaErrore(email.parentNode, "Inserire un'email valida");
        formValido = false;
    }

    var vecchiaPassword = document.getElementById("vecchia-password");
    var password = document.getElementById("password");
    var password2 = document.getElementById("password2");

    if(vecchiaPassword.value.trim().length != 0) {

        if (password.value.trim().length == 0) {
            notificaErrore(password.parentNode, "Inserire una password valida");
            formValido = false;
        }
        else if (password2.value.trim().length == 0) {
            notificaErrore(password2.parentNode, "Si prega di ripetere la password");
            formValido = false;
        }
        else if (password.value != password2.value) {
            notificaErrore(password2.parentNode, "Le password non combaciano");
            formValido = false;
        }
    }
    else {
        //pulisco i campi di modifica della password
        password.value = '';
        password2.value = '';
    }
    return formValido;
}

function rispostaEliminiazionePrenotazione(target) {
    var pari = $('#'+target).parent().nextAll(".pari");
    var dispari = $('#'+target).parent().nextAll(".dispari");

    $('#'+target).parent().slideUp('Slow', function(){
        $(this).remove();
    });
    dispari.removeClass("dispari").addClass("pari");
    pari.removeClass("pari").addClass("dispari");
}