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
 * Funzione che mostra eventuali errori in un form
 *
 * @param targetNode è il campo che ha generato l'errore (input o textarea)
 * @param testo il testo dell'errore
 * @param divAlert il div.alert.errore che conterrà gli errori da mostrare
 * @param formErr il form che ha generato l'errore
 */
function notificaErrore(targetNode, testo, divAlert, formErr) {
    pulisciErrori(divAlert,formErr);

    divAlert.append("<p>"+testo+"</p>");
    divAlert.show();
    targetNode.addClass("error");
    formErr.find(".error").first().focus();
}

/**
 * Funzione che elimina tutti i messaggi di errore dai vari campi del form
 */
function pulisciErrori(divAlert, formErr) {
    divAlert.find("p").not(".intestazione-alert").remove();
    divAlert.hide();
    formErr.find("input").removeClass("error");
}

/**
 * Funzione che prende una stringa e se essa non rappresenta una data allora la converte in oggetto Date
 * @param d la data da val
 * @returns {*}
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
    if(date.getDate() == giorno && date.getMonth() == mese && date.getFullYear() == anno)
        return date;
    return false;
}

function validaFormUtente(validazionePassword) {
    pulisciErrori($(".alert.errore"),$("form"));

    var formValido = true;

    var anagrafica = $("#nome, #cognome");
    anagrafica.each(function() {
        if($(this).val().trim().length == 0) {
            notificaErrore($(this),"Campo "+$(this).attr("name")+" obbligatorio",$(".alert.errore"),$("form"));
            formValido = false;
        }

    });

    var email = $("#email");
    //espressione regolare che valida un'email a grandi linee. Presa da
    //https://stackoverflow.com/questions/46155/how-to-validate-email-address-in-javascript quarta risposta
    if (/[^\s@]+@[^\s@]+\.[^\s@]+/.test(email.val().trim()) == false) {
        notificaErrore(email,"Inserire un'<span lang='en'> email </span> valida",$(".alert.errore"),$("form"));
        formValido = false;
    }

    var username = $("#username");
    if (username.val().trim().length == 0) {
        notificaErrore(username,"Inserire uno <span lang='en'> username </span> valido",$(".alert.errore"),$("form"));
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
        notificaErrore(password,"Inserire una <span lang='en'> password </span> valida",$(".alert.errore"),$("form"));
        passwordValide = false;
    }
    else if (password2.val().trim().length == 0) {
        notificaErrore(password2,"Si prega di ripetere la <span lang='en'> password </span>",$(".alert.errore"),$("form"));
        passwordValide = false;
    }
    else if (password.val() != password2.val()) {
        notificaErrore(password2,"Le <span lang='en'> password </span> non combaciano",$(".alert.errore"),$("form"));
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

function generaAlertErroreGenerico() {
    generaAlert('red',"Errore","Si è verificato un errore generico. Riprova più tardi.");
}