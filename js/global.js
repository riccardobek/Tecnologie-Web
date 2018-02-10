$("document").ready(function() {
    $("#menu-mobile > ul > li").on("click",function(event) {
        var url = $(event.target).children("a").attr("href");
        if(url != undefined && url != null && location.href != url)
            location.href = url;
    });
    $("body").on("touchstart", function(){ /* ontouchstart fixa il comportamento degli eventi touch su Safari per iOS */

    });

});



/**
 * Funzione che notifica un errore in un campo del form.
 *
 * La funzione si divide in due parti:
 * 1- Aggiunge la classe "error" all'elemento "targetNode" (ovvero al div.field-container passato come parametro)
 * 2- Crea e aggiunge a tale div lo span contenente il testo dell'errore
 *
 * @param targetNode è il div.field-container a cui si vuole aggiungere l'errore
 * @param testo è il testo dell'errore
 */
function notificaErrore(targetNode, testo) {
    $(".alert.errore").append("<p>"+testo+"</p>");
    $(".alert.errore").show();
    targetNode.addClass("error");
    $("form .error").first().focus();

    /*var span = $("<span role='alert'>"+testo+"</span>");
    span.appendTo(targetNode);



    targetNode.children("input,textarea").on("focus",function() {
        pulisciErrore(targetNode);
    });*/
}

/**
 * Funzione che elimina tutti i messaggi di errore dai vari campi del form
 */
function pulisciErrori() {
    $(".alert.errore p").not(".intestazione-alert").remove();
    $(".alert.errore").hide();
    $("input").removeClass("error");
}

/**
 * Funzione che elimina il messaggio di errore (se esiste) dal div.field-container passato come parametro
 * @param targetElement il div.field-container dal quale rimuovere l'eventuale messaggio di errore
 */
function pulisciErrore(targetElement) {
    /*if(targetElement.hasClass("error")) {
        //Se l'elemento targetNode ha un errore (quindi ha la classe error) la tolgo
        targetElement.removeClass("error");

        //Prendo tutti i figli del div.field-container che sto esaminando e rimuovo lo span
        targetElement.children().each(function() {
            //Itero sui figli del div.field-container che sto esaminando alla disperata ricerca dello span da rimuovere
            if($(this).is("span")) {
                //quando l'ho trovato lo rimuovo
                $(this).remove();
            }
        });
    }*/
}

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
    if(date.getDate() == giorno && date.getMonth() == mese && date.getFullYear() == anno)
        return date;
    return false;
}

function validaFormUtente(validazionePassword) {
    pulisciErrori();

    var formValido = true;

    var anagrafica = $("#nome, #cognome");
    anagrafica.each(function() {
        if($(this).val().trim().length == 0) {
            notificaErrore($(this),"Campo "+$(this).attr("name")+" obbligatorio");
            formValido = false;
        }

    });

    var email = $("#email");
    //espressione regolare che valida un'email a grandi linee. Presa da
    //https://stackoverflow.com/questions/46155/how-to-validate-email-address-in-javascript quarta risposta
    if (/[^\s@]+@[^\s@]+\.[^\s@]+/.test(email.val().trim()) == false) {
        notificaErrore(email,"Inserire un'<span lang='en'> email </span> valida");
        formValido = false;
    }

    var username = $("#username");
    if (username.val().trim().length == 0) {
        notificaErrore(username,"Inserire uno <span lang='en'> username </span> valido");
        formValido = false;
    }

    if(validazionePassword) {

        var password = $("#password");
        var password2 = $("#password2");

        if (!validaPassword(password, password2))
            formValido = false;
    }

    return formValido;
}

function validaPassword(password, password2) {

    passwordValide = true;

    if (password.val().trim().length == 0) {
        notificaErrore(password,"Inserire una <span lang='en'> password </span> valida");
        passwordValide = false;
    }
    else if (password2.val().trim().length == 0) {
        notificaErrore(password2,"Si prega di ripetere la <span lang='en'> password </span>");
        passwordValide = false;
    }
    else if (password.val() != password2.val()) {
        notificaErrore(password2,"Le <span lang='en'> password </span> non combaciano");
        passwordValide = false;
    }

    return passwordValide;
}

function calcolaDimensioneDialog() {
    var larghezzaSchermo = $( window ).width();
    return (larghezzaSchermo <= 768) ? "80%" : "20em";
}

function generaAlert(colore,titolo,testo) {
    $.alert({
        boxWidth: calcolaDimensioneDialog(),
        useBootstrap: false,
        title: titolo,
        type:colore,
        content: testo
    });
}