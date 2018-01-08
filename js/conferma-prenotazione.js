$(function () {
    $("form").on("submit",function(event) {
        event.preventDefault();
        event.stopPropagation();

        var form = $(event.target);

        //Disabilito provvisoriamente il click del tasto per evitare più di un invio alla volta
        form.find("input[type='submit']").prop("disabled",true);

        $.post(form.attr("action"),form.serialize(),function(r) {
            //Riabilito il click del tasto
            form.find("input[type='submit']").prop("disabled",false);

            console.log("Risposta HTTP: "+r);

            var risposta = JSON.parse(r);
            if(risposta.stato === 1) {
                $("div#pulsanti-container").remove();
                alert("Successo cazo!");
            }
            else {
                alert("Errore cazo!");
            }
        })
    });
});