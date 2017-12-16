/**
 * Funzione che valida il form di registrazione ed, eventualmente (con return false) ne interrompe il submit
 * @returns {boolean}
 */
function validaForm() {
    pulisciErrori();

    var formValido = true;

    var email = document.getElementById("email");
    //espressione regolare che valida un'email a grandi linee. Presa da
    //https://stackoverflow.com/questions/46155/how-to-validate-email-address-in-javascript quarta risposta
    if(/[^\s@]+@[^\s@]+\.[^\s@]+/.test(email.value.trim()) == false) {
        notificaErrore(email.parentNode, "Inserire un'email valida");
        formValido = false;
    }

    var username = document.getElementById("username");
    if(username.value.trim().length == 0) {
        notificaErrore(username.parentNode,"Inserire uno username valido");
        formValido = false;
    }

    var password = document.getElementById("password");
    var password2 = document.getElementById("password2");

    if(password.value.trim().length == 0) {
        notificaErrore(password.parentNode,"Inserire una password valida");
        formValido = false;
    }
    else if(password2.value.trim().length == 0) {
        notificaErrore(password2.parentNode,"Si prega di ripetere la password");
        formValido = false;
    }
    else if(password.value != password2.value) {
        notificaErrore(password2.parentNode,"Le password non combaciano");
        formValido = false;
    }

    return formValido;
}

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
function notificaErrore(targetNode,testo) {
    var span = document.createElement("span");

    var testoSpan = document.createTextNode(testo);
    span.appendChild(testoSpan);

    targetNode.className += " error";
    targetNode.appendChild(span);

    targetNode.getElementsByTagName("input")[0].addEventListener("focus",function() {
        pulisciErrore(targetNode);
    })
}

/**
 * Funzione che elimina tutti i messaggi di errore dai vari campi del form
 */
function pulisciErrori() {
    var elementi = document.getElementsByClassName("field-container");
    for(var i=0; i<elementi.length; i++) {
        pulisciErrore(elementi[i]);
    }
}

/**
 * Funzione che elimina il messaggio di errore (se esiste) dal div.field-container passato come parametro
 * @param targetElement il div.field-container dal quale rimuovere l'eventuale messaggio di errore
 */
function pulisciErrore(targetElement) {
    if(targetElement.className.match("error")) {
        //Se l'elemento targetNode ha un errore (quindi ha la classe error) la tolgo
        targetElement.className = targetElement.className.replace("error", "");

        //Prendo tutti i figli del div.field-container che sto esaminando e rimuovo lo span
        var figli = targetElement.childNodes;

        for(var i=0; i<figli.length; i++) {
            //Itero sui figli del div.field-container che sto esaminando alla disperata ricerca dello span da rimuovere
            if(figli[i].nodeName.toLowerCase() == "span") {
                //quando l'ho trovato lo rimuovo
                targetElement.removeChild(figli[i]);
            }
        }
    }
}