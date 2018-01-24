$(function () {
    $("form").on("submit",function(event) {
        event.preventDefault();
        event.stopPropagation();

        //Nascondo eventuali precedenti avvisi mostrati
        $(".alert").hide();

        var form = $(event.target);

        //Disabilito provvisoriamente il click del tasto per evitare più di un invio alla volta
        form.find("input[type='submit']").prop("disabled",true);


        $.post(form.attr("action"),form.serialize(),function(r) {
                        console.log("Risposta HTTP: "+r);

            var risposta = JSON.parse(r);
            if(risposta.stato === 1) {
                $("div#pulsanti-container").remove();
                $("#pulsanti-fine-prenotazione").show();
                $(".alert.successo > a").attr("href","pdf_prenotazione.php?codice="+risposta.CodicePrenotazione);
                $(".alert.successo").show();
            }
            else {
                $(".alert.errore").text(risposta.messaggio).show();
                //Riabilito il click del tasto
                form.find("input[type='submit']").prop("disabled",false);
            }
        })
    });
});