$("document").ready(function() {
    $("#footer > ul > li").on("click",function(event) {
        var url = $(event.target).children("a").attr("href");
        if(url != undefined && url != null && location.href != url)
            location.href = url;
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